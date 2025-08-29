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