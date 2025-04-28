<?php

namespace App\Controllers;

use App\Core\{Controller, View};
use App\Models\SaleModel;
use App\Services\RBACService;

class SalesController extends Controller
{
    private SaleModel $salesModel;

    public function __construct()
    {
        $this->salesModel = new SaleModel();
    }

    /**
     * Display the sales analytics dashboard with filtered data
     */
    public function index(): void
    {
        // Get filter parameters from request
        $period = $_GET['period'] ?? 'monthly';
        $fromDate = $_GET['from'] ?? '';
        $toDate = $_GET['to'] ?? '';
        $branchId = $_GET['branch'] ?? '';
        $status = $_GET['status'] ?? '';
        $paymentMethod = $_GET['payment'] ?? '';
        $searchQuery = $_GET['q'] ?? '';

        // Determine date range based on period
        $dateRange = $this->getDateRange($period, $fromDate, $toDate);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        // Validate custom date range
        if (!$this->validateDateRange($startDate, $endDate)) {
            $_SESSION['message'] = 'Invalid date range selected';
            $_SESSION['message_type'] = 'error';
            $startDate = date('Y-m-d', strtotime('-30 days'));
            $endDate = date('Y-m-01');
        }

        // Fetch data
        $totalSales = $this->salesModel->getTotalSalesCount($startDate, $endDate, $branchId);
        $salesSummary = $this->salesModel->getSalesSummary($branchId);
        $salesTrend = $this->salesModel->getSalesTrend($startDate, $endDate, $branchId, $period);
        $paymentMethodChart = $this->salesModel->getPaymentMethodDistribution($startDate, $endDate, $branchId);
        $topSellingProducts = $this->salesModel->getTopSellingProducts($startDate, $endDate, $branchId);
        $salesByBranch = $this->salesModel->getSalesByBranch($startDate, $endDate, $branchId);
        $recentSales = $this->salesModel->getRecentSales(
            $startDate,
            $endDate,
            $branchId,
            $status,
            $paymentMethod,
            $searchQuery
        );

        error_log(print_r($recentSales,true));

        // Calculate trends for stats cards
        $previousPeriod = $this->getPreviousPeriodRange($period, $startDate, $endDate);
        $prevSalesSummary = $this->salesModel->getSalesSummary($branchId);
        $prevTotalSales = $this->salesModel->getTotalSalesCount(
            $previousPeriod['start'],
            $previousPeriod['end'],
            $branchId
        );

        $totalSalesTrend = $this->calculateTrend($totalSales, $prevTotalSales);
        $totalsTodayTrend = $this->calculateTrend(
            $salesSummary['today_sales'] ?? 0,
            $prevSalesSummary['today_sales'] ?? 0
        );
        $totalThisWeekTrend = $this->calculateTrend(
            $salesSummary['this_week_sales'] ?? 0,
            $prevSalesSummary['this_week_sales'] ?? 0
        );
        $totalThisMonthTrend = $this->calculateTrend(
            $salesSummary['this_month_sales'] ?? 0,
            $prevSalesSummary['this_month_sales'] ?? 0
        );

        // Render the sales analytics view
        View::renderTemplate('Sales', [
            'title' => 'Sales Analytics',
            'totalSales' => $totalSales,
            'totalsToday' => $salesSummary['today_sales'] ?? 0,
            'totalThisWeek' => $salesSummary['this_week_sales'] ?? 0,
            'totalThisMonth' => $salesSummary['this_month_sales'] ?? 0,
            'salesTrend' => $salesTrend,
            'paymentMethodChart' => $paymentMethodChart,
            'topSellingProducts' => $topSellingProducts,
            'salesByBranch' => $salesByBranch,
            'sales' => $recentSales,
            'totalSalesTrend' => $totalSalesTrend,
            'totalsTodayTrend' => $totalsTodayTrend,
            'totalThisWeekTrend' => $totalThisWeekTrend,
            'totalThisMonthTrend' => $totalThisMonthTrend,
            'currentStatus' => $status,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'searchQuery' => $searchQuery,
            'paymentMethod' => $paymentMethod,
            'period' => $period,
            'branchId' => $branchId
        ]);
    }

    /**
     * Display the sales list page
     */
    public function salesList(): void
    {
        View::renderTemplate('SalesList', [
            'title' => 'Sales List',
        ]);
    }

    /**
     * Calculate date range based on period
     */
    private function getDateRange(string $period, string $fromDate, string $toDate): array
    {
        if ($period === 'custom' && $fromDate && $toDate) {
            return ['start' => $fromDate, 'end' => $toDate];
        }

        switch ($period) {
            case 'daily':
                return ['start' => date('Y-m-d'), 'end' => date('Y-m-d')];
            case 'weekly':
                return ['start' => date('Y-m-d', strtotime('monday this week')), 'end' => date('Y-m-d')];
            case 'monthly':
                return ['start' => date('Y-m-01'), 'end' => date('Y-m-d')];
            case 'yearly':
                return ['start' => date('Y-01-01'), 'end' => date('Y-m-d')];
            default:
                return ['start' => date('Y-m-d', strtotime('-30 days')), 'end' => date('Y-m-d')];
        }
    }

    /**
     * Validate custom date range
     */
    private function validateDateRange(string $startDate, string $endDate): bool
    {
        $start = strtotime($startDate);
        $end = strtotime($endDate);
        return $start && $end && $start <= $end && $start <= time();
    }


    /**
     * Calculate date range for the previous period
     */
    private function getPreviousPeriodRange(string $period, string $startDate, string $endDate): array
    {
        $start = strtotime($startDate);
        $end = strtotime($endDate);
        $duration = $end - $start;

        switch ($period) {
            case 'daily':
                return [
                    'start' => date('Y-m-d', strtotime('-1 day', $start)),
                    'end' => date('Y-m-d', strtotime('-1 day', $end))
                ];
            case 'weekly':
                return [
                    'start' => date('Y-m-d', strtotime('-1 week', $start)),
                    'end' => date('Y-m-d', strtotime('-1 week', $end))
                ];
            case 'monthly':
                return [
                    'start' => date('Y-m-d', strtotime('-1 month', $start)),
                    'end' => date('Y-m-d', strtotime('-1 month', $end))
                ];
            case 'yearly':
                return [
                    'start' => date('Y-m-d', strtotime('-1 year', $start)),
                    'end' => date('Y-m-d', strtotime('-1 year', $end))
                ];
            case 'custom':
                return [
                    'start' => date('Y-m-d', $start - $duration - 86400),
                    'end' => date('Y-m-d', $start - 86400)
                ];
            default:
                return [
                    'start' => date('Y-m-d', strtotime('-30 days', $start)),
                    'end' => date('Y-m-d', strtotime('-30 days', $end))
                ];
        }
    }

    /**
     * Calculate trend percentage and type
     */
    private function calculateTrend(float $currentValue, float $previousValue): array
    {
        if ($previousValue == 0) {
            return [
                'percentage' => $currentValue > 0 ? '+100.0%' : '0.0%',
                'type' => $currentValue > 0 ? 'positive' : 'neutral'
            ];
        }

        $change = $currentValue - $previousValue;
        $percentage = ($change / $previousValue) * 100;
        $formattedPercentage = number_format(abs($percentage), 1) . '%';
        $formattedPercentage = $percentage >= 0 ? '+' . $formattedPercentage : '-' . $formattedPercentage;

        return [
            'percentage' => $formattedPercentage,
            'type' => $percentage >= 0 ? 'positive' : 'negative'
        ];
    }

}