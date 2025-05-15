<?php

namespace App\Helper;

class Helper
{
    public static function utcstrtotime(string $str)
    {
        return strtotime($str . ' + 7 hours');
    }

    public static function filterPreset($query, $filterPreset)
    {
        match (strtolower($filterPreset)) {
            'today' => $query->whereDate('created_at', now()->toDateString()),
            'this_week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'this_month' => $query->whereMonth('created_at', now()->month),
            'this_year' => $query->whereYear('created_at', now()->year),
            default => null,
        };
    }
}
