<?php

return [
    'GET' => [
        '/' => ['controller' => App\Controllers\Login::class],
        '/dashboard' => ['controller' => App\Controllers\Dashboard::class],
        '/products' => ['controller' => App\Controllers\Products::class],
        '/products/search' => ['controller' => App\Controllers\Products::class, 'action' => 'search'],
        '/product' => ['controller' => App\Controllers\Products::class, 'action' => 'details'],
        '/api/product' => ['controller' => App\Controllers\Products::class, 'action' => 'apiDetails'],
        '/categories/search' => ['controller' => App\Controllers\Categories::class, 'action' => 'search'],
        '/users' => ['controller' => App\Controllers\UserController::class],
        '/roles' => ['controller' => App\Controllers\RolesController::class],
        '/discounts' => ['controller' => App\Controllers\Discounts::class],
        '/orders' => ['controller' => App\Controllers\PurchaseOrders::class],
        '/orders/add' => ['controller' => App\Controllers\PurchaseOrders::class, 'action' => 'addformview'],
        '/orders/details' => ['controller' => App\Controllers\PurchaseOrders::class, 'action' => 'details'],
        '/branch/add' => ['controller' => App\Controllers\Branches::class, 'action' => 'addformview'],
        '/branches' => ['controller' => App\Controllers\Branches::class],
        '/suppliers' => ['controller' => App\Controllers\Suppliers::class],
        '/suppliers/edit' => ['controller' => App\Controllers\Suppliers::class, 'action' => 'edit'],
        '/suppliers/add' => ['controller' => App\Controllers\Suppliers::class, 'action' => 'add'],
        '/suppliers/details' => ['controller' => App\Controllers\Suppliers::class, 'action' => 'details'],
        '/reports' => ['controller' => App\Controllers\Dashboard::class],
        '/logout' => ['controller' => App\Controllers\Logout::class],
        '/pos' => ['controller' => App\Controllers\POS::class],
        '/pos/search' => ['controller' => App\Controllers\POS::class, 'action' => 'search'],
        '/reports' => ['controller' => App\Controllers\Reports::class],
        '/categories' => ['controller' => App\Controllers\Categories::class]

    ],

    'POST' => [
        '/login' => ['controller' => App\Controllers\Login::class, 'action' => 'login'],
        '/users/create' => ['controller' => App\Controllers\UserController::class, 'action' => 'create'],
        '/users/edit' => ['controller' => App\Controllers\UserController::class, 'action' => 'edit'],
        '/users/delete' => ['controller' => App\Controllers\UserController::class, 'action' => 'delete'],
        '/products/new' => ['controller' => App\Controllers\Products::class, 'action' => 'newProduct'],
        '/batch/new' => ['controller' => App\Controllers\Products::class, 'action' => 'newBatch'],
        '/category/new' => ['controller' => App\Controllers\Products::class, 'action' => 'newCategory'],
        '/customer/new' => ['controller' => App\Controllers\Customer::class, 'action' => 'newCustomer'],
        '/suppliers/add' => ['controller' => App\Controllers\Suppliers::class, 'action' => 'addSupplier'],
        '/suppliers/delete' => ['controller' => App\Controllers\Suppliers::class, 'action' => 'deleteSupplier'],
        '/suppliers/update' => ['controller' => App\Controllers\Suppliers::class, 'action' => 'updateSupplier'],
    ]

];
