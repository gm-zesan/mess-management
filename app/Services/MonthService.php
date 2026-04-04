<?php

namespace App\Services;

use App\Models\Month;
use App\Enums\MonthStatusEnum;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MonthService
{
    /**
     * Get the currently active month.
     * Throws ModelNotFoundException if no active month exists.
     *
     * @return Month
     * @throws ModelNotFoundException
     */
    public function getActiveMonth($messId = null): Month
    {
        $query = Month::where('status', MonthStatusEnum::ACTIVE->value);
        
        if ($messId) {
            $query->where('mess_id', $messId);
        }
        
        return $query->firstOrFail();
    }

    /**
     * Get the active month or return null if not found.
     *
     * @param int|null $messId Filter by mess ID
     * @return Month|null
     */
    public function getActiveMonthOrNull($messId = null): ?Month
    {
        $query = Month::where('status', MonthStatusEnum::ACTIVE->value);
        
        if ($messId) {
            $query->where('mess_id', $messId);
        }
        
        return $query->first();
    }

    /**
     * Check if an active month exists.
     *
     * @param int|null $messId Filter by mess ID
     * @return bool
     */
    public function hasActiveMonth($messId = null): bool
    {
        $query = Month::where('status', MonthStatusEnum::ACTIVE->value);
        
        if ($messId) {
            $query->where('mess_id', $messId);
        }
        
        return $query->exists();
    }

    /**
     * Ensure only one month is active at a time.
     * Deactivates other months when activating a new one.
     *
     * @param Month $month
     * @return Month
     */
    public function activateMonth(Month $month): Month
    {
        // Deactivate all other months
        Month::where('id', '!=', $month->id)
            ->where('status', MonthStatusEnum::ACTIVE->value)
            ->update(['status' => MonthStatusEnum::CLOSED->value]);

        // Activate this month
        $month->update(['status' => MonthStatusEnum::ACTIVE->value]);

        return $month;
    }

    /**
     * Close a month and prevent further modifications.
     *
     * @param Month $month
     * @return Month
     */
    public function closeMonth(Month $month): Month
    {
        $month->update([
            'status' => MonthStatusEnum::CLOSED->value,
            'closed_at' => now(),
        ]);

        return $month;
    }

    /**
     * Check if a month is closed.
     *
     * @param int|Month $month
     * @return bool
     */
    public function isClosed($month): bool
    {
        $monthId = $month instanceof Month ? $month->id : $month;
        $monthModel = Month::find($monthId);
        
        return $monthModel && $monthModel->closed_at !== null;
    }

    /**
     * Check if a month is open (not closed).
     *
     * @param int|Month $month
     * @return bool
     */
    public function isOpen($month): bool
    {
        return !$this->isClosed($month);
    }

    /**
     * Close the active month.
     *
     * @return Month|null
     */
    public function closeActiveMonth(): ?Month
    {
        $activeMonth = $this->getActiveMonthOrNull();
        
        if ($activeMonth) {
            return $this->closeMonth($activeMonth);
        }

        return $activeMonth;
    }
}
