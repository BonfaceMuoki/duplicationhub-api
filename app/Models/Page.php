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

        'views' => 'integer',
        'sort_order' => 'integer',
        'allocation_weight' => 'integer',
        'rate_limit_per_ip_per_day' => 'integer',

        'publish_at' => 'datetime',
        'unpublish_at' => 'datetime',

        'allowed_join_domains' => 'array',
    ];

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
}
