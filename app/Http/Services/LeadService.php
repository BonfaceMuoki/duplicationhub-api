<?php

namespace App\Http\Services;

use App\Models\Lead;
use App\Models\Page;
use App\Models\PageInvite;
use App\Models\User;
use App\Enums\LeadStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LeadService
{
    /**
     * Submit a new lead from a capture page
     */
    public function submitLead(array $data): array
    {
        try {
            DB::beginTransaction();

            // Find the page and referrer invite
            $page = Page::findOrFail($data['page_id']);
            
            // Find or create the referrer invite
            $referrerInvite = PageInvite::where('page_id', $page->id)
                ->where('handle', $data['ref'])
                ->first();
            
            // If no referrer invite exists, create a default one
            if (!$referrerInvite) {
                $referrerInvite = PageInvite::create([
                    'page_id' => $page->id,
                    'user_id' => $page->user_id, // Use the page owner as the referrer
                    'handle' => $data['ref'],
                    'clicks' => 0,
                    'leads_count' => 0,
                    'is_active' => true,
                ]);
                
                // Initialize closure table for the new referrer invite
                DB::table('page_invite_closure')->insert([
                    'ancestor_invite_id' => $referrerInvite->id,
                    'descendant_invite_id' => $referrerInvite->id,
                    'depth' => 0,
                ]);
            }

            // Check if user already exists
            $user = User::where('email', $data['email'])->first();
            
            if (!$user) {
                // Create new user account for the lead
                $user = User::create([
                    'first_name' => $data['first_name'],
                    'middle_name' => $data['middle_name'] ?? null,
                    'last_name' => $data['last_name'],
                    'date_of_birth' => $data['date_of_birth'] ?? null,
                    'gender' => $data['gender'] ?? null,
                    'email' => $data['email'],
                    'phone_number' => $data['whatsapp_number'] ?? null,
                    'password' => Hash::make(Str::random(16)), // Temporary password
                    'account_status' => 'active',
                ]);
            }

            // Generate unique handle for this submitter
            $submitterHandle = $this->generateUniqueHandle($page->id, $data['first_name']);
            
            // Create submitter invite
            $submitterInvite = PageInvite::create([
                'page_id' => $page->id,
                'user_id' => $user->id,
                'clicks' => 0,
                'leads_count' => 0,
                'is_active' => true,
            ]);

            // Get the auto-generated handle
            $submitterHandle = $submitterInvite->handle;

            // Create the lead
            $lead = Lead::create([
                'page_id' => $page->id,
                'referrer_invite_id' => $referrerInvite->id,
                'submitter_invite_id' => $submitterInvite->id,
                'submitter_user_id' => $user->id,
                'name' => trim($data['first_name'] . ' ' . ($data['middle_name'] ?? '') . ' ' . $data['last_name']),
                'email' => $data['email'],
                'whatsapp_number' => $data['whatsapp_number'] ?? null,
                'utm_source' => $data['utm_source'] ?? null,
                'utm_medium' => $data['utm_medium'] ?? null,
                'utm_campaign' => $data['utm_campaign'] ?? null,
                'ip_address' => $data['ip_address'] ?? null,
                'user_agent' => $data['user_agent'] ?? null,
                'status' => LeadStatus::NEW,
            ]);

            // Update closure table
            $this->updateClosureTable($referrerInvite->id, $submitterInvite->id);

            // Update counts
            $referrerInvite->increment('leads_count');
            $page->increment('views');

            DB::commit();

            // Generate personalized link for the submitter
            $myLink = generatePageUrl($page->slug, $submitterHandle);
            
            // Generate redirect URL for referrer
            $redirectTo = $this->generateRedirectUrl($page, $referrerInvite->handle);

            return [
                'success' => true,
                'lead' => $lead,
                'user' => $user,
                'my_link' => $myLink,
                'redirect_to' => $redirectTo,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get leads for a specific user (their own leads + referred leads)
     */
    public function getUserLeads(User $user, int $perPage = 20): array
    {
        $leads = Lead::with(['page', 'referrerInvite.user', 'submitterInvite.user','leadShares'])
            ->where(function($query) use ($user) {
                $query->where('submitter_user_id', $user->id)
                      ->orWhereHas('referrerInvite', function($q) use ($user) {
                          $q->where('user_id', $user->id);
                      });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Calculate user stats
        $stats = $this->calculateUserStats($user);

        return [
            'leads' => $leads,
            'stats' => $stats,
        ];
    }

    /**
     * Get all leads with filtering and pagination
     */
    public function getAllLeads(array $filters = [], int $perPage = 50): array
    {
        $query = Lead::with(['page', 'referrerInvite.user', 'submitterInvite.user', 'submitterUser','leadShares']);

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['page_id'])) {
            $query->where('page_id', $filters['page_id']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('whatsapp_number', 'like', "%{$search}%");
            });
        }

        $leads = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Calculate overall stats
        $stats = $this->calculateOverallStats($filters);

        return [
            'leads' => $leads,
            'stats' => $stats,
        ];
    }

    /**
     * Update lead status
     */
    public function updateLeadStatus(Lead $lead, string $status, ?string $notes = null): Lead
    {
        $lead->update([
            'status' => $status,
            'notes' => $notes,
        ]);

        return $lead->fresh();
    }

    /**
     * Update lead status and return with all relationships
     */
    public function updateStatus(Lead $lead, string $status, ?string $notes = null, array $additionalData = []): Lead
    {
        $lead->update([
            'status' => $status,
            'notes' => $notes,
        ]);

        // Handle joining_link_shared status
        if ($status === 'joining_link_shared') {
            $this->handleJoiningLinkShared($lead, $additionalData);
        }

        // Return the lead with all relationships loaded
        return $lead->fresh([
            'page',
            'referrerInvite.user',
            'submitterInvite.user',
            'submitterUser',
            'leadShares'
        ]);
    }

    /**
     * Handle joining_link_shared status by creating or updating PageInviteLinkShare
     */
    private function handleJoiningLinkShared(Lead $lead, array $additionalData): void
    {
        // Check if PageInviteLinkShare already exists for this lead
        $existingShare = \App\Models\PageInviteLinkShare::where('page_id', $lead->page_id)
            ->where('page_invite_id', $lead->referrer_invite_id)
            ->first();

        $shareData = [
            'page_id' => $lead->page_id,
            'page_invite_id' => $lead->referrer_invite_id,
            'user_page_link' => $additionalData['landing_page_url'] ?? null,
            'personal_message' => $additionalData['personal_message'] ?? null,
            'registration_status' => 'pending',
            'notes' => $additionalData['notes'] ?? null,
            'metadata' => [
                'lead_id' => $lead->id,
                'lead_name' => $lead->name,
                'lead_email' => $lead->email,
                'shared_at' => now()->toISOString(),
                'landing_page_url' => $additionalData['landing_page_url'] ?? null,
                'personal_message' => $additionalData['personal_message'] ?? null,
            ]
        ];

        if ($existingShare) {
            // Update existing record
            $existingShare->update($shareData);
        } else {
            // Create new record
            \App\Models\PageInviteLinkShare::create($shareData);
        }
    }

    /**
     * Get lead analytics for a specific page
     */
    public function getPageLeadAnalytics(Page $page, array $filters = []): array
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

        // Calculate conversion rates by invite
        $inviteStats = $page->invites()
            ->withCount(['referredLeads as total_leads'])
            ->withSum('referredLeads', 'clicks')
            ->get()
            ->map(function($invite) {
                $invite->conversion_rate = $invite->clicks > 0 
                    ? round(($invite->total_leads / $invite->clicks) * 100, 2) 
                    : 0;
                return $invite;
            })
            ->sortByDesc('total_leads');

        // Daily lead count for charts
        $dailyLeads = $leads->groupBy(function($lead) {
            return $lead->created_at->format('Y-m-d');
        })->map->count();

        return [
            'total_leads' => $leads->count(),
            'leads_by_status' => $leads->groupBy('status')->map->count(),
            'invite_performance' => $inviteStats,
            'daily_leads' => $dailyLeads,
            'conversion_rate' => $page->views > 0 ? round(($leads->count() / $page->views) * 100, 2) : 0,
        ];
    }



    /**
     * Update closure table with new relationship
     */
    private function updateClosureTable(int $ancestorId, int $descendantId): void
    {
        // Insert self-reference for descendant
        DB::table('page_invite_closure')->insert([
            'ancestor_invite_id' => $descendantId,
            'descendant_invite_id' => $descendantId,
            'depth' => 0,
        ]);

        // Insert relationship to ancestor
        DB::table('page_invite_closure')->insert([
            'ancestor_invite_id' => $ancestorId,
            'descendant_invite_id' => $descendantId,
            'depth' => 1,
        ]);

        // Insert all ancestor relationships
        $ancestors = DB::table('page_invite_closure')
            ->where('descendant_invite_id', $ancestorId)
            ->where('depth', '>', 0)
            ->get();

        foreach ($ancestors as $ancestor) {
            DB::table('page_invite_closure')->insert([
                'ancestor_invite_id' => $ancestor->ancestor_invite_id,
                'descendant_invite_id' => $descendantId,
                'depth' => $ancestor->depth + 1,
            ]);
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

    /**
     * Calculate user-specific lead statistics
     */
    private function calculateUserStats(User $user): array
    {
        $submittedLeads = Lead::where('submitter_user_id', $user->id)->count();
        $referredLeads = Lead::whereHas('referrerInvite', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->count();

        $totalInvites = PageInvite::where('user_id', $user->id)->count();
        $totalClicks = PageInvite::where('user_id', $user->id)->sum('clicks');

        return [
            'submitted_leads' => $submittedLeads,
            'referred_leads' => $referredLeads,
            'total_leads' => $submittedLeads + $referredLeads,
            'total_invites' => $totalInvites,
            'total_clicks' => $totalClicks,
            'conversion_rate' => $totalClicks > 0 ? round((($submittedLeads + $referredLeads) / $totalClicks) * 100, 2) : 0,
        ];
    }

    /**
     * Calculate overall lead statistics
     */
    private function calculateOverallStats(array $filters = []): array
    {
        $query = Lead::query();

        // Apply filters
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $totalLeads = $query->count();
        $leadsByStatus = $query->get()->groupBy('status')->map->count();

        return [
            'total_leads' => $totalLeads,
            'leads_by_status' => $leadsByStatus,
            'new_leads' => $leadsByStatus['new'] ?? 0,
            'contacted_leads' => $leadsByStatus['contacted'] ?? 0,
            'joined_leads' => $leadsByStatus['joined'] ?? 0,
        ];
    }
} 