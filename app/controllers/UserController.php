<?php

namespace App\Controllers;

use App\View;

class UserController
{
    public function index(): void
    {
        $users = [
            [
                'id' => '12345',
                'name' => 'H S D Fernando',
                'role' => 'Admin',
                'branch' => 'Head Office',
                'status' => 'active'
            ],
            [
                'id' => '12346',
                'name' => 'S L Perera',
                'role' => 'Inventory Manager',
                'branch' => 'Colombo 05',
                'status' => 'active'
            ],
            [
                'id' => '12347',
                'name' => 'S U D Gunaratne',
                'role' => 'Branch Manager',
                'branch' => 'Wattala',
                'status' => 'inactive'
            ],
        ];

        View::render('users', ['users' => $users]);
    }
}
