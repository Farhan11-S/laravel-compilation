<?php

namespace App\Constants;

class JobTypes
{
    public const JOB_TYPES = [
        'full-time',
        'part-time',
        'temporary',
        'contract',
        'internship',
        'volunteer',
        'fresh-graduate',
        'subcontract',
        'permanent',
    ];

    public static function isProperJobType($value): mixed
    {
        return in_array($value, self::JOB_TYPES);
    }
}
