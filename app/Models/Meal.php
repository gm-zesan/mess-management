<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'mess_id',
        'user_id',
        'month_id',
        'date',
        'breakfast_count',
        'lunch_count',
        'dinner_count',
    ];

    protected $casts = [
        'date' => 'date',
        'breakfast_count' => 'float',
        'lunch_count' => 'float',
        'dinner_count' => 'float',
    ];

    /**
     * Get the mess that owns this meal.
     */
    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    /**
     * Get the user that owns this meal.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the month that owns this meal.
     */
    public function month(): BelongsTo
    {
        return $this->belongsTo(Month::class);
    }

    /**
     * Get the total meal count (breakfast + lunch + dinner).
     */
    public function getTotalMealCountAttribute(): float
    {
        return $this->breakfast_count + $this->lunch_count + $this->dinner_count;
    }

}