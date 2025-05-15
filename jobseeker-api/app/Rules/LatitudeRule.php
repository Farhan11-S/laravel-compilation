<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class LatitudeRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $regex = '/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/';
        if (!preg_match($regex, $value)) {
            $fail('The :attribute is not valid.');
        }
    }
}
