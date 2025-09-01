<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageInvite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{
    /**
     * Display the blog listing page
     */
    public function index()
    {
        // Get published pages without caching for now
        $pages = Page::where('status', 'published')
            ->where('is_active', true)
            // ->where('is_indexable', true)
            // ->where(function ($query) {
            //     $query->whereNull('publish_at')
            //         ->orWhere('publish_at', '<=', now());
            // })
            // ->where(function ($query) {
            //     $query->whereNull('unpublish_at')
            //         ->orWhere('unpublish_at', '>', now());
            // })
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->with(['user', 'invites'])
            ->get();

        // SEO meta data for pages listing
        $seoData = [
            'title' => 'Pages - ' . config('app.name'),
            'description' => 'Discover insights, tips, and strategies to grow your business. Read our latest articles and guides.',
            'keywords' => 'business tips, growth strategies, marketing insights, business pages',
            'og_type' => 'website',
            'canonical_url' => url('/pages'),
        ];

        return view('pages.index', compact('pages', 'seoData'));
    }

    /**
     * Display a single blog page
     */
    public function show(Request $request, string $slug)
    {
        $page = Page::where('slug', $slug)
            ->where('status', 'published')
            ->where('is_active', true)
            ->where('is_indexable', true)
            ->where(function ($query) {
                $query->whereNull('publish_at')
                    ->orWhere('publish_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('unpublish_at')
                    ->orWhere('unpublish_at', '>', now());
            })
            ->with(['user', 'invites'])
            ->firstOrFail();

        // Get referrer from query parameter
        $ref = $request->query('ref');
        
        if ($ref) {
            // Validate referrer exists
            $invite = PageInvite::where('page_id', $page->id)
                ->where('handle', $ref)
                ->where('is_active', true)
                ->first();

            if ($invite) {
                // Track the view
                $page->increment('views');
                $invite->increment('clicks');
            }
        }

        // Get related pages
        $relatedPages = Cache::remember("related_pages_{$page->id}", 3600, function () use ($page) {
            return Page::where('status', 'published')
                ->where('is_active', true)
                ->where('is_indexable', true)
                ->where('id', '!=', $page->id)
                ->where('user_id', $page->user_id)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
        });

        // SEO meta data for individual page
        $seoData = [
            'title' => $page->meta_title ?: $page->title,
            'description' => $page->meta_description ?: $page->summary,
            'keywords' => $page->meta_keywords ?? 'business, growth, strategy',
            'og_type' => 'article',
            'canonical_url' => url("/pages/{$page->slug}"),
            'og_image' => $page->og_image_url ?: $page->full_image_url,
            'published_time' => $page->created_at->toISOString(),
            'modified_time' => $page->updated_at->toISOString(),
            'author' => $page->user->name ?? 'Admin',
        ];

        return view('pages.show', compact('page', 'relatedPages', 'seoData', 'ref'));
    }

    /**
     * Display blog page by category/tag (if you implement categories later)
     */
    public function category(string $category)
    {
        // This can be implemented when you add categories to pages
        abort(404);
    }

    /**
     * Search blog posts
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return redirect()->route('pages.index');
        }

        $pages = Page::where('status', 'published')
            ->where('is_active', true)
            ->where('is_indexable', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('headline', 'like', "%{$query}%")
                  ->orWhere('summary', 'like', "%{$query}%")
                  ->orWhere('body', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->with(['user', 'invites'])
            ->get();

        $seoData = [
            'title' => "Search Results for '{$query}' - " . config('app.name'),
            'description' => "Search results for '{$query}' on our pages. Find relevant articles and insights.",
            'canonical_url' => url("/pages/search?q={$query}"),
        ];

        return view('pages.search', compact('pages', 'query', 'seoData'));
    }
} 