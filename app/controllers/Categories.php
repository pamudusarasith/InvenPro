<?php

namespace App\Controllers;

use App;
use App\Consts;

class Categories
{
    public function index(): void
    {
        App\Utils::requireAuth();

        App\View::render('Template', [
            'title' => 'Categories',
            'view' => 'Categories',
            'stylesheets' => ['']
        ]);
    }

    public function search()
    {
        App\Utils::requireAuth();

        $query = $_GET['q'];
        $model = new App\Models\Category();
        $results = $model->search($query);

        header(Consts::HEADER_JSON);
        echo json_encode(['success' => true, 'data' => ['query' => $query, 'results' => $results]]);
    }
}
