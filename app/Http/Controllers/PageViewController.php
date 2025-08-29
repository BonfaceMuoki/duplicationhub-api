<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageInvite;
use Illuminate\Http\Request;

class PageViewController extends Controller
{
    /**
     * Display a capture page
     */
    public function show(Request $request, string $slug)
    {
        $page = Page::where('slug', $slug)
            ->where('is_active', true)
            ->where('status', 'published')
            ->firstOrFail();

        // Check if page is published
        if ($page->publish_at && $page->publish_at->isFuture()) {
            abort(404, 'Page not yet published');
        }

        if ($page->unpublish_at && $page->unpublish_at->isPast()) {
            abort(404, 'Page has been unpublished');
        }

        // Get referrer from query parameter
        $ref = $request->query('ref');
        
        if ($ref) {
            // Validate referrer exists
            $invite = PageInvite::where('page_id', $page->id)
                ->where('handle', $ref)
                ->where('is_active', true)
                ->first();

            if (!$invite) {
                abort(404, 'Invalid referral link');
            }

            // Track the view
            $page->increment('views');
            $invite->increment('clicks');
        }

        // Prepare page data for frontend
        $pageData = [
            'id' => $page->id,
            'slug' => $page->slug,
            'title' => $page->title,
            'headline' => $page->headline,
            'summary' => $page->summary,
            'video_url' => $page->video_url,
            'image_url' => $page->image_url,
            'cta_text' => $page->cta_text,
            'cta_subtext' => $page->cta_subtext,
            'body' => $page->body,
            'capture_mode' => $page->capture_mode,
            'show_consent' => $page->show_consent,
            'consent_text' => $page->consent_text,
            'ref' => $ref,
            'platform_base_url' => $page->platform_base_url,
        ];

        // For API requests, return JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $pageData
            ]);
        }

        // For web requests, return view with Open Graph tags
        return view('pages.show', [
            'page' => $pageData,
            'ogTags' => $this->generateOpenGraphTags($page, $ref)
        ]);
    }

    /**
     * Generate Open Graph tags for social media sharing
     */
    private function generateOpenGraphTags(Page $page, ?string $ref = null): array
    {
        $baseUrl = url("/{$page->slug}");
        $fullUrl = $ref ? "{$baseUrl}?ref={$ref}" : $baseUrl;

        return [
            'title' => $page->meta_title ?: $page->headline,
            'description' => $page->meta_description ?: $page->summary,
            'image' => $page->og_image_url ?: $page->image_url,
            'url' => $fullUrl,
            'type' => 'website',
            'site_name' => config('app.name'),
        ];
    }

    /**
     * Get page analytics (for authenticated users)
     */
    public function analytics(Request $request, string $slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        
        // Check if user has access to this page
        if (auth()->user()->id !== $page->user_id && !auth()->user()->hasRole('super admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view this page analytics'
            ], 403);
        }

        // Get page statistics
        $stats = [
            'total_views' => $page->views,
            'total_invites' => $page->invites()->count(),
            'total_leads' => $page->leads()->count(),
            'conversion_rate' => $page->views > 0 ? round(($page->leads()->count() / $page->views) * 100, 2) : 0,
        ];

        // Get top performing invites
        $topInvites = $page->invites()
            ->orderBy('leads_count', 'desc')
            ->orderBy('clicks', 'desc')
            ->with('user')
            ->limit(10)
            ->get();

        // Get recent leads
        $recentLeads = $page->leads()
            ->with(['referrerInvite.user', 'submitterInvite.user'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'page' => $page,
                'stats' => $stats,
                'top_invites' => $topInvites,
                'recent_leads' => $recentLeads,
            ]
        ]);
    }

    /**
     * Get page preview (for page creators)
     */
    public function preview(Request $request, string $slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        
        // Check if user has access to preview this page
        if (auth()->user()->id !== $page->user_id && !auth()->user()->hasRole('super admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to preview this page'
            ], 403);
        }

        // Return page data without tracking views
        $pageData = [
            'id' => $page->id,
            'slug' => $page->slug,
            'title' => $page->title,
            'headline' => $page->headline,
            'summary' => $page->summary,
            'video_url' => $page->video_url,
            'image_url' => $page->image_url,
            'cta_text' => $page->cta_text,
            'cta_subtext' => $page->cta_subtext,
            'body' => $page->body,
            'capture_mode' => $page->capture_mode,
            'show_consent' => $page->show_consent,
            'consent_text' => $page->consent_text,
            'platform_base_url' => $page->platform_base_url,
            'is_preview' => true,
        ];

        return response()->json([
            'success' => true,
            'data' => $pageData
        ]);
    }
} 