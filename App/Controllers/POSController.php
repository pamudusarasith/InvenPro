<?php

namespace App\Controllers;

use App\Core\{Controller, View};
use App\Models\{ProductModel, SaleModel};

class POSController extends Controller
{
  public function index(): void
  {
    View::renderTemplate('POS');
  }

  public function searchProducts(): void
  {
    $productModel = new ProductModel();
    $query = $_GET['q'];
    if (empty($query)) {
      self::sendJSON(["success" => false, "message" => "Query cannot be empty"]);
    }

    $products = $productModel->searchPOSProducts($query);

    self::sendJSON([
      "success" => true,
      "data" => $products,
    ]);
  }

  public function getCheckoutItems(array $items, ProductModel $productModel): array
  {
    $result = [];
    foreach ($items as $item) {
      $batches = $productModel->getSamePriceBatches(
        $item['product_id'],
        number_format($item['price'], 2, '.', '')
      );
      while ($item['quantity'] > 0) {
        $batch = array_shift($batches);
        if ($batch === null) {
          $product = $productModel->getProductById($item['product_id']);
          self::sendJSON([
            "success" => false,
            "message" => "Not enough stock for product \"{$product['product_name']}\"",
          ]);
          exit;
        }

        $quantity = min($item['quantity'], (float) $batch['current_quantity']);
        $result[] = [
          'product_id' => $batch['product_id'],
          'batch_id' => $batch['id'],
          'quantity' => $quantity,
          'unit_price' => $batch['unit_price'],
          'discount' => 0,
        ];

        $item['quantity'] -= $quantity;
      }
    }
    return $result;
  }

  public function checkout(): void
  {
    $data = self::recvJSON();

    if (!$this->validator->validateCheckout($data)) {
      self::sendJSON([
        "success" => false,
        "message" => $this->validator->getError(),
      ]);
    }

    $data['user_id'] = $_SESSION['user']['id'];
    $data['branch_id'] = $_SESSION['user']['branch_id'];

    $productModel = new ProductModel();

    $data['items'] = $this->getCheckoutItems($data['items'], $productModel);

    $subtotal = array_reduce($data['items'], function ($carry, $item) {
      return $carry + $item['quantity'] * $item['unit_price'];
    }, 0);

    $data['subtotal'] = $subtotal;
    $data['discount'] = 0;
    $data['total'] = $subtotal - $data['discount'];

    $saleModel = new SaleModel();
    $saleModel->createSale($data);

    self::sendJSON([
      "success" => true,
      "message" => "Sale created successfully",
    ]);
  }
}
