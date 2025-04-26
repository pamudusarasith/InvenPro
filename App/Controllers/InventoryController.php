<?php

namespace App\Controllers;

use App\Core\{Controller, View};
use App\Models\{CategoryModel, ProductModel};

class InventoryController extends Controller
{
  public function index()
  {
    $status = $_GET['status'] ?? "";
    $query = $_GET['q'] ?? "";
    $categoryId = $_GET['c'] ?? null;
    $page = $_GET['p'] ?? 1;
    $itemsPerPage = $_GET['ipp'] ?? 10;

    $productModel = new ProductModel();
    $units = $productModel->getMeasuringUnits();

    if (!empty($query) || !empty($status)) {
      $products = $productModel->searchInventoryProducts($query, $status, $page, $itemsPerPage);
      $totalRecords = $productModel->getInventoryProductsCount($query);
      View::renderTemplate('Inventory', [
        'title' => 'Inventory',
        'units' => $units,
        'products' => $products,
        'page' => $page,
        'itemsPerPage' => $itemsPerPage,
        'totalPages' => ceil($totalRecords / $itemsPerPage),
      ]);
      return;
    }

    $categoryModel = new CategoryModel();
    $categories = $categoryModel->getPrimaryCategories();

    foreach ($categories as &$category) {
      $recordsCount = $productModel->getCountByCategoryId($category['id']);
      if ($categoryId && $category['id'] == $categoryId) {
        $category['products'] = $productModel->getProductsByCategoryId($category['id'], $page, $itemsPerPage);
        $category['page'] = $page;
        $category['totalPages'] = ceil($recordsCount / $itemsPerPage);
        $category['itemsPerPage'] = $itemsPerPage;
      } else {
        $category['products'] = $productModel->getProductsByCategoryId($category['id'], 1, 10);
        $category['page'] = 1;
        $category['totalPages'] = ceil($recordsCount / 10);
        $category['itemsPerPage'] = 10;
      }
    }

    View::renderTemplate('Inventory', [
      'title' => 'Inventory',
      'categories' => $categories,
      'units' => $units
    ]);
  }
}
