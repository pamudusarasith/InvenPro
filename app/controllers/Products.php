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

        $data = $this->validateProductData($_POST, $_FILES);

        $product = new App\Models\Product();
        $product->createProduct($data);

        exit();
    }

    public function updateProduct()
    {
        App\Utils::requireAuth();

        $id = $_POST['id'];
        $data = $this->validateProductData($_POST, $_FILES);

        $product = new App\Models\Product();
        $product->updateProduct($id, $data);

        exit();
    }

    public function deleteProduct()
    {
        App\Utils::requireAuth();

        $id = $_GET['id'];
        $product = new App\Models\Product();
        $product->deleteProduct($id);

        exit();
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
            // 'stylesheets' => ['productDetails', 'search'],
            // 'scripts' => ['productDetails', 'search'],
            'data' => ['product' => $product]
        ]);
    }

    public function apiDetails()
    {
        App\Utils::requireAuth();

        $id = $_GET['id'];
        $model = new App\Models\Product();
        $product = $model->getProductDetails($id);

        header(Consts::HEADER_JSON);
        echo json_encode($product);
    }

    private function validateProductData($input, $files) {
        $errors = [];
        
        // Required fields
        $required = ['name', 'description', 'measure_unit', 
                    'min_threshold', 'max_threshold',
                    'reorder_point', 'reorder_quantity'];
                    
        foreach ($required as $field) {
            if (empty($input[$field])) {
                $errors[] = "Missing required field: $field";
            }
        }

        // Validate thresholds
        if (!empty($input['min_threshold']) && !empty($input['max_threshold'])) {
            $min = floatval($input['min_threshold']);
            $max = floatval($input['max_threshold']);
            $point = floatval($input['reorder_point']);
            $qty = floatval($input['reorder_quantity']);

            if ($min >= $max) {
                $errors[] = "Minimum threshold must be less than maximum";
            }
            if ($point <= $min || $point >= $max) {
                $errors[] = "Reorder point must be between thresholds";
            }
            if ($qty <= 0) {
                $errors[] = "Reorder quantity must be greater than zero";
            }
        }

        // Validate image
        if (!empty($files['image'])) {
            $image = $files['image'];
            if ($image['error'] === 0) {
                if ($image['size'] > 5242880) {
                    $errors[] = "Image must be less than 5MB";
                }
                $allowed = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($image['type'], $allowed)) {
                    $errors[] = "Invalid image format";
                }
            }
        }

        if (!empty($errors)) {
            throw new \Exception(implode("\n", $errors));
        }

        return array_merge($input, ['image' => $files['image'] ?? null]);
    }
}
