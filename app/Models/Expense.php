<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = ['mess_id', 'month_id', 'user_id', 'category', 'amount', 'date', 'note'];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function month(): BelongsTo
    {
        return $this->belongsTo(Month::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter expenses by mess.
     */
    public function scopeByMess(Builder $query, $messId): Builder
    {
        return $query->where('mess_id', $messId);
    }

    /**
     * Scope to filter expenses by month.
     */
    public function scopeByMonth(Builder $query, $monthId): Builder
    {
        return $query->where('month_id', $monthId);
    }

    /**
     * Scope to filter expenses by mess and month.
     */
    public function scopeByMessAndMonth(Builder $query, $messId, $monthId): Builder
    {
        return $query->where('mess_id', $messId)
                     ->where('month_id', $monthId);
    }
}
