<?php

namespace App\Controllers;

use App;

class Products
{
    public function index()
    {
        if (!isset($_SESSION["email"])) {
            header("Location: /");
            exit();
        }
        $product = new App\Models\Product();
        $categories = $product->getCategories();
        $products = [];
        foreach ($categories as $category) {
            $products[$category] = $product->getProductsByCategory($category);
        }

        App\View::render('Template', [
            'title' => 'Products',
            'view' => 'Products',
            'data' => ['categories' => $categories, 'products' => $products]
        ]);
    }
}
