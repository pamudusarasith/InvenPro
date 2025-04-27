<?php

namespace App\Controllers;

use App\Core\{Controller, View};
use App\Services\RBACService;
use App\Models\{UserModel, AuditLogModel,CustomerModel , RoleModel, PermissionModel, SaleModel, SupplierModel, ProductModel, BranchModel, OrderModel};

class DashboardController extends Controller
{
    public function index()
    {
        RBACService::requireAuthentication();
        
        $saleModel = new SaleModel();
        $supplierModel = new SupplierModel();
        $productModel = new ProductModel();
        $orderModel = new OrderModel();
        $customerModel = new CustomerModel();
        $userModel = new UserModel();

        $sales = $saleModel->getSalesCardData();
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
            $dashboardData = [
                'greeting' => 'Welcome to Invenpro!',
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
        
            View::renderTemplate(
                'AdminDashboard',
                [
                    'title' => 'Dashboard',
                    'dashboardData' => $dashboardData,
                ]
            );
        } elseif ($_SESSION['user']['role_name'] === 'Inventory Manager') {
            View::render('Template', [
                'title' => 'Invenpro',
                'view' => 'InventoryManagerDashboard',
                'stylesheets' => [
                    'dashboard'
                ],
            ]);
        } elseif ($_SESSION['user']['role_name'] === 'Branch Manager') {
            View::render('Template', [
                'title' => 'Invenpro',
                'view' => 'BranchManagerDashboard',
                'stylesheets' => [
                    'dashboard'
                ],
            ]);
        } else {
            View::renderError(403);
        }
    }
}
