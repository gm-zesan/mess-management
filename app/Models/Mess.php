<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mess extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'join_code',
        'creator_id',
        'manager_id',
    ];

    /**
     * Get the manager of this mess.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the user who created this mess.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get all members of this mess.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'mess_user')
            ->withPivot('status', 'invited_by_id')
            ->withTimestamps();
    }

    /**
     * Get all mess-user relationships.
     */
    public function messUsers(): HasMany
    {
        return $this->hasMany(MessUser::class);
    }

    /**
     * Get all meals for this mess.
     */
    public function meals(): HasMany
    {
        return $this->hasMany(Meal::class);
    }

    /**
     * Get all expenses for this mess.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Get all deposits for this mess.
     */
    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    /**
     * Get all months for this mess.
     */
    public function months(): HasMany
    {
        return $this->hasMany(Month::class);
    }

    /**
     * Get approved members.
     */
    public function approvedMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'mess_user')
            ->where('mess_user.status', 'approved')
            ->withPivot('status', 'invited_by_id')
            ->withTimestamps();
    }

    /**
     * Get pending members.
     */
    public function pendingMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'mess_user')
            ->where('mess_user.status', 'pending')
            ->withPivot('status', 'invited_by_id')
            ->withTimestamps();
    }

    /**
     * Get managers of this mess.
     */
    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'mess_user')
            ->where('mess_user.role', 'manager')
            ->where('mess_user.status', 'approved')
            ->withPivot('role', 'status', 'invited_by_id')
            ->withTimestamps();
    }

    /**
     * Check if user is manager.
     */
    public function isManager(User $user): bool
    {
        return $this->messUsers()
            ->where('user_id', $user->id)
            ->where('role', 'manager')
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Check if user is approved member.
     */
    public function isApprovedMember(User $user): bool
    {
        return $this->messUsers()
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Generate unique join code.
     */
    public static function generateJoinCode(): string
    {
        do {
            $code = strtoupper(substr(md5(time() . rand()), 0, 8));
        } while (self::where('join_code', $code)->exists());

        return $code;
    }
}
