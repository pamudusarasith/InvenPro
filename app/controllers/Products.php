<?php

namespace App\Controllers;

use App;
use App\Consts;

class Products
{
    public function index()
    {
        App\Utils::requireAuth();

        $product = new App\Models\Product();
        $categories = $product->getCategories();
        $products = [];
        foreach ($categories as $category) {
            $products[$category] = $product->getProductsByCategory($category);
        }

        App\View::render('Template', [
            'title' => 'Products',
            'view' => 'Products',
            'stylesheets' => ['products'],
            'scripts' => ['products'],
            'data' => ['categories' => $categories, 'products' => $products]
        ]);
    }

    public function newProduct()
    {
        App\Utils::requireAuth();

        header(Consts::HEADER_JSON);
        echo json_encode(['success' => true, 'message' => 'Product added successfully']);
    }

    public function newBatch()
    {
        App\Utils::requireAuth();

        header(Consts::HEADER_JSON);
        echo json_encode(['success' => true, 'message' => 'Batch added successfully']);
    }

    public function newCategory()
    {
        App\Utils::requireAuth();

        header(Consts::HEADER_JSON);
        echo json_encode(['success' => true, 'message' => 'Category added successfully']);
    }
}
