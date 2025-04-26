<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\OrderModel;
use App\Models\ProductModel;

class ProductsController extends Controller
{
  public function details(array $params)
  {
    $productModel = new ProductModel();
    $product = $productModel->getProductById($params['id']);
    $units = $productModel->getMeasuringUnits();
    $suppliers = $productModel->getSuppliersByProductId($params['id']);
    $sales = $productModel->getSalesOfMonth($params['id']);

    View::renderTemplate('ProductDetails', [
      'title' => 'Product Details',
      'product' => $product,
      'units' => $units,
      'suppliers' => $suppliers,
      'sales' => $sales
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

  public function assignProducts(): void
  {
    $productModel = new ProductModel();
    $query = $_GET['q'] ?? '';
    $page = $_GET['p'] ?? 1;
    $itemsPerPage = $_GET['ipp'] ?? 10;

    $products = $productModel->assignProduct($query, $page, $itemsPerPage);

    self::sendJSON([
      "success" => true,
      "data" => $products,
    ]);
  }

  public function placeOrder(array $params)
  {
    $supplier_id = $_GET['supplier'] ?? null;
    $product_id = $params['id'] ?? null;

    if (!$supplier_id || !$product_id) {
      $_SESSION['message'] = 'Missing required parameters';
      $_SESSION['message_type'] = 'error';
      View::redirect("/products/$product_id");
      return;
    }

    $productModel = new ProductModel();
    $reorderquantity = $productModel->getReorderQuantity($product_id);

    $orderData = [
      'supplier_id' => $supplier_id,
      'order_date' => date('Y-m-d'),
      'reference' => 'PO-' . date('YmdHis'),
      'items' => [
        [
          'id' => $product_id,
          'quantity' => $reorderquantity['reorder_quantity']
        ]
      ]
    ];

    $orderModel = new OrderModel();
    try {
      $order_id = $orderModel->createOrder($orderData);

      if ($order_id) {
        $_SESSION['message'] = 'Order placed successfully';
        $_SESSION['message_type'] = 'success';
        View::redirect('/orders/' . $order_id);
      } else {
        $_SESSION['message'] = 'Failed to place order';
        $_SESSION['message_type'] = 'error';
        View::redirect('/products');
      }
    } catch (\Exception $e) {
      $_SESSION['message'] = 'Error: ' . $e->getMessage();
      $_SESSION['message_type'] = 'error';
      View::redirect('/products');
    }
  }
}
