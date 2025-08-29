<?php

namespace App\Http\Services;

use App\Models\PageInvite;
use App\Models\Lead;
use App\Models\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ReferralService
{
    /**
     * Get complete referral tree for a specific invite
     */
    public function getReferralTree(PageInvite $invite): array
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

        return [
            'invite' => $invite->load(['user', 'page']),
            'upline' => $ancestors,
            'downline' => $descendants,
            'total_downline' => $descendants->count(),
            'total_upline' => $ancestors->count(),
            'network_depth' => $descendants->max('pivot.depth') ?? 0,
        ];
    }

    /**
     * Get direct referrals only (depth = 1)
     */
    public function getDirectReferrals(PageInvite $invite): Collection
    {
        return $invite->descendants()
            ->with(['user', 'page'])
            ->where('depth', 1)
            ->get();
    }

    /**
     * Get complete upline chain (who referred this user)
     */
    public function getUpline(PageInvite $invite): Collection
    {
        return $invite->ancestors()
            ->with(['user', 'page'])
            ->where('depth', '>', 0)
            ->orderBy('depth')
            ->get();
    }

    /**
     * Get referral statistics by level
     */
    public function getReferralStats(PageInvite $invite): array
    {
        $stats = DB::table('page_invite_closure as pic')
            ->join('page_invites as pi', 'pic.descendant_invite_id', '=', 'pi.id')
            ->where('pic.ancestor_invite_id', $invite->id)
            ->where('pic.depth', '>', 0)
            ->selectRaw('pic.depth, COUNT(*) as count, SUM(pi.leads_count) as total_leads, SUM(pi.clicks) as total_clicks')
            ->groupBy('pic.depth')
            ->orderBy('pic.depth')
            ->get();

        $totalStats = [
            'total_referrals' => 0,
            'total_leads' => 0,
            'total_clicks' => 0,
            'by_level' => [],
            'metrics' => [
                'avg_leads_per_referral' => 0,
                'click_to_lead_ratio' => 0,
                'referral_efficiency' => 0,
            ],
        ];

        foreach ($stats as $stat) {
            $totalStats['total_referrals'] += (int) $stat->count;
            $totalStats['total_leads'] += (int) $stat->total_leads;
            $totalStats['total_clicks'] += (int) $stat->total_clicks;
            $totalStats['by_level'][$stat->depth] = [
                'referrals' => (int) $stat->count,
                'leads' => (int) $stat->total_leads,
                'clicks' => (int) $stat->total_clicks,
                'conversion_rate' => (int) $stat->total_clicks > 0 ? round(($stat->total_leads / (int) $stat->total_clicks) * 100, 2) : 0,
            ];
        }

        // Calculate additional metrics
        if ($totalStats['total_referrals'] > 0) {
            $totalStats['metrics']['avg_leads_per_referral'] = round($totalStats['total_leads'] / $totalStats['total_referrals'], 2);
        }
        
        if ($totalStats['total_clicks'] > 0) {
            $totalStats['metrics']['click_to_lead_ratio'] = round($totalStats['total_clicks'] / max($totalStats['total_leads'], 1), 2);
        }
        
        if ($totalStats['total_clicks'] > 0) {
            $totalStats['metrics']['referral_efficiency'] = round(($totalStats['total_leads'] / $totalStats['total_clicks']) * 100, 2);
        }

        return $totalStats;
    }

    /**
     * Get page referral analytics
     */
    public function getPageReferralAnalytics(Page $page): array
    {
        $invites = $page->invites()
            ->with(['user', 'referredLeads', 'submittedLeads'])
            ->get();

        $referralStats = [];
        $topReferrers = [];

        foreach ($invites as $invite) {
            $stats = $this->getReferralStats($invite);
            
            $referralStats[] = [
                'invite_id' => $invite->id,
                'user' => $invite->user,
                'handle' => $invite->handle,
                'stats' => $stats,
                'performance_score' => $this->calculatePerformanceScore($invite, $stats),
            ];
        }

        // Sort by performance score
        usort($referralStats, function($a, $b) {
            return $b['performance_score'] <=> $a['performance_score'];
        });

        $topReferrers = array_slice($referralStats, 0, 10);

        return [
            'total_invites' => $invites->count(),
            'top_referrers' => $topReferrers,
            'overall_stats' => $this->getOverallPageStats($page),
            'referral_distribution' => $this->getReferralDistribution($page),
        ];
    }

    /**
     * Get user referral performance
     */
    public function getUserReferralPerformance(int $userId): array
    {
        $invites = PageInvite::where('user_id', $userId)
            ->with(['page', 'referredLeads', 'submittedLeads'])
            ->get();

        $totalStats = [
            'total_pages' => $invites->count(),
            'total_referrals' => 0,
            'total_leads' => 0,
            'total_clicks' => 0,
            'total_earnings' => 0, // Placeholder for commission calculations
            'by_page' => [],
        ];

        foreach ($invites as $invite) {
            $stats = $this->getReferralStats($invite);
            $pageStats = [
                'page' => $invite->page,
                'handle' => $invite->handle,
                'stats' => $stats,
                'performance_score' => $this->calculatePerformanceScore($invite, $stats),
            ];

            $totalStats['total_referrals'] += $stats['total_referrals'];
            $totalStats['total_leads'] += $stats['total_leads'];
            $totalStats['total_clicks'] += $stats['total_clicks'];
            $totalStats['by_page'][] = $pageStats;
        }

        // Sort pages by performance
        usort($totalStats['by_page'], function($a, $b) {
            return $b['performance_score'] <=> $a['performance_score'];
        });

        return $totalStats;
    }

    /**
     * Get referral network visualization data
     */
    public function getReferralNetwork(PageInvite $invite): array
    {
        $network = DB::table('page_invite_closure as pic')
            ->join('page_invites as pi', 'pic.descendant_invite_id', '=', 'pi.id')
            ->join('users as u', 'pi.user_id', '=', 'u.id')
            ->where('pic.ancestor_invite_id', $invite->id)
            ->select('pic.depth', 'pi.id', 'pi.handle', 'u.first_name', 'u.last_name', 'u.email', 'pi.leads_count', 'pi.clicks')
            ->orderBy('pic.depth')
            ->get();

        $networkData = [];
        foreach ($network as $node) {
            $networkData[] = [
                'id' => $node->id,
                'handle' => $node->handle,
                'name' => $node->first_name . ' ' . $node->last_name,
                'email' => $node->email,
                'depth' => $node->depth,
                'leads_count' => $node->leads_count,
                'clicks' => $node->clicks,
                'conversion_rate' => $node->clicks > 0 ? round(($node->leads_count / $node->clicks) * 100, 2) : 0,
            ];
        }

        return [
            'root_invite' => $invite->load(['user', 'page']),
            'network' => $networkData,
            'network_depth' => $network->max('depth') ?? 0,
            'total_nodes' => count($networkData),
        ];
    }

    /**
     * Update referral relationship (for admin use)
     */
    public function updateReferralRelationship(int $inviteId, int $newAncestorId): bool
    {
        try {
            DB::beginTransaction();

            // Remove old relationships
            DB::table('page_invite_closure')
                ->where('descendant_invite_id', $inviteId)
                ->delete();

            // Add new self-reference
            DB::table('page_invite_closure')->insert([
                'ancestor_invite_id' => $inviteId,
                'descendant_invite_id' => $inviteId,
                'depth' => 0,
            ]);

            // Add relationship to new ancestor
            DB::table('page_invite_closure')->insert([
                'ancestor_invite_id' => $newAncestorId,
                'descendant_invite_id' => $inviteId,
                'depth' => 1,
            ]);

            // Add all ancestor relationships
            $ancestors = DB::table('page_invite_closure')
                ->where('descendant_invite_id', $newAncestorId)
                ->where('depth', '>', 0)
                ->get();

            foreach ($ancestors as $ancestor) {
                DB::table('page_invite_closure')->insert([
                    'ancestor_invite_id' => $ancestor->ancestor_invite_id,
                    'descendant_invite_id' => $inviteId,
                    'depth' => $ancestor->depth + 1,
                ]);
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Calculate performance score for an invite
     */
    private function calculatePerformanceScore(PageInvite $invite, array $stats): float
    {
        // Base score from referrals (weight: 30%)
        $referralScore = $stats['total_referrals'] * 20;
        
        // Lead generation score (weight: 40%)
        $leadsScore = $stats['total_leads'] * 25;
        
        // Conversion efficiency score (weight: 30%)
        $conversionScore = 0;
        if ($stats['total_clicks'] > 0) {
            $conversionRate = ($stats['total_leads'] / $stats['total_clicks']) * 100;
            $conversionScore = min($conversionRate * 2, 100); // Cap at 100
        }
        
        // Bonus for high activity
        $activityBonus = 0;
        if ($stats['total_clicks'] >= 10) $activityBonus += 50;
        if ($stats['total_referrals'] >= 5) $activityBonus += 100;
        if ($stats['total_leads'] >= 10) $activityBonus += 75;
        
        $totalScore = $referralScore + $leadsScore + $conversionScore + $activityBonus;
        
        return round($totalScore, 2);
    }

    /**
     * Get overall page statistics
     */
    private function getOverallPageStats(Page $page): array
    {
        $totalLeads = $page->leads()->count();
        $totalViews = $page->views;
        $totalInvites = $page->invites()->count();

        return [
            'total_leads' => $totalLeads,
            'total_views' => $totalViews,
            'total_invites' => $totalInvites,
            'conversion_rate' => $totalViews > 0 ? round(($totalLeads / $totalViews) * 100, 2) : 0,
            'avg_leads_per_invite' => $totalInvites > 0 ? round($totalLeads / $totalInvites, 2) : 0,
        ];
    }

    /**
     * Get referral distribution across the page
     */
    private function getReferralDistribution(Page $page): array
    {
        $distribution = DB::table('page_invite_closure as pic')
            ->join('page_invites as pi', 'pic.ancestor_invite_id', '=', 'pi.id')
            ->where('pi.page_id', $page->id)
            ->where('pic.depth', '>', 0)
            ->selectRaw('pic.depth, COUNT(*) as count')
            ->groupBy('pic.depth')
            ->orderBy('pic.depth')
            ->get();

        $result = [];
        foreach ($distribution as $dist) {
            $result[$dist->depth] = $dist->count;
        }

        return $result;
    }

    /**
     * Get user-specific dashboard data (for normal users)
     */
    public function getUserDashboardSummary(int $userId, string $userRole = 'normal'): array
    {
        // Check if user is admin or super admin
        $isAdmin = in_array($userRole, ['admin', 'super admin']);
        
        if ($isAdmin) {
            // Admin sees all data across the system
            return $this->getAdminDashboardSummary();
        } else {
            // Normal user sees only their own data
            return $this->getNormalUserDashboardSummary($userId);
        }
    }

    /**
     * Get dashboard summary for normal users (only their data)
     */
    private function getNormalUserDashboardSummary(int $userId): array
    {
        // Get user's page invites
        $userInvites = PageInvite::where('user_id', $userId)->pluck('id');

        // Get total leads from user's pages
        $totalLeads = Lead::whereIn('referrer_invite_id', function($query) use ($userId) {
            $query->select('page_invites.id')
                  ->from('page_invites')
                  ->where('user_id', $userId);
        })->count();

        // Get unique referrers (people the user has referred) - unique per page + submitter combination
        $uniqueReferrers = DB::table('leads as l')
            ->join('page_invites as pi', 'l.referrer_invite_id', '=', 'pi.id')
            ->where('pi.user_id', $userId)
            ->distinct(['l.page_id', 'l.submitter_invite_id'])
            ->count(['l.page_id', 'l.submitter_invite_id']);

        // Get top performing page for this user (by leads they referred)
        $topPerformingPage = DB::table('leads as l')
            ->join('pages as p', 'l.page_id', '=', 'p.id')
            ->join('page_invites as pi', 'l.referrer_invite_id', '=', 'pi.id')
            ->where('pi.user_id', $userId)
            ->select([
                'p.id',
                'p.slug',
                'p.title',
                DB::raw('COUNT(*) as leads_count')
            ])
            ->groupBy('p.id', 'p.slug', 'p.title')
            ->orderBy('leads_count', 'desc')
            ->first();

        // Get latest lead that the user referred
        $latestLead = DB::table('leads as l')
            ->join('pages as p', 'l.page_id', '=', 'p.id')
            ->leftJoin('page_invites as pi', 'l.submitter_invite_id', '=', 'pi.id')
            ->leftJoin('users as u', 'pi.user_id', '=', 'u.id')
            ->leftJoin('page_invites as ref_pi', 'l.referrer_invite_id', '=', 'ref_pi.id')
            ->leftJoin('users as ref_u', 'ref_pi.user_id', '=', 'ref_u.id')
            ->where('ref_pi.user_id', $userId)
            ->select([
                'l.id as lead_id',
                'l.created_at as joined_at',
                'l.status as lead_status',
                'p.id as page_id',
                'p.slug as page_slug',
                'p.title as page_title',
                'u.first_name',
                'u.last_name',
                'u.email',
                'u.phone_number',
                'ref_pi.handle as referrer_handle',
                'ref_u.first_name as referrer_first_name',
                'ref_u.last_name as referrer_last_name'
            ])
            ->orderBy('l.created_at', 'desc')
            ->first();

        // Debug: Log the structure of latestLead for troubleshooting
        if ($latestLead) {
            \Log::info('Latest Lead Structure:', (array) $latestLead);
        }

        // Get recent activity for leads that the user referred (last 10 leads)
        $recentActivity = DB::table('leads as l')
            ->join('pages as p', 'l.page_id', '=', 'p.id')
            ->leftJoin('page_invites as pi', 'l.submitter_invite_id', '=', 'pi.id')
            ->leftJoin('users as u', 'pi.user_id', '=', 'u.id')
            ->leftJoin('page_invites as ref_pi', 'l.referrer_invite_id', '=', 'ref_pi.id')
            ->leftJoin('users as ref_u', 'ref_pi.user_id', '=', 'ref_u.id')
            ->where('ref_pi.user_id', $userId)
            ->select([
                'l.id as lead_id',
                'l.created_at as joined_at',
                'l.status as lead_status',
                'p.id as page_id',
                'p.slug as page_slug',
                'p.title as page_title',
                'u.first_name',
                'u.last_name',
                'u.email',
                'u.phone_number',
                'ref_pi.handle as referrer_handle',
                'ref_u.first_name as referrer_first_name',
                'ref_u.last_name as referrer_last_name'
            ])
            ->orderBy('l.created_at', 'desc')
            ->limit(10)
            ->get();

        // Format recent activity
        $formattedActivity = $recentActivity->map(function ($activity) {
            $leadName = ($activity->first_name ?? 'Unknown') . ' ' . substr($activity->last_name ?? 'User', 0, 1) . '.';
            $referrerName = $activity->referrer_handle ?? 'Direct';
            $pageName = ($activity->page_title ?? 'Unknown Page') . ' (' . ($activity->page_slug ?? 'unknown') . ')';

            return [
                'lead_id' => $activity->lead_id,
                'lead_name' => $leadName,
                'page_name' => $pageName,
                'page_slug' => $activity->page_slug ?? 'unknown',
                'referrer_name' => $referrerName,
                'referrer_invite_id' => $activity->referrer_handle ? 
                    DB::table('page_invites')->where('handle', $activity->referrer_handle)->value('id') : null,
                'joined_at' => $activity->joined_at,
                'lead_status' => $activity->lead_status ?? 'unknown',
                'actions' => [
                    'view_lead_url' => "/leads/{$activity->lead_id}",
                    'whatsapp_url' => $activity->phone_number ? "whatsapp://send?phone=" . preg_replace('/[^0-9]/', '', $activity->phone_number) : null,
                    'email_url' => "mailto:" . ($activity->email ?? ''),
                    'copy_link' => url("/leads/{$activity->lead_id}")
                ]
            ];
        });

        try {
            return [
                'total_leads' => $totalLeads,
                'unique_referrers' => $uniqueReferrers,
                'top_performing_page' => $topPerformingPage ? [
                    'name' => ($topPerformingPage->page_title ?? 'Unknown') . ' (' . ($topPerformingPage->slug ?? 'unknown') . ')',
                    'slug' => $topPerformingPage->slug ?? 'unknown',
                    'id' => $topPerformingPage->id,
                    'leads_count' => $topPerformingPage->leads_count ?? 0
                ] : null,
                'latest_lead' => $latestLead ? [
                    'name' => ($latestLead->first_name ?? 'Unknown') . ' ' . substr($latestLead->last_name ?? 'User', 0, 1) . '.',
                    'referrer' => $latestLead->referrer_handle ?? 'Direct',
                    'page' => $latestLead->page_slug ?? 'Unknown',
                    'page_id' => $latestLead->page_id,
                    'lead_id' => $latestLead->lead_id,
                    'referrer_invite_id' => $latestLead->referrer_handle ? 
                        DB::table('page_invites')->where('handle', $latestLead->referrer_handle)->value('id') : null,
                    'joined_at' => $latestLead->joined_at
                ] : null,
                'recent_activity' => $formattedActivity,
                'user_context' => 'personal'
            ];
        } catch (\Exception $e) {
            \Log::error('Error formatting dashboard data: ' . $e->getMessage(), [
                'latestLead' => $latestLead ? (array) $latestLead : null,
                'topPerformingPage' => $topPerformingPage ? (array) $topPerformingPage : null
            ]);
            throw $e;
        }
    }

    /**
     * Get dashboard summary for admin users (all system data)
     */
    private function getAdminDashboardSummary(): array
    {
        // Get total leads across all pages
        $totalLeads = Lead::count();

        // Get unique referrers across all pages
        $uniqueReferrers = DB::table('page_invite_closure as pic')
            ->join('page_invites as pi', 'pic.ancestor_invite_id', '=', 'pi.id')
            ->where('pic.depth', '>', 0)
            ->distinct('pi.user_id')
            ->count('pi.user_id');

        // Get top performing page across all pages (by leads generated)
        $topPerformingPage = DB::table('leads as l')
            ->join('pages as p', 'l.page_id', '=', 'p.id')
            ->select([
                'p.id',
                'p.slug',
                'p.title',
                DB::raw('COUNT(*) as leads_count')
            ])
            ->groupBy('p.id', 'p.slug', 'p.title')
            ->orderBy('leads_count', 'desc')
            ->first();

        // Get latest lead across all pages
        $latestLead = DB::table('leads as l')
            ->join('pages as p', 'l.page_id', '=', 'p.id')
            ->leftJoin('page_invites as pi', 'l.submitter_invite_id', '=', 'pi.id')
            ->leftJoin('users as u', 'pi.user_id', '=', 'u.id')
            ->leftJoin('page_invites as ref_pi', 'l.referrer_invite_id', '=', 'ref_pi.id')
            ->leftJoin('users as ref_u', 'ref_pi.user_id', '=', 'ref_u.id')
            ->select([
                'l.id as lead_id',
                'l.created_at as joined_at',
                'l.status as lead_status',
                'p.id as page_id',
                'p.slug as page_slug',
                'p.title as page_title',
                'u.first_name',
                'u.last_name',
                'u.email',
                'u.phone_number',
                'ref_pi.handle as referrer_handle',
                'ref_u.first_name as referrer_first_name',
                'ref_u.last_name as referrer_last_name'
            ])
            ->orderBy('l.created_at', 'desc')
            ->first();

        // Get recent activity across all pages (last 10 leads)
        $recentActivity = DB::table('leads as l')
            ->join('pages as p', 'l.page_id', '=', 'p.id')
            ->leftJoin('page_invites as pi', 'l.submitter_invite_id', '=', 'pi.id')
            ->leftJoin('users as u', 'pi.user_id', '=', 'u.id')
            ->leftJoin('page_invites as ref_pi', 'l.referrer_invite_id', '=', 'ref_pi.id')
            ->leftJoin('users as ref_u', 'ref_pi.user_id', '=', 'ref_u.id')
            ->select([
                'l.id as lead_id',
                'l.created_at as joined_at',
                'l.status as lead_status',
                'p.id as page_id',
                'p.slug as page_slug',
                'p.title as page_title',
                'u.first_name',
                'u.last_name',
                'u.email',
                'u.phone_number',
                'ref_pi.handle as referrer_handle',
                'ref_u.first_name as referrer_first_name',
                'ref_u.last_name as referrer_last_name'
            ])
            ->orderBy('l.created_at', 'desc')
            ->limit(10)
            ->get();

        // Format recent activity
        $formattedActivity = $recentActivity->map(function ($activity) {
            $leadName = ($activity->first_name ?? 'Unknown') . ' ' . substr($activity->last_name ?? 'User', 0, 1) . '.';
            $referrerName = $activity->referrer_handle ?? 'Direct';
            $pageName = ($activity->page_title ?? 'Unknown Page') . ' (' . ($activity->page_slug ?? 'unknown') . ')';

            return [
                'lead_id' => $activity->lead_id,
                'lead_name' => $leadName,
                'page_name' => $pageName,
                'page_slug' => $activity->page_slug ?? 'unknown',
                'referrer_name' => $referrerName,
                'referrer_invite_id' => $activity->referrer_handle ? 
                    DB::table('page_invites')->where('handle', $activity->referrer_handle)->value('id') : null,
                'joined_at' => $activity->joined_at,
                'lead_status' => $activity->lead_status ?? 'unknown',
                'actions' => [
                    'view_lead_url' => "/leads/{$activity->lead_id}",
                    'whatsapp_url' => $activity->phone_number ? "whatsapp://send?phone=" . preg_replace('/[^0-9]/', '', $activity->phone_number) : null,
                    'email_url' => "mailto:" . ($activity->email ?? ''),
                    'copy_link' => url("/leads/{$activity->lead_id}")
                ]
            ];
        });

        return [
            'total_leads' => $totalLeads,
            'unique_referrers' => $uniqueReferrers,
            'top_performing_page' => $topPerformingPage ? [
                'name' => ($topPerformingPage->page_title ?? 'Unknown') . ' (' . ($topPerformingPage->slug ?? 'unknown') . ')',
                'slug' => $topPerformingPage->slug ?? 'unknown',
                'id' => $topPerformingPage->id,
                'leads_count' => $topPerformingPage->leads_count ?? 0
            ] : null,
            'latest_lead' => $latestLead ? [
                'name' => ($latestLead->first_name ?? 'Unknown') . ' ' . substr($latestLead->last_name ?? 'User', 0, 1) . '.',
                'referrer' => $latestLead->referrer_handle ?? 'Direct',
                'page' => $latestLead->page_slug ?? 'Unknown',
                'page_id' => $latestLead->page_id,
                'lead_id' => $latestLead->lead_id,
                'referrer_invite_id' => $latestLead->referrer_handle ? 
                    DB::table('page_invites')->where('handle', $latestLead->referrer_handle)->value('id') : null,
                'joined_at' => $latestLead->joined_at
            ] : null,
            'recent_activity' => $formattedActivity,
            'user_context' => 'admin'
        ];
    }

    /**
     * Get comprehensive dashboard data
     */
    public function getDashboardSummary(): array
    {
        // Get total leads count
        $totalLeads = Lead::count();

        // Get unique referrers count
        $uniqueReferrers = DB::table('page_invite_closure as pic')
            ->join('page_invites as pi', 'pic.ancestor_invite_id', '=', 'pi.id')
            ->where('pic.depth', '>', 0)
            ->distinct('pi.user_id')
            ->count('pi.user_id');

        // Get top performing page (by leads generated)
        $topPerformingPage = DB::table('leads as l')
            ->join('pages as p', 'l.page_id', '=', 'p.id')
            ->select([
                'p.id',
                'p.slug',
                'p.title',
                DB::raw('COUNT(*) as leads_count')
            ])
            ->groupBy('p.id', 'p.slug', 'p.title')
            ->orderBy('leads_count', 'desc')
            ->first();

        // Get latest lead with referrer and page info
        $latestLead = DB::table('leads as l')
            ->join('pages as p', 'l.page_id', '=', 'p.id')
            ->leftJoin('page_invites as pi', 'l.submitter_invite_id', '=', 'pi.id')
            ->leftJoin('users as u', 'pi.user_id', '=', 'u.id')
            ->leftJoin('page_invites as ref_pi', 'l.referrer_invite_id', '=', 'ref_pi.id')
            ->leftJoin('users as ref_u', 'ref_pi.user_id', '=', 'ref_u.id')
            ->select([
                'l.id as lead_id',
                'l.created_at as joined_at',
                'l.status as lead_status',
                'p.id as page_id',
                'p.slug as page_slug',
                'p.title as page_title',
                'u.first_name',
                'u.last_name',
                'u.email',
                'ref_pi.handle as referrer_handle',
                'ref_u.first_name as referrer_first_name',
                'ref_u.last_name as referrer_last_name'
            ])
            ->orderBy('l.created_at', 'desc')
            ->first();

        // Get recent activity (last 10 leads)
        $recentActivity = DB::table('leads as l')
            ->join('pages as p', 'l.page_id', '=', 'p.id')
            ->leftJoin('page_invites as pi', 'l.submitter_invite_id', '=', 'pi.id')
            ->leftJoin('users as u', 'pi.user_id', '=', 'u.id')
            ->leftJoin('page_invites as ref_pi', 'l.referrer_invite_id', '=', 'ref_pi.id')
            ->leftJoin('users as ref_u', 'ref_pi.user_id', '=', 'ref_u.id')
            ->select([
                'l.id as lead_id',
                'l.created_at as joined_at',
                'l.status as lead_status',
                'p.id as page_id',
                'p.slug as page_slug',
                'p.title as page_title',
                'u.first_name',
                'u.last_name',
                'u.email',
                'u.phone_number',
                'ref_pi.handle as referrer_handle',
                'ref_u.first_name as referrer_first_name',
                'ref_u.last_name as referrer_last_name'
            ])
            ->orderBy('l.created_at', 'desc')
            ->limit(10)
            ->get();

        // Format recent activity
        $formattedActivity = $recentActivity->map(function ($activity) {
            $leadName = $activity->first_name . ' ' . substr($activity->last_name, 0, 1) . '.';
            $referrerName = $activity->referrer_handle ?? 'Direct';
            $pageName = $activity->page_title . ' (' . $activity->page_slug . ')';

            return [
                'lead_id' => $activity->lead_id,
                'lead_name' => $leadName,
                'page_name' => $pageName,
                'page_slug' => $activity->page_slug,
                'referrer_name' => $referrerName,
                'referrer_invite_id' => $activity->referrer_handle ? 
                    DB::table('page_invites')->where('handle', $activity->referrer_handle)->value('id') : null,
                'joined_at' => $activity->joined_at,
                'lead_status' => $activity->lead_status,
                'actions' => [
                    'view_lead_url' => "/leads/{$activity->lead_id}",
                    'whatsapp_url' => $activity->phone_number ? "whatsapp://send?phone=" . preg_replace('/[^0-9]/', '', $activity->phone_number) : null,
                    'email_url' => "mailto:{$activity->email}",
                    'copy_link' => url("/leads/{$activity->lead_id}")
                ]
            ];
        });

        return [
            'total_leads' => $totalLeads,
            'unique_referrers' => $uniqueReferrers,
            'top_performing_page' => $topPerformingPage ? [
                'name' => $topPerformingPage->page_title . ' (' . $topPerformingPage->slug . ')',
                'slug' => $topPerformingPage->slug,
                'id' => $topPerformingPage->id,
                'leads_count' => $topPerformingPage->leads_count
            ] : null,
            'latest_lead' => $latestLead ? [
                'name' => $latestLead->first_name . ' ' . substr($latestLead->last_name, 0, 1) . '.',
                'referrer' => $latestLead->referrer_handle ?? 'Direct',
                'page' => $latestLead->page_slug,
                'page_id' => $latestLead->page_id,
                'lead_id' => $latestLead->lead_id,
                'referrer_invite_id' => $latestLead->referrer_handle ? 
                    DB::table('page_invites')->where('handle', $latestLead->referrer_handle)->value('id') : null,
                'joined_at' => $latestLead->joined_at
            ] : null,
            'recent_activity' => $formattedActivity
        ];
    }

    /**
     * Get detailed level 1 invitees with lead status and their referral count
     */
    public function getLevelOneInviteesDetails(PageInvite $invite, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $query = DB::table('page_invite_closure as pic')
            ->join('page_invites as pi', 'pic.descendant_invite_id', '=', 'pi.id')
            ->join('users as u', 'pi.user_id', '=', 'u.id')
            ->leftJoin('leads as l', 'pi.id', '=', 'l.submitter_invite_id')
            ->where('pic.ancestor_invite_id', $invite->id)
            ->where('pic.depth', 1)
            ->select([
                'pi.id as invite_id',
                'pi.handle',
                'pi.created_at as joined_date',
                'pi.clicks',
                'pi.leads_count',
                'u.id as user_id',
                'u.first_name',
                'u.last_name',
                'u.email',
                'l.id as lead_id',
                'l.status as lead_status',
                'l.created_at as lead_created_at',
                'l.notes as lead_notes'
            ])
            ->orderBy('pi.created_at', 'desc');

        // Get total count for pagination
        $totalCount = $query->count();
        
        // Apply pagination
        $levelOneInvitees = $query->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $inviteesWithDetails = [];

        foreach ($levelOneInvitees as $invitee) {
            // Get how many people this invitee has invited (their level 1 referrals)
            $theirReferrals = DB::table('page_invite_closure')
                ->where('ancestor_invite_id', $invitee->invite_id)
                ->where('depth', 1)
                ->count();

            // Get their lead details if they exist
            $leadDetails = null;
            if ($invitee->lead_id) {
                $leadDetails = [
                    'id' => $invitee->lead_id,
                    'status' => $invitee->lead_status,
                    'created_at' => $invitee->lead_created_at,
                    'notes' => $invitee->lead_notes,
                ];
            }

            $inviteesWithDetails[] = [
                'invite_id' => $invitee->invite_id,
                'handle' => $invitee->handle,
                'user' => [
                    'id' => $invitee->user_id,
                    'name' => $invitee->first_name . ' ' . $invitee->last_name,
                    'email' => $invitee->email,
                ],
                'joined_date' => $invitee->joined_date,
                'activity' => [
                    'clicks' => $invitee->clicks,
                    'leads_count' => $invitee->leads_count,
                ],
                'lead' => $leadDetails,
                'referral_network' => [
                    'immediate_invitees' => $theirReferrals,
                    'total_downline' => $this->getTotalDownlineCount($invitee->invite_id),
                ],
                'performance_summary' => [
                    'has_joined' => !is_null($invitee->lead_id),
                    'is_active_referrer' => $theirReferrals > 0,
                    'referral_potential' => $this->getReferralPotential($invitee->invite_id),
                ]
            ];
        }

        // Create Laravel paginator with flat data structure
        $paginator = new LengthAwarePaginator(
            $inviteesWithDetails,
            $totalCount,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );

        return $paginator;
    }

    /**
     * Get total downline count for an invite
     */
    private function getTotalDownlineCount(int $inviteId): int
    {
        return DB::table('page_invite_closure')
            ->where('ancestor_invite_id', $inviteId)
            ->where('depth', '>', 0)
            ->count();
    }

    /**
     * Calculate referral potential score for an invite
     */
    private function getReferralPotential(int $inviteId): string
    {
        $immediateReferrals = DB::table('page_invite_closure')
            ->where('ancestor_invite_id', $inviteId)
            ->where('depth', 1)
            ->count();

        $totalDownline = $this->getTotalDownlineCount($inviteId);

        if ($immediateReferrals == 0) {
            return 'New';
        } elseif ($immediateReferrals <= 2) {
            return 'Low';
        } elseif ($immediateReferrals <= 5) {
            return 'Medium';
        } elseif ($immediateReferrals <= 10) {
            return 'High';
        } else {
            return 'Expert';
        }
    }
} 