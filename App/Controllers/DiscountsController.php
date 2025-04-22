<?php

namespace App\Controllers;

use App\Core\{Controller, View};

class DiscountsController extends Controller
{
  public function index()
  {
    $discountModel = new \App\Models\DiscountModel();
    $discounts = $discountModel->getDiscounts();

    View::renderTemplate("Discounts", [
      "title" => "Discounts",
      "discounts" => $discounts,
    ]);
  }

  public function createDiscount()
  {
    $_POST['end_date'] = $_POST['end_date'] ?: null;
    foreach ($_POST['coupons'] as &$coupon) {
      $coupon['is_active'] = isset($coupon['is_active']) ? 1 : 0;
    }

    if (!$this->validator->validateCreateDiscount($_POST)) {
      $_SESSION['message'] = $this->validator->getError();
      $_SESSION['message_type'] = 'error';
      View::redirect('/discounts');
      return;
    }

    foreach ($_POST['conditions'] as &$condition) {
      $condition['condition_value'] = json_encode($condition['condition_value']);
    }

    $discountModel = new \App\Models\DiscountModel();
    $discountModel->createDiscount($_POST);
    $_SESSION['message'] = 'Discount created successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/discounts');
  }
}
