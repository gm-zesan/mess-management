<?php

namespace App\Models;

use App\Enums\MonthStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Month extends Model
{
    use HasFactory;

    protected $fillable = ['mess_id', 'name', 'start_date', 'end_date', 'status', 'closed_at'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => MonthStatusEnum::class,
        'closed_at' => 'datetime',
    ];

    /**
     * Get the mess that owns this month.
     */
    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    /**
     * Get the meals for this month.
     */
    public function meals(): HasMany
    {
        return $this->hasMany(Meal::class);
    }

    /**
     * Get the expenses for this month.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Get the deposits for this month.
     */
    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    /**
     * Check if the month is closed.
     */
    public function isClosed(): bool
    {
        return $this->closed_at !== null;
    }

    /**
     * Check if the month is open.
     */
    public function isOpen(): bool
    {
        return !$this->isClosed();
    }

    /**
     * Scope to filter months by mess.
     */
    public function scopeByMess(Builder $query, $messId): Builder
    {
        return $query->where('mess_id', $messId);
    }

    /**
     * Scope to get active months.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', MonthStatusEnum::ACTIVE->value);
    }

    /**
     * Scope to get closed months.
     */
    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', MonthStatusEnum::CLOSED->value);
    }

    /**
     * Scope to get months by mess and status.
     */
    public function scopeByMessAndStatus(Builder $query, $messId, $status): Builder
    {
        return $query->where('mess_id', $messId)
                     ->where('status', $status);
    }
}
