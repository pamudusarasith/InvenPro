<?php

namespace App\Controllers;

use App;

class POS
{
    public function index(): void
    {
        App\Utils::requireAuth();

        App\View::render('Template', [
            'title' => 'Invenpro',
            'view' => 'POS',
            'stylesheets' => ['pos'],
            'scripts' => ['pos'],
        ]);
    }
}
