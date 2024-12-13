<?php

return [
    'GET' => [
        '/' => ['controller' => App\Controllers\Login::class],
        '/dashboard' => ['controller' => App\Controllers\Dashboard::class],
        '/inventory' => ['controller' => App\Controllers\Products::class],
        '/products/search' => ['controller' => App\Controllers\Products::class, 'action' => 'search'],
        '/products/delete' => ['controller' => App\Controllers\Products::class, 'action' => 'deleteProduct'],
        '/product' => ['controller' => App\Controllers\Products::class, 'action' => 'details'],
        '/api/product' => ['controller' => App\Controllers\Products::class, 'action' => 'apiDetails'],
        '/api/categories/search' => ['controller' => App\Controllers\Categories::class, 'action' => 'search'],
        '/users' => ['controller' => App\Controllers\User::class],
        '/roles' => ['controller' => App\Controllers\RolesController::class],
        '/discounts' => ['controller' => App\Controllers\Discounts::class],
        '/suppliers' => ['controller' => App\Controllers\Suppliers::class],
        '/orders' => ['controller' => App\Controllers\PurchaseOrders::class],
        '/orders/add' => ['controller' => App\Controllers\PurchaseOrders::class, 'action' => 'addformview'],
        '/orders/details' => ['controller' => App\Controllers\PurchaseOrders::class, 'action' => 'details'],
        '/branch/add' => ['controller' => App\Controllers\Branches::class, 'action' => 'addformview'],
        '/branches' => ['controller' => App\Controllers\Branches::class],
        '/suppliers/add' => ['controller' => App\Controllers\Suppliers::class, 'action' => 'add'],
        '/suppliers/details' => ['controller' => App\Controllers\Suppliers::class, 'action' => 'details'],
        '/reports' => ['controller' => App\Controllers\Dashboard::class],
        '/logout' => ['controller' => App\Controllers\Logout::class],
        '/pos' => ['controller' => App\Controllers\POS::class],
        '/pos/search' => ['controller' => App\Controllers\POS::class, 'action' => 'search'],
        '/reports' => ['controller' => App\Controllers\Reports::class],
        '/categories' => ['controller' => App\Controllers\Categories::class],
        '/roles' => ['controller' => App\Controllers\RolesController::class],
        '/users/delete' => ['controller' => App\Controllers\User::class, 'action' => 'deleteUser'],
    ],

    'POST' => [
        '/login' => ['controller' => App\Controllers\Login::class, 'action' => 'login'],
        '/users/create' => ['controller' => App\Controllers\User::class, 'action' => 'addUser'],
        '/users/edit' => ['controller' => App\Controllers\User::class, 'action' => 'updateUser'],
        '/products/new' => ['controller' => App\Controllers\Products::class, 'action' => 'newProduct'],
        '/products/update' => ['controller' => App\Controllers\Products::class, 'action' => 'updateProduct'],
        '/batch/new' => ['controller' => App\Controllers\Products::class, 'action' => 'newBatch'],
        '/category/new' => ['controller' => App\Controllers\Products::class, 'action' => 'newCategory'],
        '/customer/new' => ['controller' => App\Controllers\Customer::class, 'action' => 'newCustomer'],
        '/suppliers/add' => ['controller' => App\Controllers\Suppliers::class, 'action' => 'addSupplier'],
        '/suppliers/delete' => ['controller' => App\Controllers\Suppliers::class, 'action' => 'deleteSupplier'],
        '/customer/delete' => ['controller' => App\Controllers\Customer::class, 'action' => 'delete'],
        '/customer/update' => ['controller' => App\Controllers\Customer::class, 'action' => 'update'],
        '/customer/retrieve' => ['controller' => App\Controllers\Customer::class, 'action' => 'retrieve']

    ]

];
