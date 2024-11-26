<?php

namespace App\Controllers;

use App;
use App\Consts;

class POS
{
    public function index(): void
    {
        App\Utils::requireAuth();

        App\View::render('Template', [
            'title' => 'Invenpro',
            'view' => 'POS',
            'stylesheets' => ['pos', 'search'],
            'scripts' => ['pos', 'search'],
        ]);
    }

    public function search()
    {
        App\Utils::requireAuth();

        $query = $_GET['q'];
        $product = new App\Models\Product();
        $products = $product->posSearch($query);

        header(Consts::HEADER_JSON);
        echo json_encode(['success' => true, 'data' => ['query' => $query, 'results' => $products]]);
    }
}
