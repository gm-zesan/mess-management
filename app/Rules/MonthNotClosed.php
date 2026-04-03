<?php

namespace App\Rules;

use Closure;
use App\Models\Month;
use Illuminate\Contracts\Validation\ValidationRule;

class MonthNotClosed implements ValidationRule
{
    private $monthId;

    public function __construct($monthId = null)
    {
        $this->monthId = $monthId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $monthId = $this->monthId ?? $value;
        
        if (!$monthId) {
            return;
        }

        $month = Month::find($monthId);

        if ($month && $month->isClosed()) {
            $fail("The {$attribute} is closed. No further modifications are allowed.");
        }
    }
}
