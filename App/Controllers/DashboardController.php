<?php

namespace App\Controllers;

use App\Core\{Controller, View};
use App\Services\RBACService;

class DashboardController extends Controller
{
    public function index()
    {
        RBACService::requireAuthentication();

        if ($_SESSION['user']['role_name'] === 'System Admin') {
            View::renderTemplate(
                'AdminDashboard',
                [
                    'title' => 'Dashboard'
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
            View::redirect('403.html');
        }
    }
}
