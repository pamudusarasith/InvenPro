<?php

namespace App\Controllers;

use App\Core\{Controller, View};
use App\Models\CustomerModel;
use App\Models\DiscountModel;
use App\Services\DiscountService;
use App\Services\RBACService;

class DiscountsController extends Controller
{
  public function __construct()
  {
    parent::__construct();
    RBACService::requireAuthentication();
  }

  public function index()
  {
    if (!RBACService::hasPermission('view_discounts')) {
      $_SESSION['message'] = 'You do not have permission to view discounts';
      $_SESSION['message_type'] = 'error';
      View::redirect('/dashboard');
      return;
    }

    $page = $_GET['p'] ?? 1;
    $itemsPerPage = $_GET['ipp'] ?? 10;
    $query = $_GET['q'] ?? '';
    $status = $_GET['status'] ?? '';
    $from = $_GET['from'] ?? '';
    $to = $_GET['to'] ?? '';
    $type = $_GET['type'] ?? '';
    $discountModel = new DiscountModel();
    $discounts = $discountModel->getDiscounts($page, $itemsPerPage, $query, $status, $from, $to, $type);
    $totalRecords = $discountModel->getDiscountsCount($query, $status, $from, $to, $type);
    $totalPages = ceil($totalRecords / $itemsPerPage);

    View::renderTemplate("Discounts", [
      "title" => "Discounts",
      "discounts" => $discounts,
      "totalPages" => $totalPages,
    ]);
  }

  public function createDiscount()
  {
    if (!RBACService::hasPermission('add_discounts')) {
      $_SESSION['message'] = 'You do not have permission to create discounts';
      $_SESSION['message_type'] = 'error';
      View::redirect('/dashboard');
      return;
    }

    $_POST['end_date'] = $_POST['end_date'] ?: null;
    $_POST['is_combinable'] = isset($_POST['is_combinable']) ? 1 : 0;

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
    if (!RBACService::hasPermission('edit_discounts')) {
      $_SESSION['message'] = 'You do not have permission to edit discounts';
      $_SESSION['message_type'] = 'error';
      View::redirect('/dashboard');
      return;
    }

    $discountModel = new DiscountModel();
    if (!$params['id'] || !$discountModel->discountExists($params['id'])) {
      $_SESSION['message'] = 'Discount not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/discounts');
      return;
    }

    $_POST['end_date'] = $_POST['end_date'] ?: null;
    $_POST['is_combinable'] = isset($_POST['is_combinable']) ? 1 : 0;

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
    if (!RBACService::hasPermission('delete_discounts')) {
      $_SESSION['message'] = 'You do not have permission to delete discounts';
      $_SESSION['message_type'] = 'error';
      View::redirect('/dashboard');
      return;
    }

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
    if (!RBACService::hasPermission('activate_discounts')) {
      $_SESSION['message'] = 'You do not have permission to activate discounts';
      $_SESSION['message_type'] = 'error';
      View::redirect('/dashboard');
      return;
    }

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
    if (!RBACService::hasPermission('deactivate_discounts')) {
      $_SESSION['message'] = 'You do not have permission to deactivate discounts';
      $_SESSION['message_type'] = 'error';
      View::redirect('/dashboard');
      return;
    }

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

  public function getDiscounts()
  {
    if (!RBACService::hasPermission('view_discounts')) {
      self::sendJSON([
        "success" => false,
        "message" => "You do not have permission to view discounts"
      ]);
      return;
    }
    $data = self::recvJSON();

    if (isset($data['customer_id'])) {
      $customerModel = new CustomerModel();
      $points = $customerModel->getLoyaltyPoints($data['customer_id']);
    }

    $discountModel = new DiscountModel();
    $discounts = $discountModel->getDiscounts(null, null, null, 1, date('Y-m-d'), date('Y-m-d'), "regular", null);

    $selectedDiscounts = DiscountService::calculateOptimalDiscounts(
      $data['items'],
      $discounts,
      isset($points) ? $points : null
    );

    self::sendJSON([
      "success" => true,
      "data" => $selectedDiscounts,
    ]);
  }
}
