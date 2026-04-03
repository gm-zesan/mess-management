<?php

namespace App\Exceptions;

use Exception;

class MonthClosedException extends Exception
{
    public function __construct($message = "This month is closed. No further modifications are allowed.")
    {
        parent::__construct($message);
    }

    public function render()
    {
        return response()->view('errors.month-closed', [], 403);
    }
}
