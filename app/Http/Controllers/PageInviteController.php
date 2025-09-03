<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageInvite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PageInviteController extends Controller
{
    /**
     * Track click on invite link
     */
    public function trackClick(Request $request)
    {
        $request->validate([
            'page_id' => 'required|integer|exists:pages,id',
            'ref' => 'required|string',
        ]);

        $page = Page::findOrFail($request->page_id);
        
        // Find or create the invite
        $invite = PageInvite::where('page_id', $page->id)
            ->where('handle', $request->ref)
            ->first();
        
        // If no invite exists, create a default one
        if (!$invite) {
            $invite = PageInvite::create([
                'page_id' => $page->id,
                'user_id' => $page->user_id, // Use the page owner as the referrer
                'handle' => $request->ref,
                'clicks' => 0,
                'leads_count' => 0,
                'is_active' => true,
            ]);
            
            // Initialize closure table for the new invite
            DB::table('page_invite_closure')->insert([
                'ancestor_invite_id' => $invite->id,
                'descendant_invite_id' => $invite->id,
                'depth' => 0,
            ]);
        }

        // Increment click count
        $invite->increment('clicks');

        return response()->json([
            'success' => true,
            'message' => 'Click tracked successfully'
        ]);
    }

    /**
     * Get invite performance for a user
     */
    public function myInvites(Request $request)
    {
        $user = auth()->user();
        
        $invites = PageInvite::with(['page'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate totals
        $totalClicks = $invites->sum('clicks');
        $totalLeads = $invites->sum('leads_count');
        $conversionRate = $totalClicks > 0 ? round(($totalLeads / $totalClicks) * 100, 2) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'invites' => $invites,
                'stats' => [
                    'total_clicks' => $totalClicks,
                    'total_leads' => $totalLeads,
                    'conversion_rate' => $conversionRate,
                ]
            ]
        ]);
    }

    /**
     * Get all invites for admin
     */
    public function allInvites(Request $request)
    {
        $invites = PageInvite::with(['page', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $invites
        ]);
    }

    /**
     * Create new invite for a page
     */
    public function createInvite(Request $request)
    {
        $request->validate([
            'page_id' => 'required|exists:pages,id',
            'handle' => 'required|string|max:50',
            'join_url' => 'nullable|url|max:500',
        ]);

        $page = Page::findOrFail($request->page_id);
        
        // Check if user has permission to create invites for this page
        if (auth()->user()->id !== $page->user_id && !auth()->user()->hasRole('super admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to create invites for this page'
            ], 403);
        }

        // Check if handle already exists for this page
        if (PageInvite::where('page_id', $request->page_id)
            ->where('handle', $request->handle)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Handle already exists for this page'
            ], 422);
        }

        $invite = PageInvite::create([
            'page_id' => $request->page_id,
            'user_id' => auth()->user()->id,
            'handle' => $request->handle,
            'join_url' => $request->join_url,
            'clicks' => 0,
            'leads_count' => 0,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Invite created successfully',
            'data' => $invite
        ], 201);
    }

    /**
     * Update invite
     */
    public function updateInvite(Request $request, PageInvite $invite)
    {
        $request->validate([
            'handle' => 'sometimes|string|max:50',
            'join_url' => 'sometimes|nullable|url|max:500',
            'is_active' => 'sometimes|boolean',
        ]);

        // Check if user has permission to update this invite
        if (auth()->user()->id !== $invite->user_id && !auth()->user()->hasRole('super admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this invite'
            ], 403);
        }

        // Check if new handle conflicts with existing ones
        if (isset($request->handle) && $request->handle !== $invite->handle) {
            if (PageInvite::where('page_id', $invite->page_id)
                ->where('handle', $request->handle)
                ->where('id', '!=', $invite->id)
                ->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Handle already exists for this page'
                ], 422);
            }
        }

        $invite->update($request->only(['handle', 'join_url', 'is_active']));

        return response()->json([
            'success' => true,
            'message' => 'Invite updated successfully',
            'data' => $invite
        ]);
    }

    /**
     * Delete invite
     */
    public function deleteInvite(PageInvite $invite)
    {
        // Check if user has permission to delete this invite
        if (auth()->user()->id !== $invite->user_id && !auth()->user()->hasRole('super admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this invite'
            ], 403);
        }

        $invite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Invite deleted successfully'
        ]);
    }

    /**
     * Get referral tree for a specific invite
     */
    public function getReferralTree(Request $request, PageInvite $invite)
    {
        $descendants = $invite->descendants()
            ->with(['user', 'page'])
            ->where('depth', '>', 0)
            ->orderBy('depth')
            ->get();

        $ancestors = $invite->ancestors()
            ->with(['user', 'page'])
            ->where('depth', '>', 0)
            ->orderBy('depth')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'invite' => $invite->load(['user', 'page']),
                'upline' => $ancestors,
                'downline' => $descendants,
            ]
        ]);
    }

    /**
     * Get complete invites tree structure for a page
     */
    public function getInvitesTree(Request $request)
    {
        $request->validate([
            'page_id' => 'required|integer|exists:pages,id',
            'root_invite_id' => 'nullable|integer|exists:page_invites,id',
            'user_id' => 'nullable|integer|exists:users,id',
            'include_inactive' => 'nullable|boolean',
        ]);

        $pageId = $request->page_id;
        
        $rootInviteId = $request->root_invite_id;
        
        $userId = $request->user_id;
        $includeInactive = $request->boolean('include_inactive', false);

        // If no root invite specified, find the invite that has closure table entries (actual root)
        if (!$rootInviteId) {
            // Get the page to find the owner
            $page = \App\Models\Page::findOrFail($pageId);
            
            // First, try to find the page owner's invite (most likely to be the root)
            $rootInvite = PageInvite::where('page_id', $pageId)
                ->where('user_id', $page->user_id)
                ->first();

            // If no owner invite found, try to find an invite that has no ancestors (true root)
            if (!$rootInvite) {
                $rootInvite = PageInvite::where('page_id', $pageId)
                    ->whereDoesntHave('ancestors', function ($query) {
                        $query->where('depth', '>', 0);
                    })
                    ->first();
            }

            // If no true root found, look for an invite that has closure table entries with depth 0
            if (!$rootInvite) {
                $rootInvite = PageInvite::where('page_id', $pageId)
                    ->whereHas('ancestors', function ($query) {
                        $query->where('depth', 0);
                    })
                    ->first();
            }

            // If still no proper root found, fall back to first invite for the page
            if (!$rootInvite) {
                $rootInvite = PageInvite::where('page_id', $pageId)
                    ->orderBy('created_at', 'asc')
                    ->first();
            }

            if (!$rootInvite) {
                return response()->json([
                    'success' => false,
                    'message' => 'No invites found for this page'
                ], 404);
            }
            $rootInviteId = $rootInvite->id;
        }

        // Get the root invite with all its data including inviter details
        $rootInvite = PageInvite::with(['user', 'page'])
            ->findOrFail($rootInviteId);

        // Add inviter information to root invite
        $rootInviteData = $this->formatInviteWithInviterDetails($rootInvite);

        // Build the tree structure recursively with enhanced user details
        $tree = $this->buildInviteTree($rootInviteId, $pageId, $userId, $includeInactive);

        // Get tree statistics
        $stats = $this->getTreeStatistics($rootInviteId, $pageId, $userId, $includeInactive);

        // Get page information (reuse the page we already fetched)
        $page->load('user');

        return response()->json([
            'success' => true,
            'data' => [
                'page' => [
                    'id' => $page->id,
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'owner' => [
                        'id' => $page->user->id,
                        'name' => $page->user->full_name,
                        'email' => $page->user->email,
                    ]
                ],
                'root_invite' => $rootInviteData,
                'tree' => $tree,
                'statistics' => $stats,
                'filters_applied' => [
                    'page_id' => $pageId,
                    'root_invite_id' => $rootInviteId,
                    'user_id' => $userId,
                    'include_inactive' => $includeInactive,
                ]
            ]
        ]);
    }

    /**
     * Build the invite tree structure recursively
     */
    private function buildInviteTree($inviteId, $pageId, $userId = null, $includeInactive = false, $maxDepth = 10)
    {
        // Get direct descendants (depth > 0, but not self-reference)
        $query = PageInvite::whereHas('ancestors', function ($query) use ($inviteId) {
            $query->where('ancestor_invite_id', $inviteId)
                  ->where('depth', '>', 0)
                  ->where('descendant_invite_id', '!=', $inviteId);
        })
        ->with(['user', 'page']);

        // Apply user filter if specified
        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Apply active filter if specified
        if (!$includeInactive) {
            $query->where('is_active', true);
        }

        $directDescendants = $query->get();

        $children = [];

        foreach ($directDescendants as $descendant) {
            $child = $this->formatInviteWithInviterDetails($descendant);
            $child['children'] = $this->buildInviteTree($descendant->id, $pageId, $userId, $includeInactive, $maxDepth - 1);
            $children[] = $child;
        }

        return $children;
    }

    /**
     * Get tree statistics
     */
    private function getTreeStatistics($rootInviteId, $pageId, $userId = null, $includeInactive = false)
    {
        // Get all descendants of the root invite
        $query = PageInvite::whereHas('ancestors', function ($query) use ($rootInviteId) {
            $query->where('ancestor_invite_id', $rootInviteId)
                  ->where('depth', '>', 0);
        });

        // Apply user filter if specified
        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Apply active filter if specified
        if (!$includeInactive) {
            $query->where('is_active', true);
        }

        $allDescendants = $query->get();

        // Calculate statistics
        $totalInvites = $allDescendants->count() + 1; // +1 for root
        $totalClicks = $allDescendants->sum('clicks');
        $totalLeads = $allDescendants->sum('leads_count');
        $activeInvites = $allDescendants->where('is_active', true)->count() + 1; // +1 for root

        // Get depth distribution
        $depthDistribution = [];
        for ($i = 1; $i <= 10; $i++) {
            $depthQuery = PageInvite::whereHas('ancestors', function ($query) use ($rootInviteId, $i) {
                $query->where('ancestor_invite_id', $rootInviteId)
                      ->where('depth', $i);
            });

            // Apply same filters for depth distribution
            if ($userId) {
                $depthQuery->where('user_id', $userId);
            }
            if (!$includeInactive) {
                $depthQuery->where('is_active', true);
            }

            $count = $depthQuery->count();
            
            if ($count > 0) {
                $depthDistribution[$i] = $count;
            }
        }

        // Get unique users in the tree
        $uniqueUsers = $allDescendants->pluck('user_id')->unique()->count() + 1; // +1 for root

        return [
            'total_invites' => $totalInvites,
            'total_clicks' => $totalClicks,
            'total_leads' => $totalLeads,
            'active_invites' => $activeInvites,
            'unique_users' => $uniqueUsers,
            'conversion_rate' => $totalClicks > 0 ? round(($totalLeads / $totalClicks) * 100, 2) : 0,
            'depth_distribution' => $depthDistribution,
            'max_depth' => count($depthDistribution) > 0 ? max(array_keys($depthDistribution)) : 0,
        ];
    }

    /**
     * Format invite with detailed inviter information
     */
    private function formatInviteWithInviterDetails($invite)
    {
        // Get the direct inviter (parent at depth 1)
        $inviter = PageInvite::whereHas('descendants', function ($query) use ($invite) {
            $query->where('descendant_invite_id', $invite->id)
                  ->where('depth', 1);
        })->with('user')->first();

        return [
            'id' => $invite->id,
            'handle' => $invite->handle,
            'join_url' => $invite->join_url,
            'clicks' => $invite->clicks,
            'leads_count' => $invite->leads_count,
            'is_active' => $invite->is_active,
            'created_at' => $invite->created_at,
            'updated_at' => $invite->updated_at,
            'user' => [
                'id' => $invite->user->id,
                'name' => $invite->user->full_name,
                'first_name' => $invite->user->first_name,
                'middle_name' => $invite->user->middle_name,
                'last_name' => $invite->user->last_name,
                'email' => $invite->user->email,
                'phone_number' => $invite->user->phone_number,
                'date_of_birth' => $invite->user->date_of_birth,
                'gender' => $invite->user->gender,
                'account_status' => $invite->user->account_status,
            ],
            'page' => [
                'id' => $invite->page->id,
                'title' => $invite->page->title,
                'slug' => $invite->page->slug,
                'is_public' => $invite->page->is_public ?? false,
            ],
            'inviter' => $inviter ? [
                'id' => $inviter->id,
                'handle' => $inviter->handle,
                'user' => [
                    'id' => $inviter->user->id,
                    'name' => $inviter->user->full_name,
                    'email' => $inviter->user->email,
                ]
            ] : null,
        ];
    }

    /**
     * Create a new page invite link share
     */
    public function createLinkShare(Request $request)
    {
        $request->validate([
            'page_id' => 'required|integer|exists:pages,id',
            'page_invite_id' => 'required|integer|exists:page_invites,id',
            'user_page_link' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'metadata' => 'nullable|array'
        ]);

        // Verify that the page invite belongs to the specified page
        $pageInvite = PageInvite::where('id', $request->page_invite_id)
            ->where('page_id', $request->page_id)
            ->first();

        if (!$pageInvite) {
            return response()->json([
                'success' => false,
                'message' => 'Page invite not found for the specified page'
            ], 404);
        }

        $linkShare = \App\Models\PageInviteLinkShare::create([
            'page_id' => $request->page_id,
            'page_invite_id' => $request->page_invite_id,
            'user_page_link' => $request->user_page_link,
            'registration_status' => 'pending',
            'notes' => $request->notes,
            'metadata' => $request->metadata
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Link share created successfully',
            'data' => $linkShare->load(['page', 'pageInvite'])
        ], 201);
    }

    /**
     * Get a specific page invite link share
     */
    public function getLinkShare(\App\Models\PageInviteLinkShare $share)
    {
        return response()->json([
            'success' => true,
            'data' => $share->load(['page', 'pageInvite'])
        ]);
    }

    /**
     * Update the registration status of a link share
     */
    public function updateLinkShareStatus(Request $request, \App\Models\PageInviteLinkShare $share)
    {
        $request->validate([
            'registration_status' => 'required|in:pending,registered,completed,failed',
            'notes' => 'nullable|string|max:1000'
        ]);

        $share->update([
            'registration_status' => $request->registration_status,
            'notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Link share status updated successfully',
            'data' => $share->load(['page', 'pageInvite'])
        ]);
    }
} 