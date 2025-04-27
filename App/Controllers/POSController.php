<?php

namespace App\Controllers;

use App\Core\{Controller, View};
use App\Models\{DiscountModel, ProductModel, SaleModel, UserModel};
use App\Services\DiscountService;
use App\Services\NotificationService;

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
        number_format($item['unit_price'], 2, '.', '')
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
      exit;
    }

    $data['user_id'] = $_SESSION['user']['id'];
    $data['branch_id'] = $_SESSION['user']['branch_id'];

    $productModel = new ProductModel();

    $data['items'] = $this->getCheckoutItems($data['items'], $productModel);

    $subtotal = array_reduce($data['items'], function ($carry, $item) {
      return $carry + $item['quantity'] * $item['unit_price'];
    }, 0);

    $discountModel = new DiscountModel();
    $discounts = $discountModel->getDiscounts(
      null,
      null,
      null,
      1,
      date('Y-m-d'),
      date('Y-m-d'),
      null
    );
    $discounts = DiscountService::calculateOptimalDiscounts(
      $data['items'],
      $discounts,
      $data['customer_id'] ?? null
    );

    $discountAmount = 0;
    foreach ($discounts as $discount) {
      if ($discount['discount_type'] === 'percentage') {
        $discountAmount += $subtotal * ($discount['value'] / 100);
      } elseif ($discount['discount_type'] === 'fixed') {
        $discountAmount += $discount['value'];
      }
    }
    $discountAmount = min($discountAmount, $subtotal);
    $data['discounts'] = $discounts;
    $data['customer_id'] = $data['customer_id'] ?? null;

    $data['subtotal'] = $subtotal;
    $data['discount'] = $discountAmount;
    $data['total'] = $subtotal - $data['discount'];

    $saleModel = new SaleModel();
    $saleId = $saleModel->createSale($data);

    if (!$saleId) {
      self::sendJSON([
        "success" => false,
        "message" => "Failed to create sale",
      ]);
      exit;
    }

    // Check each product for low stock after sale is completed
    $productsToCheck = [];
    foreach ($data['items'] as $item) {
      $productsToCheck[] = $item['product_id'];
    }

    // Trigger low stock notifications for affected products
    foreach ($productsToCheck as $productId) {
      if ($productModel->checkLowStock($productId)) {
        $this->sendLowStockNotification($productId);
      }
    }

    self::sendJSON([
      "success" => true,
      "message" => "Sale created successfully",
      "sale_id" => $saleId
    ]);
  }

  public function sendLowStockNotification(int $productId): void
  {
    $productModel = new ProductModel();
    $product = $productModel->getProductById($productId);
    // Notify users with create_purchase_orders permission
    $userModel = new UserModel();
    $usersWithPermission = $userModel->getUsersByPermission('create_purchase_orders');

    foreach ($usersWithPermission as $user) {
      NotificationService::sendToUser(
        $user['id'],
        'Low Stock Alert',
        "Product {$product['product_name']} is low on stock.",
        'warning',
        'high',
        [
          'product_id' => $product['id'],
          'product_name' => $product['product_name'],
          'current_quantity' => $product['current_quantity'],
          'reorder_level' => $product['reorder_level'],
          'action' => 'view_product'
        ],
        null
      );
    }
  }
}
