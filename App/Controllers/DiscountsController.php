<?php

namespace App\Controllers;

use App\Core\{Controller, View};
use App\Models\DiscountModel;

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

    if (!$this->validator->validateCreateOrUpdateDiscount($_POST)) {
      $_SESSION['message'] = $this->validator->getError();
      $_SESSION['message_type'] = 'error';
      View::redirect('/discounts');
      return;
    }

    foreach ($_POST['conditions'] as &$condition) {
      $condition['condition_value'] = json_encode($condition['condition_value']);
    }

    $discountModel = new DiscountModel();
    $discountModel->createDiscount($_POST);
    $_SESSION['message'] = 'Discount created successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/discounts');
  }

  public function updateDiscount(array $params)
  {
    $discountModel = new DiscountModel();
    if (!$params['id'] || !$discountModel->discountExists($params['id'])) {
      $_SESSION['message'] = 'Discount not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/discounts');
      return;
    }

    $_POST['end_date'] = $_POST['end_date'] ?: null;
    foreach ($_POST['coupons'] as &$coupon) {
      $coupon['is_active'] = isset($coupon['is_active']) ? 1 : 0;
    }

    if (!$this->validator->validateCreateOrUpdateDiscount($_POST)) {
      $_SESSION['message'] = $this->validator->getError();
      $_SESSION['message_type'] = 'error';
      View::redirect('/discounts');
      return;
    }

    foreach ($_POST['conditions'] as &$condition) {
      $condition['condition_value'] = json_encode($condition['condition_value']);
    }

    $discountModel->updateDiscount($params['id'], $_POST);
    $_SESSION['message'] = 'Discount updated successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/discounts');
  }

  public function deleteDiscount(array $params)
  {
    $discountModel = new DiscountModel();
    if (!$params['id'] || !$discountModel->discountExists($params['id'])) {
      $_SESSION['message'] = 'Discount not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/discounts');
      return;
    }

    $discountModel->deleteDiscount($params['id']);
    $_SESSION['message'] = 'Discount deleted successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/discounts');
  }

  public function activateDiscount(array $params)
  {
    $discountModel = new DiscountModel();
    if (!$params['id'] || !$discountModel->discountExists($params['id'])) {
      $_SESSION['message'] = 'Discount not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/discounts');
      return;
    }

    $discountModel->changeStatus($params['id'], 1);
    $_SESSION['message'] = 'Discount activated successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/discounts');
  }

  public function deactivateDiscount(array $params)
  {
    $discountModel = new DiscountModel();
    if (!$params['id'] || !$discountModel->discountExists($params['id'])) {
      $_SESSION['message'] = 'Discount not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/discounts');
      return;
    }

    $discountModel->changeStatus($params['id'], 0);
    $_SESSION['message'] = 'Discount deactivated successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/discounts');
  }
}
