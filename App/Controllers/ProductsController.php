<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\ProductModel;
use Exception;

class ProductsController extends Controller
{
  public function details(array $params)
  {
    $productModel = new ProductModel();
    $product = $productModel->getProductById($params['id']);
    $units = $productModel->getMeasuringUnits();
    $suppliers = $productModel->getSuppliersByProductId($params['id']);
    View::renderTemplate('ProductDetails', [
      'title' => 'Product Details',
      'product' => $product,
      'units' => $units,
      'suppliers' => $suppliers
    ]);
  }

  public function createProduct()
  {
    if (!$this->validator->validateCreateProduct($_POST)) {
      $_SESSION['message'] = $this->validator->getError();
      $_SESSION['message_type'] = 'error';
      View::redirect('/inventory');
    }

    $productModel = new ProductModel();
    $productModel->createProduct($_POST);

    $_SESSION['message'] = 'Product created successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/inventory');
  }

  public function updateProduct(array $params)
  {
    if (!$this->validator->validateUpdateProduct($_POST)) {
      $_SESSION['message'] = $this->validator->getError();
      $_SESSION['message_type'] = 'error';
      View::redirect('/products/' . $params['id']);
    }

    $productModel = new ProductModel();
    $productModel->updateProduct($params['id'], $_POST);

    $_SESSION['message'] = 'Product updated successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/products/' . $params['id']);
  }

  public function deleteProduct(array $params)
  {
    $productModel = new ProductModel();
    $productModel->deleteProduct($params['id']);

    $_SESSION['message'] = 'Product deleted successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/inventory');
  }

  public function productSuppliersDetails(array $params)
  {
    $productModel = new ProductModel();
    $productsupplier = $productModel->getSuppliersByProductId($params['product_id']);
    View::renderTemplate('ProductSuppliersDetails', [
      'title' => 'Product Suppliers',
      'productsupplier' => $productsupplier
    ]);
  }

  public function searchProducts()
  {
    $productModel = new ProductModel();
    $query = $_GET['q'] ?? '';
    $page = $_GET['p'] ?? 1;
    $itemsPerPage = $_GET['ipp'] ?? 10;

    try {
      $products = $productModel->searchProducts($query, $page, $itemsPerPage);

      self::sendJSON([
        "success" => true,
        "data" => $products,
      ]);
    } catch (Exception $e) {
      self::sendJSON([
        "success" => false,
        "message" => "Failed searching products"
      ]);
    }
  }
}
