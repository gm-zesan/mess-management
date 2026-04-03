<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Month extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'start_date', 'end_date', 'status', 'closed_at'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'closed_at' => 'datetime',
    ];

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
}
