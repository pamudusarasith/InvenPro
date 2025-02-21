<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\ProductModel;

class ProductsController extends Controller
{
  public function details(array $params) {
    $productModel = new ProductModel();
    $product = $productModel->getProductById($params['id']);
    $units = $productModel->getMeasuringUnits();
    View::renderTemplate('ProductDetails', [
      'title' => 'Product Details',
      'product' => $product,
      'units' => $units
    ]);
  }
}
