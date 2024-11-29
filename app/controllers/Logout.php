<?php

namespace App\Controllers;

use App\Utils;

class Logout
{
    public function index(): void
    {
        session_unset();
        session_destroy();
        Utils::redirect("/");
    }
}
