<?php

namespace App\Controllers;

use App;

class Products
{
    public function index()
    {
        if (!isset($_SESSION["email"])) {
            header("Location: /");
            return;
        }

        App\View::render('Products');
    }
}
