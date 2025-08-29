<?php

namespace App\Http\Controllers;

use App\Http\Services\ReferralService;
use App\Models\PageInvite;
use App\Models\Page;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReferralController extends Controller
{
    protected ReferralService $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * Get complete referral tree for a specific invite
     */
    public function getReferralTree(PageInvite $invite): JsonResponse
    {
        try {
            $referralTree = $this->referralService->getReferralTree($invite);
            
            return response()->json([
                'success' => true,
                'data' => $referralTree
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get referral tree: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get direct referrals only (depth = 1)
     */
    public function getDirectReferrals(PageInvite $invite): JsonResponse
    {
        try {
            $directReferrals = $this->referralService->getDirectReferrals($invite);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'invite' => $invite->load(['user', 'page']),
                    'direct_referrals' => $directReferrals,
                    'count' => $directReferrals->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get direct referrals: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get complete upline chain (who referred this user)
     */
    public function getUpline(PageInvite $invite): JsonResponse
    {
        try {
            $upline = $this->referralService->getUpline($invite);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'invite' => $invite->load(['user', 'page']),
                    'upline' => $upline,
                    'count' => $upline->count(),
                    'depth' => $upline->max('pivot.depth') ?? 0
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get upline: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get referral statistics by level
     */
    public function getReferralStats(PageInvite $invite): JsonResponse
    {
        try {
            $stats = $this->referralService->getReferralStats($invite);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'invite' => $invite->load(['user', 'page']),
                    'stats' => $stats
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get referral stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get page referral analytics
     */
    public function getPageReferralAnalytics(Page $page): JsonResponse
    {
        try {
            $analytics = $this->referralService->getPageReferralAnalytics($page);
            
            return response()->json([
                'success' => true,
                'data' => $analytics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get page referral analytics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user referral performance
     */
    public function getUserReferralPerformance(User $user): JsonResponse
    {
        try {
            $performance = $this->referralService->getUserReferralPerformance($user->id);
            
            return response()->json([
                'success' => true,
                'data' => $performance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get user referral performance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user-specific dashboard summary (for normal users)
     */
    public function getMyDashboardSummary(): JsonResponse
    {
        try {
            $userId = auth()->id();
            $user = auth()->user();
            $userRole = $user->roles->isNotEmpty() ? $user->roles[0]->name : 'normal';
            
            $summary = $this->referralService->getUserDashboardSummary($userId, $userRole);
            
            return response()->json([
                'success' => true,
                'data' => $summary
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get your dashboard summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comprehensive dashboard summary
     */
    public function getDashboardSummary(): JsonResponse
    {
        try {
            $summary = $this->referralService->getDashboardSummary();
            
            return response()->json([
                'success' => true,
                'data' => $summary
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get dashboard summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed level 1 invitees with lead status and referral count
     */
    public function getLevelOneInviteesDetails(PageInvite $invite, Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);
            
            // Validate pagination parameters
            $perPage = max(1, min(100, (int) $perPage)); // Limit per_page between 1-100
            $page = max(1, (int) $page);
            
            $paginator = $this->referralService->getLevelOneInviteesDetails($invite, $perPage, $page);
            
            return response()->json([
                'success' => true,
                'data' => $paginator
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get level 1 invitees details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get referral network visualization data
     */
    public function getReferralNetwork(PageInvite $invite): JsonResponse
    {
        try {
            $network = $this->referralService->getReferralNetwork($invite);
            
            return response()->json([
                'success' => true,
                'data' => $network
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get referral network: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update referral relationship (admin only)
     */
    public function updateReferralRelationship(Request $request, PageInvite $invite): JsonResponse
    {
        $request->validate([
            'new_ancestor_id' => 'required|integer|exists:page_invites,id'
        ]);

        try {
            $success = $this->referralService->updateReferralRelationship(
                $invite->id, 
                $request->new_ancestor_id
            );
            
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Referral relationship updated successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update referral relationship'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update referral relationship: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get my referral performance (authenticated user)
     */
    public function getMyReferralPerformance()
    {
        try {
            $user = auth()->user();
            
            $performance = $this->referralService->getUserReferralPerformance($user->id);
            
            return response()->json([
                'success' => true,
                'data' => $performance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get your referral performance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get referral leaderboard for a page
     */
    public function getReferralLeaderboard(Page $page, Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            $analytics = $this->referralService->getPageReferralAnalytics($page);
            
            $leaderboard = array_slice($analytics['top_referrers'], 0, $limit);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'page' => $page->load(['user']),
                    'leaderboard' => $leaderboard,
                    'total_participants' => $analytics['total_invites']
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get referral leaderboard: ' . $e->getMessage()
            ], 500);
        }
    }
}
