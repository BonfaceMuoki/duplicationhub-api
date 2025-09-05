<?php

namespace App\Http\Controllers;

use App\Http\Services\PageService;
use App\Http\Services\LeadService;
use App\Models\Page;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    protected PageService $pageService;
    protected LeadService $leadService;

    public function __construct(PageService $pageService, LeadService $leadService)
    {
        $this->pageService = $pageService;
        $this->leadService = $leadService;
    }

    /**
     * Get all pages with optional filtering and pagination
     */
    public function getAllPages(Request $request)
    {
        $request->validate([
            'status' => ['sometimes', 'in:draft,published,archived'],
            'is_active' => ['sometimes', 'boolean'],
            'search' => ['sometimes', 'string', 'max:255'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort_by' => ['sometimes', 'in:title,created_at,updated_at,status,sort_order'],
            'sort_direction' => ['sometimes', 'in:asc,desc'],
        ]);

        $query = Page::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('headline', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $pages = $query->paginate($perPage);

        // Add additional data for each page
        $pages->getCollection()->transform(function ($page) {
            $page->leads_count = $page->leads()->count();
            $page->invites_count = $page->invites()->count();
            $page->total_clicks = $page->invites()->sum('clicks');
            // full_image_url is automatically appended via the model accessor
            return $page;
        });

        return response()->json([
            'success' => true,
            'message' => 'Pages retrieved successfully',
            'data' => $pages->items(),
            'pagination' => [
                'current_page' => $pages->currentPage(),
                'last_page' => $pages->lastPage(),
                'per_page' => $pages->perPage(),
                'total' => $pages->total(),
                'from' => $pages->firstItem(),
                'to' => $pages->lastItem(),
            ]
        ]);
    }

    /**
     * Get a specific page with detailed information
     */
    public function getPage(Page $page)
    {
        // Load relationships
        $page->load([
            'leads' => function ($query) {
                $query->latest()->limit(10); // Latest 10 leads
            },
            'invites' => function ($query) {
                $query->latest()->limit(10); // Latest 10 invites
            }
        ]);

        // Add counts and statistics
        $page->leads_count = $page->leads()->count();
        $page->invites_count = $page->invites()->count();
        $page->total_clicks = $page->invites()->sum('clicks');
        $page->total_leads = $page->leads()->count();
        
        // Add recent activity
        $page->recent_leads = $page->leads()->latest()->limit(5)->get();
        $page->recent_invites = $page->invites()->latest()->limit(5)->get();

        return response()->json([
            'success' => true,
            'message' => 'Page retrieved successfully',
            'data' => $page
        ]);
    }

    public function createPage(Request $request)
    {
        try {
            // Validation
            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'headline' => ['required', 'string', 'max:255'],
                'summary' => ['nullable', 'string'],
                'video_url' => ['nullable', 'url', 'max:255'],
                'cta_text' => ['nullable', 'string', 'max:255'],
                'is_active' => ['boolean'],
                'status' => ['nullable', 'in:draft,published,archived'],
                'publish_at' => ['nullable', 'date'],
                'unpublish_at' => ['nullable', 'date', 'after:publish_at'],
                'sort_order' => ['nullable', 'integer'],
                'meta_title' => ['nullable', 'string', 'max:255'],
                'meta_description' => ['nullable', 'string'],
                'og_image_url' => ['nullable', 'url', 'max:255'],
                'canonical_url' => ['nullable', 'url', 'max:255'],
                'is_indexable' => ['boolean'],
                'cta_subtext' => ['nullable', 'string', 'max:255'],
                'default_join_url' => ['nullable', 'url', 'max:255'],
                'capture_mode' => ['nullable', 'in:modal,inline'],
                'body' => ['nullable', 'string'],
                'experiment_group' => ['nullable', 'string', 'max:255'],
                'variant' => ['nullable', 'string', 'max:255'],
                'allocation_weight' => ['nullable', 'integer', 'min:0', 'max:100'],
                'consent_text' => ['nullable', 'string'],
                'show_consent' => ['boolean'],
                'rate_limit_per_ip_per_day' => ['nullable', 'integer', 'min:0'],
                'require_https_join' => ['boolean'],
                'allowed_join_domains' => ['nullable', 'array'],
                'allowed_join_domains.*' => ['string'],
                'platform_base_url' => ['nullable', 'url', 'max:255'],
                // Image upload fields
                'image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
                'base64_image' => ['nullable', 'string'],
            ]);
    
            $pageData = $validated;
            $imageUrl = null;
    
            // Handle image upload if present
            if (
                $request->hasFile('image') ||
                $request->has('base64_image') ||
                ($request->has('image_url') && str_starts_with($request->image_url, 'data:image/'))
            ) {
                $imageUrl = $this->handleImageUpload($request);
                if ($imageUrl) {
                    $pageData['image_url'] = $imageUrl;
                }
            }
    
            $page = $this->pageService->createPage($pageData);
    
            return response()->json([
                'success' => true,
                'message' => 'Page created successfully',
                'data' => $page
            ], 201);
    
        } catch (ValidationException $ve) {
            // Log validation errors with input
            Log::warning('Page creation validation failed', [
                'errors' => $ve->errors(),
                'request_data' => $request->all(),
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $ve->errors()
            ], 422);
    
        } catch (\Exception $e) {
            // Log runtime errors
            Log::error('Page creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to create page. Please try again later.'
            ], 500);
        }
    }

    /**
     * Handle image upload for page creation
     */
    private function handleImageUpload(Request $request): ?string
    {
        try {
            $filename = null;

            // Handle file upload (multipart/form-data)
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                
                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Store in images/pages directory using public disk
                $path = $file->storeAs('images/pages', $filename, 'public');
            }

            // Handle base64 image from base64_image field
            if ($request->has('base64_image')) {
                $base64String = $request->base64_image;
                $filename = $this->processBase64Image($base64String);
            }

            // Handle base64 image from image_url field
            if ($request->has('image_url') && str_starts_with($request->image_url, 'data:image/')) {
                $base64String = $request->image_url;
                $filename = $this->processBase64Image($base64String);
            }

            // Return the filename
            return $filename;

        } catch (\Exception $e) {
            \Log::error('Image upload failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Process base64 image data and store it
     */
    private function processBase64Image(string $base64String): string
    {
        // Remove data:image/...;base64, prefix if present
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $matches)) {
            $imageType = $matches[1];
            $base64String = substr($base64String, strpos($base64String, ',') + 1);
        } else {
            $imageType = 'png'; // Default to PNG if no type specified
        }

        // Validate base64 string
        if (!base64_decode($base64String, true)) {
            throw new \Exception('Invalid base64 image data');
        }

        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $imageType;
        
        // Store base64 image
        $path = 'images/pages/' . $filename;
        \Storage::disk('public')->put($path, base64_decode($base64String));
        
        return $filename;
    }

    public function updatePage(Request $request, Page $page)
    {
        $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'headline' => ['sometimes', 'string', 'max:255'],
            'summary' => ['sometimes', 'nullable', 'string'],
            'video_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'cta_text' => ['sometimes', 'nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'nullable', 'in:draft,published,archived'],
            'publish_at' => ['sometimes', 'nullable', 'date'],
            'unpublish_at' => ['sometimes', 'nullable', 'date', 'after:publish_at'],
            'sort_order' => ['sometimes', 'nullable', 'integer'],
            'meta_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'meta_description' => ['sometimes', 'nullable', 'string'],
            'og_image_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'canonical_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'is_indexable' => ['sometimes', 'boolean'],
            'cta_subtext' => ['sometimes', 'nullable', 'string', 'max:255'],
            'default_join_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'capture_mode' => ['sometimes', 'nullable', 'in:modal,inline'],
            'body' => ['sometimes', 'nullable', 'string'],
            'experiment_group' => ['sometimes', 'nullable', 'string', 'max:255'],
            'variant' => ['sometimes', 'nullable', 'string', 'max:255'],
            'allocation_weight' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:100'],
            'consent_text' => ['sometimes', 'nullable', 'string'],
            'show_consent' => ['sometimes', 'boolean'],
            'rate_limit_per_ip_per_day' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'require_https_join' => ['sometimes', 'boolean'],
            'allowed_join_domains' => ['sometimes', 'nullable', 'array'],
            'allowed_join_domains.*' => ['string'],
            'platform_base_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            // Image upload fields
            'image' => ['sometimes', 'nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'], // 10MB max
            'base64_image' => ['sometimes', 'nullable', 'string'],
        ]);

        try {
            $pageData = $request->all();
            $imageUrl = null;
            // Handle image upload if present
            if ($request->hasFile('image') || $request->has('base64_image') || 
                ($request->has('image_url') && str_starts_with($request->image_url, 'data:image/'))) {
                $imageUrl = $this->handleImageUpload($request);
                if ($imageUrl) {
                    $pageData['image_url'] = $imageUrl;
                }
            }

            $page = $this->pageService->updatePage($page, $pageData);

            return response()->json([
                'message' => 'Page updated successfully',
                'data' => $page
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update page: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deletePage(Page $page)
    {
        $this->pageService->deletePage($page);

        return response()->json([
            'message' => 'Page deleted successfully'
        ]);
    }

    public function publishPage(Page $page)
    {
        $page = $this->pageService->publishPage($page);

        return response()->json([
            'message' => 'Page published successfully',
            'data' => $page
        ]);
    }

    public function unpublishPage(Page $page)
    {
        $page = $this->pageService->unpublishPage($page);

        return response()->json([
            'message' => 'Page unpublished successfully',
            'data' => $page
        ]);
    }

    public function duplicatePage(Request $request, Page $page)
    {
        $request->validate([
            'new_title' => ['required', 'string', 'max:255'],
        ]);

        $newPage = $this->pageService->duplicatePage($page, $request->new_title);

        return response()->json([
            'message' => 'Page duplicated successfully',
            'data' => $newPage
        ], 201);
    }

    public function getPageStats(Page $page)
    {
        $stats = $this->pageService->getPageStats($page);

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function getAllLeads(Request $request)
    {
        $filters = $request->only(['status', 'page_id', 'date_from', 'date_to', 'search']);
        $perPage = $request->get('per_page', 50);

        $result = $this->leadService->getAllLeads($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function updateLeadStatus(Request $request, Lead $lead)
    {
        $request->validate([
            'status' => ['required', 'in:new,contacted,joined,joining_link_shared,advertisement_link_shared'],
            'notes' => ['nullable', 'string'],
            'full_external_invite_url' => ['nullable', 'string', 'url'],
            'external_invite_code' => ['nullable', 'string', 'unique:page_invites,external_invite_code']
        ]);

        $lead = $this->leadService->updateLeadStatus(
            $lead, 
            $request->status, 
            $request->notes,
            $request->full_external_invite_url,
            $request->external_invite_code
        );

        return response()->json([
            'success' => true,
            'message' => 'Lead status updated successfully',
            // 'data' => $lead
        ]);
    }

    public function getLeadAnalytics(Request $request, Page $page)
    {
        $filters = $request->only(['date_from', 'date_to']);
        
        $analytics = $this->leadService->getPageLeadAnalytics($page, $filters);

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }

    public function dashboard()
    {
        // Get overall system statistics
        $totalPages = Page::count();
        $totalLeads = Lead::count();
        $totalUsers = \App\Models\User::count();
        $totalInvites = \App\Models\PageInvite::count();

        // Get recent activity
        $recentLeads = Lead::with(['page', 'referrerInvite.user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentPages = Page::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'overview' => [
                    'total_pages' => $totalPages,
                    'total_leads' => $totalLeads,
                    'total_users' => $totalUsers,
                    'total_invites' => $totalInvites,
                ],
                'recent_leads' => $recentLeads,
                'recent_pages' => $recentPages,
            ]
        ]);
    }

    /**
     * Upload page image (supports both file upload and base64)
     */
    public function uploadPageImage(Request $request)
    {
        $request->validate([
            'image' => ['required_without:base64_image'],
            'base64_image' => ['required_without:image'],
            'image_name' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $filename = null;

            // Handle file upload (multipart/form-data)
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                
                // Validate file
                $request->validate([
                    'image' => ['file', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'], // 10MB max
                ]);

                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Store in public/images/pages directory
                $path = $file->storeAs('public/images/pages', $filename);
            }

            // Handle base64 image
            if ($request->has('base64_image')) {
                $base64String = $request->base64_image;
                
                // Remove data:image/...;base64, prefix if present
                if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $matches)) {
                    $imageType = $matches[1];
                    $base64String = substr($base64String, strpos($base64String, ',') + 1);
                } else {
                    $imageType = 'png'; // Default to PNG if no type specified
                }

                // Validate base64 string
                if (!base64_decode($base64String, true)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid base64 image data'
                    ], 400);
                }

                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $imageType;
                
                // Store base64 image
                $path = 'public/images/pages/' . $filename;
                \Storage::put($path, base64_decode($base64String));
            }

            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'data' => [
                    'image_url' => $filename, // Return filename as image_url
                    'filename' => $filename,
                    'path' => $path ?? null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ], 500);
        }
    }
}