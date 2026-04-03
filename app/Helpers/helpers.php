<?php

/**
 * Get the active month ID.
 * 
 * @return int|null
 */
function activeMonthId()
{
    try {
        return app(App\Services\MonthService::class)->getActiveMonth()->id;
    } catch (\Exception $e) {
        return null;
    }
}

/**
 * Get the active month.
 * 
 * @return \App\Models\Month|null
 */
function activeMonth()
{
    try {
        return app(App\Services\MonthService::class)->getActiveMonth();
    } catch (\Exception $e) {
        return null;
    }
}

/**
 * Check if a month is closed.
 * 
 * @param int|\App\Models\Month $month
 * @return bool
 */
function isMonthClosed($month)
{
    return app(App\Services\MonthService::class)->isClosed($month);
}
