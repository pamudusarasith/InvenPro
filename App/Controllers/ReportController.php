<?php

namespace App\Controllers;

use App\Core\{Controller, View};
use App\Models\{ReportModel, BranchModel};

class ReportController extends Controller
{
    /**
     * Display the reports dashboard with filtered data
     */
    public function index(): void
    {
        // Get filter parameters
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
        // Validate custom date range
        if ($timePeriod === 'custom' && !$this->validateDateRange($startDate, $endDate)) {
            $_SESSION['message'] = 'Invalid date range selected';
            $_SESSION['message_type'] = 'error';
            $startDate = date('Y-m-d', strtotime('-30 days'));
            $endDate = date('Y-m-d');
        }

        // Define report types and time periods
        $reportTypes = [
            'sales' => 'Sales Report',
            'inventory' => 'Inventory Report',
            'suppliers' => 'Supplier Performance',
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


        // Fetch data based on filters
        $data = $this->fetchReportData($reportType, $timePeriod, $startDate, $endDate);

        // Render the reports view
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
            'categoryStats' => $data['categoryStats']
        ]);
    }

    /**
     * Fetch report data based on filters
     */
    private function fetchReportData(string $reportType, string $timePeriod, string $startDate, string $endDate): array
    {

        $reportModel = new ReportModel();

        $topSellingProducts = $reportModel->getTopSellingProducts($startDate, $endDate);
        //error_log('Top Selling Products: ' . print_r($topSellingProducts, true)); // Log the data for debugging


        // $topSellingProducts = [
        //     ['product_name' => 'Organic Ceylon Tea 250g', 'quantity' => 152, 'revenue' => 'LKR 45,600.00'],
        //     ['product_name' => 'Fresh Milk 1L', 'quantity' => 135, 'revenue' => 'LKR 33,750.00'],
        //     ['product_name' => 'Whole Wheat Bread 700g', 'quantity' => 124, 'revenue' => 'LKR 24,800.00'],
        //     ['product_name' => 'Free-Range Eggs (12pk)', 'quantity' => 103, 'revenue' => 'LKR 20,600.00'],
        //     ['product_name' => 'Basmati Rice 5kg', 'quantity' => 89, 'revenue' => 'LKR 44,500.00']
        // ];

        $salesData = $reportModel->getSalesData($startDate, $endDate);
        $countAndRevenue = $reportModel->getCountAndRevenue($startDate, $endDate);
        $countAndRevenue['avg'] = $countAndRevenue['count'] > 0 ? number_format($countAndRevenue['revenue'] / $countAndRevenue['count'], 2) : '0.00';
        error_log('Count and Revenue: ' . print_r($countAndRevenue, true)); // Log the data for debugging

        // error_log('Sales Data: ' . print_r($salesData, true)); // Log the data for debugging

        // $dailySalesData = [
        //     ['date' => 'Apr 01', 'sales' => 12500],
        //     ['date' => 'Apr 05', 'sales' => 17800],
        //     ['date' => 'Apr 10', 'sales' => 14300],
        //     ['date' => 'Apr 15', 'sales' => 21000],
        //     ['date' => 'Apr 20', 'sales' => 15600],
        //     ['date' => 'Apr 25', 'sales' => 19200],
        //];

        $stockStatus = $reportModel->getstockStatus($startDate, $endDate);

        //$stockStatus = [
        //    'in_stock' => 268,
        //    'low_stock' => 43,
        //    'out_of_stock' => 17,
        //    'total_value' => 'LKR 'number_format(); 
        //];

        $recentPurchaseOrders = [
            ['reference' => 'PO-20250420-12345', 'supplier' => 'Ceylon Tea Suppliers', 'date' => '2025-04-20', 'status' => 'completed', 'total' => 'LKR 125,000.00'],
            ['reference' => 'PO-20250418-12344', 'supplier' => 'Fresh Farm Dairies', 'date' => '2025-04-18', 'status' => 'open', 'total' => 'LKR 87,500.00'],
            ['reference' => 'PO-20250415-12343', 'supplier' => 'Organic Grains Ltd', 'date' => '2025-04-15', 'status' => 'completed', 'total' => 'LKR 103,750.00'],
            ['reference' => 'PO-20250410-12342', 'supplier' => 'Island Rice Mills', 'date' => '2025-04-10', 'status' => 'completed', 'total' => 'LKR 145,000.00'],
            ['reference' => 'PO-20250405-12341', 'supplier' => 'Global Spice Traders', 'date' => '2025-04-05', 'status' => 'canceled', 'total' => 'LKR 76,250.00']
        ];

        $categoryData = $reportModel->getCategoryData();
        //error_log('Category Data: ' . print_r($categoryData, true)); // Log the data for debugging

        //$categoryData = [
        //    ['name' => 'Beverages', 'count' => 42],
        //    ['name' => 'Dairy', 'count' => 38],
        //    ['name' => 'Bakery', 'count' => 24],
        //   ['name' => 'Grains', 'count' => 19],
        //    ['name' => 'Spices', 'count' => 31]
        //];

        // $supplierPerformance1 = $reportModel->getSupplierPerformance($startDate, $endDate);
        // error_log('Supplier Performance: ' . print_r($supplierPerformance1, true)); // Log the data for debugging

        $supplierPerformance = [
            ['name' => 'Ceylon Tea Suppliers', 'on_time' => 92, 'quality' => 88],
            ['name' => 'Fresh Farm Dairies', 'on_time' => 85, 'quality' => 95],
            ['name' => 'Organic Grains Ltd', 'on_time' => 78, 'quality' => 92],
            ['name' => 'Island Rice Mills', 'on_time' => 90, 'quality' => 85],
            ['name' => 'Global Spice Traders', 'on_time' => 72, 'quality' => 90]
        ];

        $categoryRevenueData = $reportModel->getCategoryRevenueData($startDate, $endDate);
        $totalRevenueByCategory = array_sum(array_column($categoryRevenueData, 'revenue'));
        $categoryStats['avgRevByCat'] = $totalRevenueByCategory / (count($categoryRevenueData) > 0 ? count($categoryRevenueData) : 1);
        $categoryStats['catCount'] = count($categoryData);
        $categoryStats['catTotalProducts'] = array_sum(array_column($categoryData, 'count'));
        $categoryStats['catAvgProducts'] = (float)$categoryStats['catTotalProducts'] / ((int)$categoryStats['catCount'] > 0 ? (int)$categoryStats['catCount'] : 1);

        error_log('Category Revenue Data: ' . print_r($categoryRevenueData, true)); // Log the data for debugging

        // $categoryRevenueData = [
        //    ['name' => 'Beverages', 'revenue' => 145000],
        //     ['name' => 'Dairy', 'revenue' => 98000],
        //     ['name' => 'Bakery', 'revenue' => 76500],
        //     ['name' => 'Grains', 'revenue' => 68000],
        //     ['name' => 'Spices', 'revenue' => 39850]
        // ];

        $expiringBatches = $reportModel->getExpiringBatches($startDate, $endDate);
        //error_log('Expiring Batches: ' . print_r($expiringBatches, true)); // Log the data for debugging

        //$expiringBatches = [
        //   ['product_name' => 'Fresh Milk 1L', 'batch_code' => 'FM2504001', 'expiry_date' => '2025-05-15', 'quantity' => 45, 'days_left' => 20],
        //    ['product_name' => 'Yogurt 500g', 'batch_code' => 'YG2504002', 'expiry_date' => '2025-05-10', 'quantity' => 36, 'days_left' => 15],
        //    ['product_name' => 'Cottage Cheese 250g', 'batch_code' => 'CC2504003', 'expiry_date' => '2025-05-07', 'quantity' => 24, 'days_left' => 12],
        //    ['product_name' => 'Whole Wheat Bread 700g', 'batch_code' => 'WWB2504001', 'expiry_date' => '2025-05-03', 'quantity' => 18, 'days_left' => 8],
        //    ['product_name' => 'Organic Butter 200g', 'batch_code' => 'OB2504001', 'expiry_date' => '2025-05-08', 'quantity' => 12, 'days_left' => 13]
        //];

        $lowStockItems = $reportModel->getLowStock();
        //error_log('Low Stock Items: ' . print_r($lowStockItems, true)); // Log the data for debugging

        //$lowStockItems = [
        //    ['product_name' => 'Basmati Rice 5kg', 'current_stock' => 8, 'reorder_level' => 15, 'days_to_out' => 6],
        //    ['product_name' => 'Ceylon Black Tea 250g', 'current_stock' => 12, 'reorder_level' => 20, 'days_to_out' => 5],
        //    ['product_name' => 'Coconut Oil 1L', 'current_stock' => 6, 'reorder_level' => 12, 'days_to_out' => 4],
        //    ['product_name' => 'Brown Sugar 1kg', 'current_stock' => 10, 'reorder_level' => 18, 'days_to_out' => 7],
        //    ['product_name' => 'Curry Powder 200g', 'current_stock' => 5, 'reorder_level' => 15, 'days_to_out' => 3]
        //];

        $monthlySalesData = [];
        for ($i = 1; $i <= 30; $i++) {
            $date = sprintf('Apr %02d', $i);
            $sales = rand(8000, 22000);
            $monthlySalesData[] = ['date' => $date, 'sales' => $sales];
        }

        // Sample data from the view
        $kpiMetrics = [
            [
                'label' => 'Total Sales',
                'value' => 'LKR '.number_format($countAndRevenue['revenue'], 2),
                'trend' => '+12.5%',
                'trend_type' => 'positive',
                'icon' => 'payments',
                'type' => 'primary'
            ],
            [
                'label' => 'Total Orders',
                'value' => '142',
                'trend' => '+8.3%',
                'trend_type' => 'positive',
                'icon' => 'shopping_cart',
                'type' => 'success'
            ],
            [
                'label' => 'Average Order Value',
                'value' => 'LKR 3,009.51',
                'trend' => '+4.2%',
                'trend_type' => 'positive',
                'icon' => 'inventory',
                'type' => 'accent'
            ],
            [
                'label' => 'Profit Margin',
                'value' => '24.6%',
                'trend' => '-1.8%',
                'trend_type' => 'negative',
                'icon' => 'trending_up',
                'type' => 'warning'
            ]
        ];

        // Return all data
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
            'categoryStats' => $categoryStats
        ];
    }

    /**
     * Calculate date range based on time period
     */
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
                return ['start' => date('Y-m-d'), 'end' => date('Y-m-d', strtotime('7 day'))];
            case 'next_month':
                return ['start' => date('Y-m-d', ), 'end' => date('Y-m-d',strtotime('1 month'))];
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
}