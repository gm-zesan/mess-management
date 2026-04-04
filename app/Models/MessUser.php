<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessUser extends Model
{
    protected $table = 'mess_user';

    protected $fillable = [
        'mess_id',
        'user_id',
        'status',
        'invited_by_id',
    ];

    /**
     * Get the mess this entry belongs to.
     */
    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    /**
     * Get the user this entry belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who invited this member.
     */
    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_id');
    }

    /**
     * Approve this mess user.
     */
    public function approve(): void
    {
        $this->update(['status' => 'approved']);
    }

    /**
     * Reject this mess user.
     */
    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }
}
