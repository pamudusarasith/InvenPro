<?php

namespace App\Controllers;

use App\Core\{Controller, View};
use App\Models\BatchModel;
use App\Models\CategoryModel;

class CategoryController extends Controller
{
  public function index(): void
  {
    $query = $_GET['q'] ?? "";
    $page = $_GET['p'] ?? 1;
    $itemsPerPage = $_GET['ipp'] ?? 10;

    $categoryModel = new CategoryModel();
    $categories = $categoryModel->getCategories($query, $page, $itemsPerPage);
    $totalRecords = $categoryModel->getCategoriesCount($query);
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
    $page = $_GET['p'] ?? 1;
    $itemsPerPage = $_GET['ipp'] ?? 10;


    $categoryModel = new CategoryModel();
    $categories = $categoryModel->search($query, $page, $itemsPerPage);

    $this->sendJSON([
      'success' => true,
      'data' => $categories
    ]);
  }

  public function createCategory()
  {
    $categoryModel = new CategoryModel();
    $categoryModel->createCategory($_POST);
    $_SESSION['message'] = 'category added successfully';
    $_SESSION['message_type'] = 'success';
    view::redirect('/categories');
  }

  public function updateCategory(array $params)
  {
    $categoryModel = new CategoryModel();
    $_POST['id'] = $params['id'];
    $categoryModel->updateCategory($_POST);
    $_SESSION['message'] = 'Category updated successfully';
    $_SESSION['message_type'] = 'success';
    view::redirect('/categories');
  }


  public function deletecategory(array $params)
  {
    $categoryModel = new CategoryModel();
    $categoryModel->deleteCategory(($params['id']));

    $_SESSION['message'] = 'Category deleted successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/categories');
  }
}
