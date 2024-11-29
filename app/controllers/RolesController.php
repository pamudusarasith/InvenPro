<?php

namespace App\Controllers;

use App;
use App\Consts;
use App\Utils;

class RolesController
{
    public function index()
    {
        App\Utils::requireAuth();

        if (!App\Utils::can("manage_roles")) {
            Utils::error(403);
        }

        $rolesModel = new App\Models\Roles();
        $roles = $rolesModel->getAllRoles();

        App\View::render('Template', [
            'title' => 'Roles & Permissions',
            'view' => 'Roles',
            'data' => [
                'roles' => $roles,
            ],
            // 'stylesheets' => ['roles', 'search'],
            // 'scripts' => ['roles', 'search'],
        ]);
    }
}