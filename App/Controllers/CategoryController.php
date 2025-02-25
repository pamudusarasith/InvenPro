<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CategoryModel;

class CategoryController extends Controller
{
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
