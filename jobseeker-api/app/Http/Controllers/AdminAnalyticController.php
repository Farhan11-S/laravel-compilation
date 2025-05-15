<?php

namespace App\Http\Controllers;

use AkkiIo\LaravelGoogleAnalytics\LaravelGoogleAnalytics;
use AkkiIo\LaravelGoogleAnalytics\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminAnalyticController extends Controller
{
    public function query(Request $request)
    {
        $query = $request->query();
        $period = $query['period'] ?? 30;
        $startDate = $query['startDate'] ?? null;
        $endDate = $query['endDate'] ?? null;
        $metrics = $query['metrics'] ?? '';
        $dimensions = $query['dimensions'] ?? '';
        $orderByMetricName = $query['orderByMetricName'] ?? null;
        $orderByMetric = $query['orderByMetric'] ?? 'ASC';
        $orderByDimensionName = $query['orderByDimensionName'] ?? null;
        $orderByDimension = $query['orderByDimension'] ?? 'ASC';
        $keepEmptyRows = $query['keepEmptyRows'] ?? false;
        $limit = $query['limit'] ?? 0;
        $filterByDimensionName = $query['filterByDimensionName'] ?? 0;
        $filterByDimensionValue = $query['filterByDimensionValue'] ?? 0;
        $filterByDimensionMatchType = $query['filterByDimensionMatchType'] ?? 1;

        $metrics = explode(',', $metrics);
        $dimensions = explode(',', $dimensions);

        $analytics = new LaravelGoogleAnalytics;

        if (!empty($startDate) && !empty($endDate)) {
            $analytics->dateRange(new Period(
                Carbon::today()->subDays($startDate)->startOfDay(),
                Carbon::today()->subDays($endDate)->startOfDay()
            ));
        } else {
            $analytics->dateRange(Period::days($period));
        }

        $analytics->metrics(...$metrics)
            ->dimensions(...$dimensions)
            ->keepEmptyRows($keepEmptyRows)
            ->limit($limit);
        if (!empty($orderByDimensionName)) {
            $analytics->orderByDimension($orderByDimensionName, $orderByDimension);
        }

        if (!empty($orderByMetricName)) {
            $analytics->orderByMetric($orderByMetricName, $orderByMetric);
        }

        if (!empty($filterByDimensionName) && !empty($filterByDimensionValue)) {
            $analytics->whereDimension($filterByDimensionName, $filterByDimensionMatchType, $filterByDimensionValue);
        }

        return [
            'table' => $analytics->get()
                ->table
        ];
    }

    public function compareThisMonthAndLastMonth(Request $request)
    {
        $query = $request->query();
        $metrics = $query['metrics'] ?? '';
        $dimensions = $query['dimensions'] ?? '';
        $orderByMetricName = $query['orderByMetricName'] ?? null;
        $orderByMetric = $query['orderByMetric'] ?? 'ASC';
        $orderByDimensionName = $query['orderByDimensionName'] ?? null;
        $orderByDimension = $query['orderByDimension'] ?? 'ASC';
        $keepEmptyRows = $query['keepEmptyRows'] ?? false;
        $limit = $query['limit'] ?? 0;
        $filterByDimensionName = $query['filterByDimensionName'] ?? 0;
        $filterByDimensionValue = $query['filterByDimensionValue'] ?? 0;
        $filterByDimensionMatchType = $query['filterByDimensionMatchType'] ?? 1;

        $metrics = explode(',', $metrics);
        $dimensions = explode(',', $dimensions);

        $analytics = new LaravelGoogleAnalytics;

        $analytics->dateRanges(new Period(
            Carbon::today()->subDays(30)->startOfDay(),
            Carbon::today()->subDays()->startOfDay()
        ), new Period(
            Carbon::today()->subDays(60)->startOfDay(),
            Carbon::today()->subDays(30)->startOfDay()
        ));

        $analytics->metrics(...$metrics)
            ->dimensions(...$dimensions)
            ->keepEmptyRows($keepEmptyRows)
            ->limit($limit);
        if (!empty($orderByDimensionName)) {
            $analytics->orderByDimension($orderByDimensionName, $orderByDimension);
        }

        if (!empty($orderByMetricName)) {
            $analytics->orderByMetric($orderByMetricName, $orderByMetric);
        }

        if (!empty($filterByDimensionName) && !empty($filterByDimensionValue)) {
            $analytics->whereDimension($filterByDimensionName, $filterByDimensionMatchType, $filterByDimensionValue);
        }

        return [
            'table' => $analytics->get()
                ->table
        ];
    }
}
