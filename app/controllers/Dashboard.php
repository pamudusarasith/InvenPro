<?php

namespace App\Controllers;
use App;

class Dashboard {
    public function index(): void {
        App\View::render('Dashboard');
    }
}

?>