<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Page;
use App\Models\PageInvite;
use App\Models\User;
use App\Models\PageInviteClosure;
use App\Enums\LeadStatus;
use App\Http\Services\MessagingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Http\Services\LeadService;

class LeadController extends Controller
{
    protected MessagingService $messagingService;

    public function __construct(MessagingService $messagingService)
    {
        $this->messagingService = $messagingService;
    }

    /**
     * Submit a new lead from a capture page
     */
    public function submit(Request $request)
    {
        $request->validate([
            'page_id' => 'required|integer|exists:pages,id',
            'ref' => 'required|string',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:Male,Female,Other,Prefer not to say',
            'email' => 'required|email|max:255',
            'whatsapp_number' => 'nullable|string|max:20',
            'utm_source' => 'nullable|string|max:100',
            'utm_medium' => 'nullable|string|max:100',
            'utm_campaign' => 'nullable|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            // Find the page and referrer invite
            $page = Page::findOrFail($request->page_id);
            
            // Check if a lead with this email already exists for this page
            $existingLead = Lead::where('page_id', $page->id)
                ->where('email', $request->email)
                ->first();

            
                
            if ($existingLead) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already submitted your interest for this page. Please contact the page owner if you need to update your information.',
                    'data' => [
                        'lead_id' => $existingLead->id,
                        'submitted_at' => $existingLead->created_at,
                    ]
                ], 409); // 409 Conflict status code
            }
            
            // Find or create the referrer invite
            $referrerInvite = PageInvite::where('page_id', $page->id)
                ->where('handle', ($request->ref=='direct'|| $request->ref==null)?'duplication':$request->ref)
                ->orderBy('id', 'desc')
                ->first();
               
           
            // If no referrer invite exists, create a default one
            if (!$referrerInvite) {
                $referrerInvite = PageInvite::create([
                    'page_id' => $page->id,
                    'user_id' => $page->user_id, // Use the page owner as the referrer
                    'handle' => $request->ref,
                    'clicks' => 0,
                    'leads_count' => 0,
                    'is_active' => true,
                ]);
                
                // Initialize closure table for the new referrer invite
           
                PageInviteClosure::updateOrCreate([
                    'ancestor_invite_id' => $referrerInvite->id,
                    'descendant_invite_id' => $referrerInvite->id,
                    'depth' => 0,
                ]);
            }

            // Check if user already exists
            $user = User::where('email', $request->email)->first();
            $isNewUser = false;
            
            if (!$user) {
                // Create new user account for the lead
                $user = User::create([
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'last_name' => $request->last_name,
                    'date_of_birth' => $request->date_of_birth,
                    'gender' => $request->gender,
                    'email' => $request->email,
                    'phone_number' => $request->whatsapp_number,
                    'password' => Hash::make(Str::random(16)), // Temporary password
                    'account_status' => 'active',
                ]);
                $user->assignRole('Normal User');
                $isNewUser = true;
            }

            // Find existing submitter invite or create a new one
            $submitterInvite = PageInvite::where('page_id', $page->id)
                ->where('user_id', $user->id)
                ->first();

            if (!$submitterInvite) {
                // Create new submitter invite (handle will be auto-generated)
                $submitterInvite = PageInvite::create([
                    'page_id' => $page->id,
                    'user_id' => $user->id,
                    'clicks' => 0,
                    'leads_count' => 0,
                    'is_active' => true,
                ]);
            }

            // Get the handle (either existing or newly generated)
            $submitterHandle = $submitterInvite->handle;

            // Create the lead
            $lead = Lead::create([
                'page_id' => $page->id,
                'referrer_invite_id' => $referrerInvite->id,
                'submitter_invite_id' => $submitterInvite->id,
                'submitter_user_id' => $user->id,
                'name' => trim($request->first_name . ' ' . ($request->middle_name ?? '') . ' ' . $request->last_name),
                'email' => $request->email,
                'whatsapp_number' => $request->whatsapp_number,
                'utm_source' => $request->utm_source,
                'utm_medium' => $request->utm_medium,
                'utm_campaign' => $request->utm_campaign,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => LeadStatus::NEW,
            ]);

            // Update closure table
            $this->updateClosureTable($referrerInvite->id, $submitterInvite->id);

            // Update counts
            $referrerInvite->increment('leads_count');
            $page->increment('views');

            DB::commit();

            // Send welcome messages (email and WhatsApp)
            $messagingResults = $this->messagingService->sendWelcomeMessages($lead, $page, $user, $isNewUser);

            // Generate personalized link for the submitter
            $myLink = generatePageUrl($page->id, $submitterHandle);
            
            // Generate redirect URL for referrer
            $redirectTo = $this->generateRedirectUrl($page, $referrerInvite->handle);

            return response()->json([
                'success' => true,
                'message' => 'Lead submitted successfully!',
                'data' => [
                    'my_link' => $myLink,
                    'redirect_to' => $redirectTo,
                    'lead_id' => $lead->id,
                    'user_id' => $user->id,
                    'messaging' => $messagingResults,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Send follow-up message to a lead
     */
    public function sendFollowUpMessage(Request $request, Lead $lead)
    {
        $request->validate([
            'message_type' => 'required|string|in:reminder,update,promotion,custom',
            'custom_message' => 'nullable|string',
            'promotion_text' => 'nullable|string',
        ]);

        // Check if user has permission to send messages to this lead
        if (auth()->user()->id !== $lead->page->user_id && !auth()->user()->hasRole('super admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to send messages to this lead'
            ], 403);
        }

        $customData = [];
        if ($request->message_type === 'custom' && $request->custom_message) {
            $customData['message'] = $request->custom_message;
        } elseif ($request->message_type === 'promotion' && $request->promotion_text) {
            $customData['promotion_text'] = $request->promotion_text;
        }

        $results = $this->messagingService->sendFollowUpMessage(
            $lead, 
            $request->message_type, 
            $customData
        );

        return response()->json([
            'success' => true,
            'message' => 'Follow-up message sent successfully',
            'data' => $results
        ]);
    }

    /**
     * Send bulk messages to multiple leads
     */
    public function sendBulkMessages(Request $request)
    {
        $request->validate([
            'lead_ids' => 'required|array|min:1',
            'lead_ids.*' => 'integer|exists:leads,id',
            'message_type' => 'required|string|in:reminder,update,promotion,custom',
            'custom_message' => 'nullable|string',
            'promotion_text' => 'nullable|string',
        ]);

        // Check if user has permission to send bulk messages
        if (!auth()->user()->hasRole('super admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Only admins can send bulk messages'
            ], 403);
        }

        $customData = [];
        if ($request->message_type === 'custom' && $request->custom_message) {
            $customData['message'] = $request->custom_message;
        } elseif ($request->message_type === 'promotion' && $request->promotion_text) {
            $customData['promotion_text'] = $request->promotion_text;
        }

        $results = $this->messagingService->sendBulkMessages(
            $request->lead_ids,
            $request->message_type,
            $customData
        );

        return response()->json([
            'success' => true,
            'message' => 'Bulk messages sent successfully',
            'data' => $results
        ]);
    }

    /**
     * Get messaging statistics for a page
     */
    public function getMessagingStats(Request $request, Page $page)
    {
        // Check if user has access to this page
        if (auth()->user()->id !== $page->user_id && !auth()->user()->hasRole('super admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view this page messaging stats'
            ], 403);
        }

        $filters = $request->only(['date_from', 'date_to']);
        $stats = $this->messagingService->getMessagingStats($page, $filters);

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get leads for a specific user (their own leads + referred leads)
     */
    public function myLeads(Request $request)
    {
        $user = auth()->user();
                
        $leads = Lead::with(['page', 'referrerInvite.user', 'submitterInvite.user'])
            ->where(function($query) use ($user) {
                $query->where('submitter_user_id', $user->id)
                      ->orWhereHas('referrerInvite', function($q) use ($user) {
                          $q->where('user_id', $user->id);
                      });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $leads
        ]);
    }

    /**
     * Get all leads (admin only)
     */
    public function allLeads(Request $request)
    {
        $leads = Lead::with(['page', 'referrerInvite.user', 'submitterInvite.user', 'submitterUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $leads
        ]);
    }

    /**
     * Update lead status
     */
    public function updateStatus(Request $request, Lead $lead)
    {
        $request->validate([
            'status' => 'required|in:new,contacted,joined,joining_link_shared,advertisement_link_shared',
            'notes' => 'nullable|string',
            'landing_page_url' => 'nullable|url|required_if:status,joining_link_shared',
            'personal_message' => 'nullable|string|required_if:status,joining_link_shared',
            'full_external_invite_url' => 'nullable|string|url',
            'external_invite_code' => 'nullable|string|unique:page_invites,external_invite_code',
        ]);

        $additionalData = [];
        
        // Collect additional data for joining_link_shared status
        if ($request->status === 'joining_link_shared') {
            $additionalData = [
                'landing_page_url' => $request->landing_page_url,
                'personal_message' => $request->personal_message,
                'notes' => $request->notes,
            ];
        }

        // Add external invite data if provided
        if ($request->full_external_invite_url || $request->external_invite_code) {
            $additionalData['full_external_invite_url'] = $request->full_external_invite_url;
            $additionalData['external_invite_code'] = $request->external_invite_code;
        }

        $lead = app(LeadService::class)->updateStatus(
            $lead,
            $request->status,
            $request->notes,
            $additionalData
        );

        return response()->json([
            'success' => true,
            'message' => 'Lead status updated successfully',
            'data' => $lead
        ]);
    }



    /**
     * Update closure table with new relationship
     */
    private function updateClosureTable(int $ancestorId, int $descendantId): void
    {
        // Insert self-reference for descendant
        // DB::table('page_invite_closure')->insert([
        //     'ancestor_invite_id' => $descendantId,
        //     'descendant_invite_id' => $descendantId,
        //     'depth' => 0,
        // ]);

        // Insert relationship to ancestor
   
        PageInviteClosure::updateOrCreate(
            [
                'ancestor_invite_id' => $ancestorId,
                'descendant_invite_id' => $descendantId,
            ],
            [
                'depth' => 1,
            ]
        );
        

        // Insert all ancestor relationships
        $ancestors = DB::table('page_invite_closure')
            ->where('descendant_invite_id', $ancestorId)
            ->where('depth', '>', 0)
            ->get();

            foreach ($ancestors as $ancestor) {
                PageInviteClosure::updateOrCreate(
                    [
                        'ancestor_invite_id' => $ancestor->ancestor_invite_id,
                        'descendant_invite_id' => $descendantId,
                    ],
                    [
                        'depth' => $ancestor->depth + 1,
                    ]
                );
            }
            
    }

    /**
     * Generate redirect URL based on page platform and referrer handle
     */
    private function generateRedirectUrl(Page $page, string $referrerHandle): string
    {
        if ($page->platform_base_url) {
            return rtrim($page->platform_base_url, '/') . '/signup/' . $referrerHandle;
        }
        
        return $page->default_join_url ?? '#';
    }
} 