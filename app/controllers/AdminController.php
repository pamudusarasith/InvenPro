<?php

namespace App\Controllers;

use App;

session_start();

class AdminController
{
    public function dashboard(): void
    {
        $employee = new App\Models\Employee();
        $totalUsers = $employee->getTotalUsers();
        $activeUsers = $employee->getActiveUsers();
        $newSignUps = $employee->getNewSignUps();

        $viewData = [
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'newSignUps' => $newSignUps,
        ];

        App\View::render('admin/dashboard', $viewData);
    }
}
