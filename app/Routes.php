<?php

return [
    'GET' => [
        '/' => ['controller' => App\Controllers\Login::class],
        '/dashboard' => ['controller' => App\Controllers\Dashboard::class],
        '/products' => ['controller' => App\Controllers\Products::class],
        '/products/search' => ['controller' => App\Controllers\Products::class, 'action' => 'search'],
        '/product' => ['controller' => App\Controllers\Products::class, 'action' => 'details'],
        '/categories/search' => ['controller' => App\Controllers\Categories::class, 'action' => 'search'],
        '/users' => ['controller' => App\Controllers\UserController::class],
        '/roles' => ['controller' => App\Controllers\RolesController::class],
        '/discounts' => ['controller' => App\Controllers\Dashboard::class],
        '/suppliers' => ['controller' => App\Controllers\Suppliers::class],
        '/orders' => ['controller' => App\Controllers\PurchaseOrders::class],
        '/branches' => ['controller' => App\Controllers\Branches::class],
        '/suppliers/add' => ['controller' => App\Controllers\Suppliers::class, 'action' => 'add'],
        '/suppliers/details' => ['controller' => App\Controllers\Suppliers::class, 'action' => 'details'],
        '/reports' => ['controller' => App\Controllers\Dashboard::class],
        '/logout' => ['controller' => App\Controllers\Logout::class],
        '/pos' => ['controller' => App\Controllers\POS::class],
        '/pos/search' => ['controller' => App\Controllers\POS::class, 'action' => 'search'],
        '/reports' => ['controller' => App\Controllers\Reports::class]
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
        '/suppliers/delete' => ['controller' => App\Controllers\Suppliers::class, 'action' => 'deleteSupplier']
    ]

];
