<?php

namespace App\Controllers;

use App\Core\{Controller, View};
use App\Models\CategoryModel;

class CategoryController extends Controller
{
  public function index(): void
  {
    $page = $_GET['p'] ?? 1;
    $itemsPerPage = $_GET['ipp'] ?? 10;

    $categoryModel = new CategoryModel();
    $categories = $categoryModel->getCategories($page, $itemsPerPage);
    $totalRecords = $categoryModel->getCategoriesCount();
    $totalPages = ceil($totalRecords / $itemsPerPage);

    View::renderTemplate('Categories', [
      'title' => 'Categories',
      'categories' => $categories,
      'page' => $page,
      'itemsPerPage' => $itemsPerPage,
      'totalPages' => $totalPages,
    ]);
  }


  public function search()
  {
    $query = $_GET['q'] ?? null;
    if (!$query) {
      $this->sendJSON([
        'success' => false,
        'message' => 'No search query provided'
      ]);
      exit;
    }

    $categoryModel = new CategoryModel();
    $categories = $categoryModel->search($query);

    $this->sendJSON([
      'success' => true,
      'data' => $categories
    ]);
  }
}
