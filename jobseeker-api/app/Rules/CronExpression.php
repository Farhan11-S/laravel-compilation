<?php

namespace App\Rules;

use Closure;
use Cron\CronExpression as Cron;
use Illuminate\Contracts\Validation\ValidationRule;

class CronExpression implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (Cron::isValidExpression($value) === false) {
            $fail("The $attribute field is not a valid cron expression.");
        }
    }
}
