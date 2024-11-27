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
        $categories = $product->getPrimaryCategories();
        $products = [];
        foreach ($categories as $category) {
            $products[$category["name"]] = $product->getProductsByCategory($category["id"]);
        }

        App\View::render('Template', [
            'title' => 'Products',
            'view' => 'Products',
            // 'stylesheets' => ['products', 'search'],
            // 'scripts' => ['products', 'search'],
            'data' => ['categories' => array_column($categories, 'name'), 'products' => $products]
        ]);
    }

    public function newProduct()
    {
        App\Utils::requireAuth();

        header(Consts::HEADER_JSON);
        echo json_encode(['success' => true, 'data' => 'Product added successfully']);
    }

    public function newBatch()
    {
        App\Utils::requireAuth();

        header(Consts::HEADER_JSON);
        echo json_encode(['success' => true, 'data' => 'Batch added successfully']);
    }

    public function newCategory()
    {
        App\Utils::requireAuth();

        header(Consts::HEADER_JSON);
        echo json_encode(['success' => true, 'data' => 'Category added successfully']);
    }

    public function search()
    {
        App\Utils::requireAuth();

        $query = $_GET['q'];
        $product = new App\Models\Product();
        $products = $product->search($query);

        header(Consts::HEADER_JSON);
        echo json_encode(['success' => true, 'data' => ['query' => $query, 'results' => $products]]);
    }

    public function details()
    {
        App\Utils::requireAuth();

        $id = $_GET['id'];
        $model = new App\Models\Product();
        $product = $model->getProductDetails($id);

        App\View::render('Template', [
            'title' => 'Product Details',
            'view' => 'ProductDetails',
            'stylesheets' => ['productDetails', 'search'],
            'scripts' => ['productDetails', 'search'],
            'data' => ['product' => $product]
        ]);
    }
}
