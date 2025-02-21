<?php

return [
  'GET' => [
    '/' => 'App\Controllers\AuthController::index',
    '/logout' => 'App\Controllers\AuthController::logout',
    '/dashboard' => 'App\Controllers\DashboardController::index',
    '/users' => 'App\Controllers\UserController::index',
    '/users/{id}' => 'App\Controllers\UserController::details',
    '/users/{id}/delete' => 'App\Controllers\UserController::deleteUser',
    '/roles' => 'App\Controllers\RoleController::index',
    '/categories' => 'App\Controllers\CategoryController::index',
    '/inventory' => 'App\Controllers\InventoryController::index',
    '/products/{id}' => 'App\Controllers\ProductsController::details',
    '/customers' => 'App\Controllers\CustomerController::index',
    '/suppliers' => 'App\Controllers\SupplierController::index',
    '/suppliers/{id}' => 'App\Controllers\SupplierController::details',
    '/suppliers/{id}/delete' => 'App\Controllers\SupplierController::deleteSupplier',
    '/orders' => 'App\Controllers\OrderController::index',
    '/discounts' => 'App\Controllers\DiscountController::index',
    '/reports' => 'App\Controllers\ReportController::index',
    '/employees' => 'App\Controllers\EmployeeController::index',
    '/pos' => 'App\Controllers\POSController::index',
    '/api/pos/search' => 'App\Controllers\POSController::searchProducts',
  ],

  'POST' => [
    '/' => 'App\Controllers\AuthController::login',
    '/users/new' => 'App\Controllers\UserController::createUser',
    '/users/{id}/update' => 'App\Controllers\UserController::updateUser',
    '/suppliers/new' => 'App\Controllers\SupplierController::createSupplier',
    '/suppliers/{id}/update' => 'App\Controllers\SupplierController::updateSupplier',
    '/api/pos/checkout' => 'App\Controllers\POSController::checkout',
  ],
];
