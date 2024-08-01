<?php

namespace App\Controllers;
use App;

session_start();

class Logout {
    public function index(): void {
        session_destroy();
        header("Location: /");
    }
}

?>