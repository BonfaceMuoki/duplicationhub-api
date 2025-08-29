<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PageInviteLinkShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id',
        'page_invite_id',
        'user_page_link',
        'personal_message',
        'registration_status',
        'notes',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'registration_status' => 'string'
    ];

    /**
     * Get the page that this link share belongs to.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Get the page invite that this link share belongs to.
     */
    public function pageInvite(): BelongsTo
    {
        return $this->belongsTo(PageInvite::class);
    }

    /**
     * Get the registration status as a human-readable string.
     */
    public function getRegistrationStatusTextAttribute(): string
    {
        return ucfirst($this->registration_status);
    }

    /**
     * Check if the registration is pending.
     */
    public function isPending(): bool
    {
        return $this->registration_status === 'pending';
    }

    /**
     * Check if the registration is completed.
     */
    public function isCompleted(): bool
    {
        return $this->registration_status === 'completed';
    }

    /**
     * Check if the registration failed.
     */
    public function isFailed(): bool
    {
        return $this->registration_status === 'failed';
    }

    /**
     * Check if the user has registered.
     */
    public function hasRegistered(): bool
    {
        return $this->registration_status === 'registered';
    }
}
