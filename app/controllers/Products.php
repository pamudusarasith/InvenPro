<?php

namespace App\Controllers;

use App;

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
            'data' => ['categories' => $categories, 'products' => $products]
        ]);
    }
}
