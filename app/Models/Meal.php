<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'month_id',
        'date',
        'meal_count',
    ];

    protected $casts = [
        'date' => 'date',
        'meal_count' => 'integer',
    ];

    /**
     * Get the member that owns this meal.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the month that owns this meal.
     */
    public function month(): BelongsTo
    {
        return $this->belongsTo(Month::class);
    }
}

