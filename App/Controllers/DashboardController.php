<?php

namespace App\Controllers;

use App\Core\View;
use App\Services\RBACService;

class DashboardController
{
    public function index()
    {
        RBACService::requireAuthentication();

        if ($_SESSION['role_name'] === 'System Admin') {
            View::renderTemplate(
                'AdminDashboard',
                [
                    'title' => 'Dashboard'
                ]
            );
        } elseif ($_SESSION['role_name'] === 'Inventory Manager') {
            View::render('Template', [
                'title' => 'Invenpro',
                'view' => 'InventoryManagerDashboard',
                'stylesheets' => [
                    'dashboard'
                ],
            ]);
        } elseif ($_SESSION['role_name'] === 'Branch Manager') {
            View::render('Template', [
                'title' => 'Invenpro',
                'view' => 'BranchManagerDashboard',
                'stylesheets' => [
                    'dashboard'
                ],
            ]);
        } else {
            View::redirect('403.html');
        }
    }
}
