<?php

namespace App\Http\Services;

use App\Models\Page;
use App\Models\PageInvite;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;

class PageService
{
    public function createPage(array $data): Page
    {
        // Generate a unique slug if not provided
        if (!isset($data['slug']) || empty($data['slug'])) {
            $slug = Str::slug($data['title']);
            $originalSlug = $slug;
            $count = 1;
            while (Page::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $data['slug'] = $slug;
        }

        // Assign the current authenticated user as the creator
        // $data['user_id'] = JWTAuth::user()->id;
        $data['user_id'] = 1;

        $page = Page::create($data);

        // Auto-create admin invite for the page creator
        $this->createAdminInvite($page);

        return $page;
    }

    public function updatePage(Page $page, array $data): Page
    {
        // Generate new slug if title changed
        if (isset($data['title']) && $data['title'] !== $page->title) {
            $slug = Str::slug($data['title']);
            $originalSlug = $slug;
            $count = 1;
            while (Page::where('slug', $slug)->where('id', '!=', $page->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $data['slug'] = $slug;
        }

        $page->update($data);
        return $page;
    }

    public function deletePage(Page $page): bool
    {
        // Delete related invites and leads will cascade
        return $page->delete();
    }

    public function publishPage(Page $page): Page
    {
        $page->update([
            'status' => 'published',
            'publish_at' => now(),
        ]);

        return $page;
    }

    public function unpublishPage(Page $page): Page
    {
        $page->update([
            'status' => 'archived',
            'unpublish_at' => now(),
        ]);

        return $page;
    }

    public function duplicatePage(Page $page, string $newTitle): Page
    {
        $newPage = $page->replicate();
        $newPage->title = $newTitle;
        $newPage->slug = $this->generateUniqueSlug($newTitle);
        $newPage->status = 'draft';
        $newPage->views = 0;
        $newPage->publish_at = null;
        $newPage->unpublish_at = null;
        $newPage->save();

        // Create admin invite for the new page
        $this->createAdminInvite($newPage);

        return $newPage;
    }

    public function getPageStats(Page $page): array
    {
        $totalViews = $page->views;
        $totalInvites = $page->invites()->count();
        $totalLeads = $page->leads()->count();
        $conversionRate = $totalViews > 0 ? round(($totalLeads / $totalViews) * 100, 2) : 0;

        // Get top performing invites
        $topInvites = $page->invites()
            ->orderBy('leads_count', 'desc')
            ->orderBy('clicks', 'desc')
            ->with('user')
            ->limit(5)
            ->get();

        // Get recent activity
        $recentLeads = $page->leads()
            ->with(['referrerInvite.user', 'submitterInvite.user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return [
            'total_views' => $totalViews,
            'total_invites' => $totalInvites,
            'total_leads' => $totalLeads,
            'conversion_rate' => $conversionRate,
            'top_invites' => $topInvites,
            'recent_leads' => $recentLeads,
        ];
    }

    public function createInvite(Page $page, string $handle, ?string $joinUrl = null): PageInvite
    {
        // Check if handle already exists for this page
        if (PageInvite::where('page_id', $page->id)->where('handle', $handle)->exists()) {
            throw new \Exception("Handle '{$handle}' already exists for this page");
        }

        $invite = PageInvite::create([
            'page_id' => $page->id,
            'user_id' => JWTAuth::user()->id,
            'handle' => $handle,
            'join_url' => $joinUrl,
            'clicks' => 0,
            'leads_count' => 0,
            'is_active' => true,
        ]);

        return $invite;
    }

    public function generateInviteHandle(Page $page, string $baseName): string
    {
        $handle = Str::slug($baseName);
        $originalHandle = $handle;
        $count = 1;

        while (PageInvite::where('page_id', $page->id)->where('handle', $handle)->exists()) {
            $handle = $originalHandle . $count;
            $count++;
        }

        return $handle;
    }

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
        ];
    }

    private function createAdminInvite(Page $page): void
    {
        $user = JWTAuth::user();
        $handle = $this->generateInviteHandle($page, $user->first_name ?: 'admin');

        PageInvite::create([
            'page_id' => $page->id,
            'user_id' => $user->id,
            'handle' => $handle,
            'clicks' => 0,
            'leads_count' => 0,
            'is_active' => true,
        ]);
    }

    private function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (Page::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }
}