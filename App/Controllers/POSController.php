<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\ProductModel;

class POSController
{
  public function index(): void
  {
    View::renderTemplate('POS');
  }

  public function searchProducts(): void
  {
    $productModel = new ProductModel();
    $products = $productModel->searchProduct($_GET['q']);

    header('Content-Type: application/json');
    echo json_encode($products);
  }
}