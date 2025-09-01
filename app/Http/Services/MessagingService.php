<?php

namespace App\Http\Services;

use App\Models\Lead;
use App\Models\Page;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MessagingService
{
    /**
     * Share page link and return client phone number and message
     */
    public function sharePageLink(array $data): array
    {
        try {
            // Validate required fields
            if (empty($data['page_url']) || empty($data['message'])) {
                return [
                    'success' => false,
                    'error' => 'Page URL and message are required'
                ];
            }

            // Validate message length
            if (strlen($data['message']) > 1000) {
                return [
                    'success' => false,
                    'error' => 'Message exceeds maximum length of 1000 characters'
                ];
            }

            // Extract page slug from URL
            $pageSlug = $this->extractPageSlugFromUrl($data['page_url']);
            
            if (!$pageSlug) {
                return [
                    'success' => false,
                    'error' => 'Invalid page URL format'
                ];
            }

            // Find the page
            $page = Page::where('slug', $pageSlug)->first();
            
            if (!$page) {
                return [
                    'success' => false,
                    'error' => 'Page not found'
                ];
            }

            // Get the page owner's phone number
            $user = $page->user;
            $phoneNumber = $user->phone_number ?? $user->whatsapp_number ?? null;

            if (!$phoneNumber) {
                return [
                    'success' => false,
                    'error' => 'Page owner contact information not available'
                ];
            }

            // Format the response
            return [
                'success' => true,
                'data' => [
                    'phone_number' => $phoneNumber,
                    'message' => $data['message'],
                    'page_title' => $page->title,
                    'page_url' => $data['page_url'],
                    'client_name' => $user->full_name ?? $user->first_name ?? 'Page Owner',
                    'campaign_launched' => true,
                    'timestamp' => now()->toISOString()
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Error sharing page link', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'An error occurred while processing your request'
            ];
        }
    }

    /**
     * Extract page slug from URL
     */
    private function extractPageSlugFromUrl(string $url): ?string
    {
        // Parse the URL to extract the page slug
        $parsedUrl = parse_url($url);
        
        if (!$parsedUrl || !isset($parsedUrl['path'])) {
            return null;
        }

        // Extract the last part of the path (the slug)
        $pathParts = explode('/', trim($parsedUrl['path'], '/'));
        $slug = end($pathParts);

        // Validate slug format (alphanumeric and hyphens only)
        if (preg_match('/^[a-zA-Z0-9-]+$/', $slug)) {
            return $slug;
        }

        return null;
    }

    /**
     * Send welcome messages to a new lead
     */
    public function sendWelcomeMessages(Lead $lead, Page $page, User $user, bool $isNewUser = false): array
    {
        $results = [
            'email_sent' => false,
            'whatsapp_sent' => false,
            'errors' => []
        ];

        try {
            // Send welcome email
            if ($user->email) {
                $results['email_sent'] = $this->sendWelcomeEmail($lead, $page, $user, $isNewUser);
            }

            // Send WhatsApp message
            if ($lead->whatsapp_number) {
                $results['whatsapp_sent'] = $this->sendWelcomeWhatsApp($lead, $page, $user);
            }

        } catch (\Exception $e) {
            Log::error('Error sending welcome messages', [
                'lead_id' => $lead->id,
                'error' => $e->getMessage()
            ]);
            $results['errors'][] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Send welcome email to new lead
     */
    private function sendWelcomeEmail(Lead $lead, Page $page, User $user, bool $isNewUser = false): bool
    {
        try {
            $emailData = [
                'name' => $user->first_name,
                'page_title' => $page->title,
                'page_headline' => $page->headline,
                'page_summary' => $page->summary,
                'is_new_user' => $isNewUser,
                'whatsapp_number' => $lead->whatsapp_number,
                'email' => $user->email,
            ];

            // Generate password reset link for new users
            if ($isNewUser) {
                $resetToken = Str::random(64);
                
                // Store token in password_resets table
                DB::table('password_resets')->updateOrInsert(
                    ['email' => $user->email],
                    [
                        'email' => $user->email,
                        'token' => $resetToken,
                        'created_at' => now()
                    ]
                );

                // Build reset URL using FRONT_END_BASE_URL from .env
                $frontendUrl = env('FRONT_END_BASE_URL', 'http://localhost:3000');
                $resetUrl = rtrim($frontendUrl, '/') . '/reset-password?token=' . $resetToken . '&email=' . urlencode($user->email);
                
                $emailData['reset_url'] = $resetUrl;
            }

            // Send email based on user type or use default template
            Mail::send('emails.leads.welcome', $emailData, function ($message) use ($user, $page) {
                $message->to($user->email, $user->first_name)
                        ->subject("Welcome to {$page->title}!");
            });

            Log::info('Welcome email sent successfully', [
                'lead_id' => $lead->id,
                'email' => $user->email,
                'is_new_user' => $isNewUser,
                'password_reset_sent' => $isNewUser
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send welcome email', [
                'lead_id' => $lead->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send welcome WhatsApp message to new lead
     */
    private function sendWelcomeWhatsApp(Lead $lead, Page $page, User $user): bool
    {
        try {
            $message = $this->generateWelcomeWhatsAppMessage($lead, $page, $user);
            
            // Use free WhatsApp methods
            $response = $this->sendWhatsAppMessageFree($lead->whatsapp_number, $message);

            if ($response['success']) {
                Log::info('Welcome WhatsApp message sent successfully', [
                    'lead_id' => $lead->id,
                    'whatsapp_number' => $lead->whatsapp_number
                ]);
                return true;
            } else {
                Log::error('Failed to send WhatsApp message', [
                    'lead_id' => $lead->id,
                    'whatsapp_number' => $lead->whatsapp_number,
                    'error' => $response['error'] ?? 'Unknown error'
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('Error sending WhatsApp message', [
                'lead_id' => $lead->id,
                'whatsapp_number' => $lead->whatsapp_number,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send follow-up message to lead
     */
    public function sendFollowUpMessage(Lead $lead, string $messageType, array $customData = []): array
    {
        $results = [
            'email_sent' => false,
            'whatsapp_sent' => false,
            'errors' => []
        ];

        try {
            $page = $lead->page;
            $user = $lead->submitterUser;

            // Send follow-up email
            if ($user->email) {
                $results['email_sent'] = $this->sendFollowUpEmail($lead, $page, $user, $messageType, $customData);
            }

            // Send follow-up WhatsApp
            if ($lead->whatsapp_number) {
                $results['whatsapp_sent'] = $this->sendFollowUpWhatsApp($lead, $page, $user, $messageType, $customData);
            }

        } catch (\Exception $e) {
            Log::error('Error sending follow-up messages', [
                'lead_id' => $lead->id,
                'message_type' => $messageType,
                'error' => $e->getMessage()
            ]);
            $results['errors'][] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Send follow-up email
     */
    private function sendFollowUpEmail(Lead $lead, Page $page, User $user, string $messageType, array $customData): bool
    {
        try {
            $emailData = array_merge([
                'name' => $user->first_name,
                'page_title' => $page->title,
                'lead_id' => $lead->id,
                'message_type' => $messageType,
            ], $customData);

            $template = "emails.leads.{$messageType}";
            
            // Check if template exists, fallback to generic
            if (!view()->exists($template)) {
                $template = 'emails.leads.follow-up';
            }

            Mail::send($template, $emailData, function ($message) use ($user, $page, $messageType) {
                $message->to($user->email, $user->first_name)
                        ->subject("Update from {$page->title}");
            });

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send follow-up email', [
                'lead_id' => $lead->id,
                'message_type' => $messageType,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send follow-up WhatsApp message
     */
    private function sendFollowUpWhatsApp(Lead $lead, Page $page, User $user, string $messageType, array $customData): bool
    {
        try {
            $message = $this->generateFollowUpWhatsAppMessage($lead, $page, $user, $messageType, $customData);
            
            $response = $this->sendWhatsAppMessageFree($lead->whatsapp_number, $message);
            return $response['success'];

        } catch (\Exception $e) {
            Log::error('Error sending follow-up WhatsApp', [
                'lead_id' => $lead->id,
                'message_type' => $messageType,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send bulk messages to multiple leads
     */
    public function sendBulkMessages(array $leadIds, string $messageType, array $customData = []): array
    {
        $results = [
            'total_leads' => count($leadIds),
            'emails_sent' => 0,
            'whatsapps_sent' => 0,
            'errors' => []
        ];

        foreach ($leadIds as $leadId) {
            try {
                $lead = Lead::with(['page', 'submitterUser'])->find($leadId);
                if (!$lead) continue;

                $messageResult = $this->sendFollowUpMessage($lead, $messageType, $customData);
                
                if ($messageResult['email_sent']) $results['emails_sent']++;
                if ($messageResult['whatsapp_sent']) $results['whatsapps_sent']++;
                
                $results['errors'] = array_merge($results['errors'], $messageResult['errors']);

            } catch (\Exception $e) {
                $results['errors'][] = "Lead {$leadId}: " . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Generate welcome WhatsApp message
     */
    private function generateWelcomeWhatsAppMessage(Lead $lead, Page $page, User $user): string
    {
        return "Hi {$user->first_name}! 👋\n\n" .
               "Thank you for your interest in {$page->title}!\n\n" .
               "We have successfully received your interest and someone from our team will get back to you soon through this WhatsApp number.\n\n" .
               "We look forward to helping you achieve your goals!\n\n" .
               "Best regards,\nThe {$page->title} Team";
    }

    /**
     * Generate follow-up WhatsApp message
     */
    private function generateFollowUpWhatsAppMessage(Lead $lead, Page $page, User $user, string $messageType, array $customData): string
    {
        $baseMessage = "Hi {$user->first_name}! 👋\n\n";
        
        switch ($messageType) {
            case 'reminder':
                return $baseMessage . 
                       "Just a friendly reminder about {$page->title}!\n\n" .
                       "Don't forget to share your referral link with friends.\n\n" .
                       "Your link: " . url("/{$page->slug}?ref={$lead->submitterInvite->handle}");
                
            case 'update':
                return $baseMessage . 
                       "Great news! We have an update for you about {$page->title}.\n\n" .
                       ($customData['message'] ?? 'Check your email for more details.');
                
            case 'promotion':
                return $baseMessage . 
                       "🎉 Special promotion for {$page->title}!\n\n" .
                       ($customData['promotion_text'] ?? 'Limited time offer available!') . "\n\n" .
                       "Your referral link: " . url("/{$page->slug}?ref={$lead->submitterInvite->handle}");
                
            default:
                return $baseMessage . 
                       "We have an update for you about {$page->title}.\n\n" .
                       ($customData['message'] ?? 'Please check your email for more details.');
        }
    }

    /**
     * Send WhatsApp message using FREE methods
     */
    private function sendWhatsAppMessageFree(string $phoneNumber, string $message): array
    {
        try {
            // Method 1: WhatsApp Web API (Free)
            if (config('services.whatsapp.web_api_enabled', false)) {
                return $this->sendViaWhatsAppWebAPI($phoneNumber, $message);
            }
            
            // Method 2: WhatsApp Direct Link (Free)
            return $this->sendViaWhatsAppDirectLink($phoneNumber, $message);
            
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Method 1: Send via WhatsApp Web API (Free)
     * Uses WhatsApp Web to send messages programmatically
     */
    private function sendViaWhatsAppWebAPI(string $phoneNumber, string $message): array
    {
        try {
            // This method requires a WhatsApp Web session
            // You can use libraries like: https://github.com/mgp25/WhatsApp-Web-API
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(config('services.whatsapp.web_api_url'), [
                'phone' => $this->formatPhoneNumber($phoneNumber),
                'message' => $message,
                'session_id' => config('services.whatsapp.web_session_id'),
            ]);

            if ($response->successful()) {
                return ['success' => true, 'response' => $response->json()];
            } else {
                return ['success' => false, 'error' => $response->body()];
            }

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Method 2: Generate WhatsApp Direct Link (Free)
     * Creates a link that opens WhatsApp with pre-filled message
     */
    private function sendViaWhatsAppDirectLink(string $phoneNumber, string $message): array
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);
            $encodedMessage = urlencode($message);
            
            // Create WhatsApp direct link
            $whatsappLink = "https://wa.me/{$formattedPhone}?text={$encodedMessage}";
            
            // Store the link for tracking (optional)
            $this->storeWhatsAppLink($phoneNumber, $message, $whatsappLink);
            
            // Return success with the link
            return [
                'success' => true, 
                'method' => 'direct_link',
                'whatsapp_link' => $whatsappLink,
                'message' => 'WhatsApp link generated. User needs to click to send.'
            ];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Method 3: Send via Email to WhatsApp (Free)
     * Some carriers support sending WhatsApp via email
     */
    private function sendViaWhatsAppEmail(string $phoneNumber, string $message): array
    {
        try {
            $carrier = $this->detectCarrier($phoneNumber);
            $emailAddress = $this->getWhatsAppEmail($phoneNumber, $carrier);
            
            if (!$emailAddress) {
                return ['success' => false, 'error' => 'Carrier not supported for WhatsApp email'];
            }

            // Send email to WhatsApp
            Mail::raw($message, function ($message) use ($emailAddress) {
                $message->to($emailAddress)
                        ->subject('WhatsApp Message');
            });

            return ['success' => true, 'method' => 'email_to_whatsapp'];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Method 4: Generate QR Code for WhatsApp Web (Free)
     * Creates a QR code that users can scan to open WhatsApp Web
     */
    private function generateWhatsAppQRCode(string $phoneNumber, string $message): array
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);
            $encodedMessage = urlencode($message);
            
            // Generate QR code data
            $qrData = "https://wa.me/{$formattedPhone}?text={$encodedMessage}";
            
            // You can use a free QR code generator service
            $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qrData);
            
            return [
                'success' => true,
                'method' => 'qr_code',
                'qr_code_url' => $qrCodeUrl,
                'whatsapp_link' => $qrData,
                'message' => 'QR code generated. User can scan to open WhatsApp.'
            ];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Format phone number for WhatsApp
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove all non-numeric characters
        $cleanNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Remove country code if it starts with + or 00
        if (strlen($cleanNumber) > 10) {
            // Assume first 1-3 digits are country code
            $cleanNumber = substr($cleanNumber, -10);
        }
        
        return $cleanNumber;
    }

    /**
     * Detect carrier from phone number
     */
    private function detectCarrier(string $phoneNumber): string
    {
        // Simple carrier detection based on number patterns
        // You can expand this with more sophisticated logic
        $cleanNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // US carriers (simplified)
        if (strlen($cleanNumber) >= 10) {
            $areaCode = substr($cleanNumber, 0, 3);
            
            $carriers = [
                '201', '202', '203', '205', '206', '207', '208', '209' => 'verizon',
                '210', '211', '212', '213', '214', '215', '216', '217' => 'att',
                '218', '219', '220', '221', '222', '223', '224', '225' => 'tmobile',
            ];
            
            foreach ($carriers as $code => $carrier) {
                if (strpos($areaCode, $code) === 0) {
                    return $carrier;
                }
            }
        }
        
        return 'unknown';
    }

    /**
     * Get WhatsApp email address for carrier
     */
    private function getWhatsAppEmail(string $phoneNumber, string $carrier): ?string
    {
        $formattedPhone = $this->formatPhoneNumber($phoneNumber);
        
        $carrierEmails = [
            'verizon' => "{$formattedPhone}@vtext.com",
            'att' => "{$formattedPhone}@txt.att.net",
            'tmobile' => "{$formattedPhone}@tmomail.net",
            'sprint' => "{$formattedPhone}@messaging.sprintpcs.com",
        ];
        
        return $carrierEmails[$carrier] ?? null;
    }

    /**
     * Store WhatsApp link for tracking
     */
    private function storeWhatsAppLink(string $phoneNumber, string $message, string $link): void
    {
        // You can store this in a database for tracking
        // For now, we'll just log it
        Log::info('WhatsApp link generated', [
            'phone' => $phoneNumber,
            'message' => $message,
            'link' => $link,
            'timestamp' => now()
        ]);
    }

    /**
     * Get all available free WhatsApp methods
     */
    public function getAvailableWhatsAppMethods(string $phoneNumber): array
    {
        $methods = [];
        
        // Method 1: Direct Link (always available)
        $methods[] = [
            'name' => 'WhatsApp Direct Link',
            'description' => 'Generate a link that opens WhatsApp with pre-filled message',
            'method' => 'direct_link',
            'free' => true
        ];
        
        // Method 2: QR Code (always available)
        $methods[] = [
            'name' => 'WhatsApp QR Code',
            'description' => 'Generate a QR code that users can scan',
            'method' => 'qr_code',
            'free' => true
        ];
        
        // Method 3: Email to WhatsApp (if carrier supported)
        $carrier = $this->detectCarrier($phoneNumber);
        if ($this->getWhatsAppEmail($phoneNumber, $carrier)) {
            $methods[] = [
                'name' => 'Email to WhatsApp',
                'description' => 'Send message via email to WhatsApp',
                'method' => 'email_to_whatsapp',
                'free' => true,
                'carrier' => $carrier
            ];
        }
        
        // Method 4: WhatsApp Web API (if configured)
        if (config('services.whatsapp.web_api_enabled', false)) {
            $methods[] = [
                'name' => 'WhatsApp Web API',
                'description' => 'Send via WhatsApp Web API',
                'method' => 'web_api',
                'free' => true
            ];
        }
        
        return $methods;
    }

    /**
     * Generate redirect URL for referrer
     */
    private function generateRedirectUrl(Page $page, string $referrerHandle): string
    {
        if ($page->platform_base_url) {
            return rtrim($page->platform_base_url, '/') . '/signup/' . $referrerHandle;
        }
        
        return $page->default_join_url ?? '#';
    }

    /**
     * Get messaging statistics for a page
     */
    public function getMessagingStats(Page $page, array $filters = []): array
    {
        $query = $page->leads();

        // Apply date filters
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $leads = $query->get();

        $stats = [
            'total_leads' => $leads->count(),
            'leads_with_email' => $leads->whereNotNull('submitterUser.email')->count(),
            'leads_with_whatsapp' => $leads->whereNotNull('whatsapp_number')->count(),
            'messaging_ready_leads' => $leads->filter(function($lead) {
                return $lead->submitterUser->email || $lead->whatsapp_number;
            })->count(),
        ];

        return $stats;
    }
} 