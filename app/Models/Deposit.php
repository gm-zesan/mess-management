<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use App\Scopes\MessTenantScope;

class Deposit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mess_id',
        'user_id',
        'month_id',
        'amount',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * The "booted" method of the model.
     * Register global scopes.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new MessTenantScope());
    }

    public function mess(): BelongsTo
    {
        return $this->belongsTo(Mess::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function month(): BelongsTo
    {
        return $this->belongsTo(Month::class);
    }

    /**
     * Scope to filter deposits by mess.
     */
    public function scopeByMess(Builder $query, $messId): Builder
    {
        return $query->where('mess_id', $messId);
    }

    /**
     * Scope to filter deposits by month.
     */
    public function scopeByMonth(Builder $query, $monthId): Builder
    {
        return $query->where('month_id', $monthId);
    }

    /**
     * Scope to filter deposits by mess and month.
     */
    public function scopeByMessAndMonth(Builder $query, $messId, $monthId): Builder
    {
        return $query->where('mess_id', $messId)
                     ->where('month_id', $monthId);
    }
}
