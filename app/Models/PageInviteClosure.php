<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageInviteClosure extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'page_invite_closure';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;
    protected $keyType = 'string'; // or 'int'
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'ancestor_invite_id',
        'descendant_invite_id',
        'depth',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'depth' => 'integer',
    ];

    /**
     * Get the ancestor page invite.
     */
    public function ancestor(): BelongsTo
    {
        return $this->belongsTo(PageInvite::class, 'ancestor_invite_id');
    }

    /**
     * Get the descendant page invite.
     */
    public function descendant(): BelongsTo
    {
        return $this->belongsTo(PageInvite::class, 'descendant_invite_id');
    }
} 