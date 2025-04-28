<?php

namespace App\Controllers;

use App\Core\{Controller, View};
use App\Models\{ReportModel, BranchModel};

class ReportController extends Controller
{
    private ReportModel $reportModel;

    public function __construct()
    {
        $this->reportModel = new ReportModel();
    }


    public function index(): void
    {
        $reportType = $_GET['report_type'] ?? 'sales';
        $timePeriod = $_GET['time_period'] ?? 'this_month';
        
        if ($timePeriod === 'custom') {
            $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
            $endDate = $_GET['end_date'] ?? date('Y-m-d');
        } else {
            $dateRange = $this->getDateRange($timePeriod);
            $startDate = $dateRange['start'];
            $endDate = $dateRange['end'];
        }

        if ($timePeriod === 'custom' && !$this->validateDateRange($startDate, $endDate)) {
            $_SESSION['message'] = 'Invalid date range selected';
            $_SESSION['message_type'] = 'error';
            $startDate = date('Y-m-d', strtotime('-30 days'));
            $endDate = date('Y-m-d');
        }

        $reportTypes = [
            'sales' => 'Sales Report',
            'inventory' => 'Inventory Report',
            'orders' => 'Purchase Orders',
            'batch_expiry' => 'Batch Expiry Report'
        ];

        $timePeriods = [
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            'this_week' => 'This Week',
            'last_week' => 'Last Week',
            'this_month' => 'This Month',
            'last_month' => 'Last Month',
            'this_year' => 'This Year',
            'last_year' => 'Last Year',
            'custom' => 'Custom Range'
        ];

        $expiryTimePeriods = [
            'next_week' => 'Next Week',
            'next_month' => 'Next Month'
        ];

        $data = $this->fetchReportData($reportType, $timePeriod, $startDate, $endDate);

        error_log(print_r($data['salesData'],true));

        View::renderTemplate('Reports', [
            'title' => 'Reports & Analytics',
            'reportTypes' => $reportTypes,
            'timePeriods' => $timePeriods,
            'expiryTimePeriods' => $expiryTimePeriods,
            'selectedReportType' => $reportType,
            'selectedTimePeriod' => $timePeriod,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'kpiMetrics' => $data['kpiMetrics'],
            'topSellingProducts' => $data['topSellingProducts'],
            'salesData' => $data['salesData'],
            'stockStatus' => $data['stockStatus'],
            'recentPurchaseOrders' => $data['recentPurchaseOrders'],
            'categoryData' => $data['categoryData'],
            'supplierPerformance' => $data['supplierPerformance'],
            'categoryRevenueData' => $data['categoryRevenueData'],
            'expiringBatches' => $data['expiringBatches'],
            'lowStockItems' => $data['lowStockItems'],
            'monthlySalesData' => $data['monthlySalesData'],
            'countAndRevenue' => $data['countAndRevenue'],
            'salesStats' => $data['salesStats'],
            'inventoryStats' => $data['inventoryStats'],
            'categoryStats' => $data['categoryStats'],
            'basketAnalysisResults' => $data['basketAnalysisResults']

        ]);
    }

    private function fetchReportData(string $reportType, string $timePeriod, string $startDate, string $endDate): array
    {
        $topSellingProducts = $this->getTopSellingProducts($startDate, $endDate);
        $salesData = $this->getSalesData($startDate, $endDate);
        $countAndRevenue = $this->getCountAndRevenue($startDate, $endDate);
        $stockStatus = $this->getStockStatus();
        $recentPurchaseOrders = $this->getRecentPurchaseOrders($startDate, $endDate);
        $categoryData = $this->getCategoryData();
        $supplierPerformance = $this->getSupplierPerformance($startDate, $endDate);
        $categoryRevenueData = $this->getCategoryRevenueData($startDate, $endDate);
        $expiringBatches = $this->getExpiringBatches($startDate, $endDate);
        $lowStockItems = $this->getLowStockItems();
        $monthlySalesData = $this->getMonthlySalesData($startDate, $endDate);

        $salesStats = $this->getSalesStats($startDate, $endDate);
        $inventoryStats = $this->getInventoryStats();
        $categoryStats = $this->getCategoryStats();

        $previousPeriod = $this->getPreviousPeriodRange($timePeriod, $startDate, $endDate);

        $previousCountAndRevenue = $this->getCountAndRevenue($previousPeriod['start'], $previousPeriod['end']);
        $previousProfitMargin = $this->getProfitMargin($previousPeriod['start'], $previousPeriod['end']);

        $currentRevenue = $countAndRevenue['revenue'] ?? 0;
        $currentOrderCount = $countAndRevenue['order_count'] ?? 0;
        $currentAvgOrderValue = $currentOrderCount > 0 ? $currentRevenue / $currentOrderCount : 0;
        $currentProfitMargin = $this->getProfitMargin($startDate, $endDate);

        $previousRevenue = $previousCountAndRevenue['revenue'] ?? 0;
        $previousOrderCount = $previousCountAndRevenue['order_count'] ?? 0;
        $previousAvgOrderValue = $previousOrderCount > 0 ? $previousRevenue / $previousOrderCount : 0;

        $revenueTrend = $this->calculateTrend($currentRevenue, $previousRevenue);
        $orderCountTrend = $this->calculateTrend($currentOrderCount, $previousOrderCount);
        $avgOrderValueTrend = $this->calculateTrend($currentAvgOrderValue, $previousAvgOrderValue);
        $profitMarginTrend = $this->calculateTrend($currentProfitMargin, $previousProfitMargin);

        $kpiMetrics = [
            [
                'label' => 'Total Sales',
                'value' => 'LKR ' . number_format($currentRevenue, 2),
                'trend' => $revenueTrend['percentage'],
                'trend_type' => $revenueTrend['type'],
                'icon' => 'payments',
                'type' => 'primary'
            ],
            [
                'label' => 'Total Orders',
                'value' => $currentOrderCount,
                'trend' => $orderCountTrend['percentage'],
                'trend_type' => $orderCountTrend['type'],
                'icon' => 'shopping_cart',
                'type' => 'success'
            ],
            [
                'label' => 'Average Order Value',
                'value' => 'LKR ' . number_format($currentAvgOrderValue, 2),
                'trend' => $avgOrderValueTrend['percentage'],
                'trend_type' => $avgOrderValueTrend['type'],
                'icon' => 'inventory',
                'type' => 'accent'
            ],
            [
                'label' => 'Profit Margin',
                'value' => number_format($currentProfitMargin, 1) . '%',
                'trend' => $profitMarginTrend['percentage'],
                'trend_type' => $profitMarginTrend['type'],
                'icon' => 'trending_up',
                'type' => 'warning'
            ]
        ];

        $basketAnalysisResults = $this->reportModel->getTopProductCombinations($startDate,$endDate);

        return [
            'kpiMetrics' => $kpiMetrics,
            'topSellingProducts' => $topSellingProducts,
            'salesData' => $salesData,
            'stockStatus' => $stockStatus,
            'recentPurchaseOrders' => $recentPurchaseOrders,
            'categoryData' => $categoryData,
            'supplierPerformance' => $supplierPerformance,
            'categoryRevenueData' => $categoryRevenueData,
            'expiringBatches' => $expiringBatches,
            'lowStockItems' => $lowStockItems,
            'monthlySalesData' => $monthlySalesData,
            'countAndRevenue' => $countAndRevenue,
            'salesStats' => $salesStats,
            'inventoryStats' => $inventoryStats,
            'categoryStats' => $categoryStats,
            'basketAnalysisResults' => $basketAnalysisResults
        ];
    }


    private function getTopSellingProducts(string $startDate, string $endDate): array
    {
        return $this->reportModel->getTopSellingProducts($startDate, $endDate);
    }


    private function getSalesData(string $startDate, string $endDate): array
    {
        return $this->reportModel->getSalesData($startDate, $endDate);
    }


    private function getCountAndRevenue(string $startDate, string $endDate): array
    {
        return $this->reportModel->getCountAndRevenue($startDate, $endDate);
    }

    private function getStockStatus(): array
    {
        return $this->reportModel->getStockStatus();
    }


    private function getRecentPurchaseOrders(string $startDate, string $endDate): array
    {
        return $this->reportModel->getRecentPurchaseOrders($startDate, $endDate);
    }

    private function getCategoryData(): array
    {
        return $this->reportModel->getCategoryData();
    }


    private function getSupplierPerformance(string $startDate, string $endDate): array
    {
        return $this->reportModel->getSupplierPerformance($startDate, $endDate);
    }


    private function getCategoryRevenueData(string $startDate, string $endDate): array
    {
        return $this->reportModel->getCategoryRevenueData($startDate, $endDate);
    }


    private function getExpiringBatches(string $startDate, string $endDate): array
    {
        return $this->reportModel->getExpiringBatches($startDate, $endDate);
    }


    private function getLowStockItems(): array
    {
        return $this->reportModel->getLowStock();
    }


    private function getMonthlySalesData(string $startDate, string $endDate): array
    {
        return $this->reportModel->getMonthlySalesData($startDate, $endDate);
    }


    private function getSalesStats(string $startDate, string $endDate): array
    {
        return $this->reportModel->getSalesStats($startDate, $endDate);
    }


    private function getInventoryStats(): array
    {
        return $this->reportModel->getInventoryStats();
    }


    private function getCategoryStats(): array
    {
        return $this->reportModel->getCategoryStats();
    }


    private function getProfitMargin(string $startDate, string $endDate): float
    {
        return $this->reportModel->getProfitMargin($startDate, $endDate);
    }


    private function getPreviousPeriodRange(string $timePeriod, string $startDate, string $endDate): array
    {
        $start = strtotime($startDate);
        $end = strtotime($endDate);
        $duration = $end - $start;

        switch ($timePeriod) {
            case 'today':
                return [
                    'start' => date('Y-m-d', strtotime('-1 day', $start)),
                    'end' => date('Y-m-d', strtotime('-1 day', $end))
                ];
            case 'yesterday':
                return [
                    'start' => date('Y-m-d', strtotime('-2 days', $start)),
                    'end' => date('Y-m-d', strtotime('-2 days', $end))
                ];
            case 'this_week':
            case 'last_week':
                return [
                    'start' => date('Y-m-d', strtotime('-1 week', $start)),
                    'end' => date('Y-m-d', strtotime('-1 week', $end))
                ];
            case 'this_month':
            case 'last_month':
                return [
                    'start' => date('Y-m-d', strtotime('-1 month', $start)),
                    'end' => date('Y-m-d', strtotime('-1 month', $end))
                ];
            case 'this_year':
            case 'last_year':
                return [
                    'start' => date('Y-m-d', strtotime('-1 year', $start)),
                    'end' => date('Y-m-d', strtotime('-1 year', $end))
                ];
            case 'custom':
                return [
                    'start' => date('Y-m-d', $start - $duration - 86400),
                    'end' => date('Y-m-d', $start - 86400)
                ];
            case 'next_week':
            case 'next_month':
                return [
                    'start' => date('Y-m-d', strtotime('-1 month', $start)),
                    'end' => date('Y-m-d', strtotime('-1 month', $end))
                ];
            default:
                return [
                    'start' => date('Y-m-d', strtotime('-30 days', $start)),
                    'end' => date('Y-m-d', strtotime('-30 days', $end))
                ];
        }
    }


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


    private function getDateRange(string $timePeriod): array
    {
        switch ($timePeriod) {
            case 'today':
                return ['start' => date('Y-m-d'), 'end' => date('Y-m-d')];
            case 'yesterday':
                return ['start' => date('Y-m-d', strtotime('-1 day')), 'end' => date('Y-m-d', strtotime('-1 day'))];
            case 'this_week':
                return ['start' => date('Y-m-d', strtotime('monday this week')), 'end' => date('Y-m-d')];
            case 'last_week':
                return ['start' => date('Y-m-d', strtotime('monday last week')), 'end' => date('Y-m-d', strtotime('sunday last week'))];
            case 'this_month':
                return ['start' => date('Y-m-01'), 'end' => date('Y-m-d')];
            case 'last_month':
                return ['start' => date('Y-m-01', strtotime('first day of last month')), 'end' => date('Y-m-t', strtotime('last month'))];
            case 'this_year':
                return ['start' => date('Y-01-01'), 'end' => date('Y-m-d')];
            case 'last_year':
                return ['start' => date('Y-01-01', strtotime('last year')), 'end' => date('Y-12-31', strtotime('last year'))];
            case 'next_week':
                return ['start' => date('Y-m-d'), 'end' => date('Y-m-d', strtotime('+7 days'))];
            case 'next_month':
                return ['start' => date('Y-m-d'), 'end' => date('Y-m-d', strtotime('+1 month'))];
            default:
                return ['start' => date('Y-m-d', strtotime('-30 days')), 'end' => date('Y-m-d')];
        }
    }


    private function validateDateRange(string $startDate, string $endDate): bool
    {
        $start = strtotime($startDate);
        $end = strtotime($endDate);
        return $start && $end && $start <= $end && $start <= time();
    }
}