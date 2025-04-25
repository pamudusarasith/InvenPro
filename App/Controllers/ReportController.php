<?php

namespace App\Controllers;
 
use App\Core\{Controller, View};
use App\Services\RBACService;
use App\Models\{UserModel, CustomerModel , ReportModel, SaleModel, SupplierModel, ProductModel, BranchModel, OrderModel};

 
class ReportController extends Controller
{
    public function index(): void
    {

      $saleModel = new SaleModel();
        $supplierModel = new SupplierModel();
        $productModel = new ProductModel();
        $orderModel = new OrderModel();
        $customerModel = new CustomerModel();
        $userModel = new UserModel();

        $sales = $saleModel->getSalesToday();
        $salesToday['today_sales'] = $sales['today_sales'] ?? 0;
        $salesToday['yesterday_sales'] = $sales['yesterday_sales'] ?? 0;
        $salesToday['trendToday'] = ($salesToday['yesterday_sales'] == 0) ? 0 : (($salesToday['today_sales'] - $salesToday['yesterday_sales']) * 100 / $salesToday['yesterday_sales']); // Avoid division by zero
        $salesToday['trnedType'] = $salesToday['trendToday'] > 0 ? 'positive' : ($salesToday['trendToday'] < 0 ? 'negative' : 'neutral');

        $stock = $productModel->getStockProductsCounts();
        $pendingOrders = $orderModel->getPendingAndOpenOrdersCount();

        if ($_SESSION['user']['branch_id'] != 1) {
            $branchId = $_SESSION['user']['branch_id'];
            $systemUsers = $userModel->getUsersCount('', '', $branchId, 'Active');
        } else {
            $systemUsers = $userModel->getUsersCount('', '', '', 'Active');
        }

        $monthlyRevenue = $saleModel->getMonthlyRevenue();
        $activeCustomers = $customerModel->getActiveCustomersCount();
        $pendingReturns = $productModel->getPendingReturnsCount();
        $activeSuppliers = $supplierModel->getActiveSuppliersCount();


        
        if ($_SESSION['user']['role_name'] === 'System Admin') {
            $reportData = [
                'sales' => [
                    'value' => 'LKR ' . number_format($salesToday['today_sales'], 2),
                    'trend' => ($salesToday['trendToday'] > 0 ? '+' : ($salesToday['trendToday'] < 0 ? '-' : '')) . number_format($salesToday['trendToday'], 2) . '%',
                    'trendType' => $salesToday['trnedType'],
                ],
                'lowStock' => [
                    'value' => $stock['low_stock'],
                ],
                'pendingOrders' => [
                    'value' => $pendingOrders['pending'],
                ],
                'systemUsers' => [
                    'value' => $systemUsers,
                ],
                'monthlyRevenue' => [
                    'value' => 'LKR ' . number_format($monthlyRevenue, 2),
                ],
                'activeCustomers' => [
                    'value' => $activeCustomers,
                ],
                'pendingReturns' => [
                    'value' => $pendingReturns,
                ],
                'approvedOrders' => [
                    'value' => $pendingOrders['open'],
                ],
                'outOfStock' => [
                    'value' => $stock['out_of_stock'],
                ],
                'totalProducts' => [
                    'value' => ($stock['in_stock']) + ($stock['low_stock']) ,
                ],
                'activeSuppliers' => [
                    'value' => $activeSuppliers,
                ],
            ];

        

        View::renderTemplate('Reports'
            , [
            'title' => 'Reports',
            'reportData' => $reportData,
        ]);
    }
  }

  public function filterTimePeriod(): void
  {
    $timePeriod = $_GET['time_period'];
    $reportModel = new ReportModel();
    $filteredSalesData = $reportModel->filterTimePeriod($timePeriod);
    
    $this->sendJSON([
      'success' => true,
      'data' => $filteredSalesData
    ]);

    error_log("Filtered sales data: " . json_encode($filteredSalesData));
  }
}