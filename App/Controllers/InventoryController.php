<?php

namespace App\Controllers;

use App\Core\{Controller, View};
use App\Models\{CategoryModel, ProductModel};

class InventoryController extends Controller
{
  public function index()
  {
    $categoryId = $_GET['c'] ?? null;
    $page = $_GET['p'] ?? 1;
    $itemsPerPage = $_GET['ipp'] ?? 10;

    $categoryModel = new CategoryModel();
    $categories = $categoryModel->getPrimaryCategories();

    $productModel = new ProductModel();
    foreach ($categories as &$category) {
      $recordsCount = $productModel->getCountByCategoryId($category['id']);
      if ($categoryId && $category['id'] == $categoryId) {
        $category['products'] = $productModel->getProductsByCategoryId($category['id'], $page, $itemsPerPage);
        $category['page'] = $page;
        $category['totalPages'] = ceil($recordsCount / $itemsPerPage);
        $category['itemsPerPage'] = $itemsPerPage;
      } else {
        $category['products'] = $productModel->getProductsByCategoryId($category['id']);
        $category['page'] = 1;
        $category['totalPages'] = ceil($recordsCount / 10);
        $category['itemsPerPage'] = 10;
      }
    }

    $units = $productModel->getMeasuringUnits();

    View::renderTemplate('Inventory', [
      'title' => 'Inventory',
      'categories' => $categories,
      'units' => $units
    ]);
  }
}
