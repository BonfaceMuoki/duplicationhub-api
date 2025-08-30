<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Represents a configurable landing/capture page.
 */
class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'slug',
        'title',
        'headline',
        'summary',
        'video_url',
        'image_url',
        'cta_text',
        'is_active',
        'is_public',
        'views',

        // Publishing
        'status',
        'publish_at',
        'unpublish_at',
        'sort_order',

        // SEO & social
        'meta_title',
        'meta_description',
        'og_image_url',
        'canonical_url',
        'is_indexable',

        // CTA behavior
        'cta_subtext',
        'default_join_url',
        'capture_mode',
        'platform_base_url',

        // Content body
        'body',

        // Experiments
        'experiment_group',
        'variant',
        'allocation_weight',

        // Compliance
        'consent_text',
        'show_consent',

        // Abuse control
        'rate_limit_per_ip_per_day',
        'require_https_join',
        'allowed_join_domains',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_indexable' => 'boolean',
        'show_consent' => 'boolean',
        'require_https_join' => 'boolean',
        'is_public' => 'boolean',

        'views' => 'integer',
        'sort_order' => 'integer',
        'allocation_weight' => 'integer',
        'rate_limit_per_ip_per_day' => 'integer',

        'publish_at' => 'datetime',
        'unpublish_at' => 'datetime',

        'allowed_join_domains' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = ['full_url', 'generated_slug'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invites(): HasMany
    {
        return $this->hasMany(PageInvite::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function linkShares(): HasMany
    {
        return $this->hasMany(PageInviteLinkShare::class);
    }

    /**
     * Get the full URL for the page image
     */
    public function getFullImageUrlAttribute(): ?string
    {
        if (!$this->image_url) {
            return null;
        }

        // If it's already a full URL, return as is
        if (filter_var($this->image_url, FILTER_VALIDATE_URL)) {
            return $this->image_url;
        }

        // Convert relative path to full storage URL
        return asset('storage/images/pages/' . $this->image_url);
    }

    /**
     * Get the complete URL for the page
     */
    public function getFullUrlAttribute(): string
    {
        return url('/pages/' . $this->slug);
    }

    /**
     * Get a generated slug from the title
     */
    public function getGeneratedSlugAttribute(): string
    {
        if (!$this->title) {
            return '';
        }

        // Convert to lowercase and replace spaces with hyphens
        $slug = strtolower(trim($this->title));
        
        // Remove special characters and replace with hyphens
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        
        // Replace multiple spaces or hyphens with single hyphen
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        
        // Remove leading and trailing hyphens
        $slug = trim($slug, '-');
        
        // Ensure slug is not empty after processing
        if (empty($slug)) {
            $slug = 'page';
        }
        
        // Ensure slug starts with a letter or number
        if (!preg_match('/^[a-z0-9]/', $slug)) {
            $slug = 'page-' . $slug;
        }
        
        return $slug;
    }

    /**
     * Generate a unique slug from the title
     */
    public function generateUniqueSlug(): string
    {
        $baseSlug = $this->getGeneratedSlugAttribute();
        
        // Ensure base slug is not empty
        if (empty($baseSlug)) {
            $baseSlug = 'page';
        }
        
        $slug = $baseSlug;
        $counter = 1;
        $maxAttempts = 100; // Prevent infinite loops
        $attempts = 0;

        // Check if slug exists and append number if needed
        while (static::where('slug', $slug)->where('id', '!=', $this->id)->exists() && $attempts < $maxAttempts) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
            $attempts++;
        }

        // If we hit max attempts, append timestamp to ensure uniqueness
        if ($attempts >= $maxAttempts) {
            $slug = $baseSlug . '-' . time();
        }

        // Ensure slug is not too long (database constraint)
        if (strlen($slug) > 255) {
            $slug = substr($slug, 0, 255);
        }

        return $slug;
    }

    /**
     * Boot the model and add event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug before saving
        static::saving(function ($page) {
            // Only generate slug if title exists and slug is empty or title changed
            if ($page->title && (!$page->slug || $page->isDirty('title'))) {
                $page->slug = $page->generateUniqueSlug();
            }
        });
    }
}
