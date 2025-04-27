<?php

namespace App\Controllers;

use App\Core\{Controller, View};
use App\Models\{BranchModel, SupplierModel};
use App\Services\ValidationService;

class SupplierController extends Controller
{
  public function index(): void
  {
    $page = $_GET['p'] ?? 1;
    $itemsPerPage = $_GET['ipp'] ?? 10;
    $page = max(1, (int) ($_GET['p'] ?? 1));
    $itemsPerPage = (int) ($_GET['ipp'] ?? 10);
    $search = $_GET['search'] ?? '';
    $branchId = $_GET['branch'] ?? '';
    $status = $_GET['status'] ?? '';

    $supplierModel = new SupplierModel();
    $suppliers = $supplierModel->getSuppliers($page, $itemsPerPage, $search, $branchId, $status);
    $totalRecords = $supplierModel->getSuppliersCount();
    $totalPages = ceil($totalRecords / $itemsPerPage);

    $branchModel = new BranchModel();
    $branches = $branchModel->getBranches($page, $itemsPerPage, $search , $status);

    View::renderTemplate('Suppliers', [
      'title' => 'Suppliers',
      'suppliers' => $suppliers,
      'branches' => $branches,
      'page' => $page,
      'itemsPerPage' => $itemsPerPage,
      'totalPages' => $totalPages,
      'totalRecords' => $totalRecords,
      'search' => $search,
      'branchId' => $branchId,
      'status' => $status
    ]);
  }

  public function details(array $params): void
  {

    $page = $_GET['p'] ?? 1;
    $itemsPerPage = $_GET['ipp'] ?? 10;
    $page = max(1, (int) ($_GET['p'] ?? 1));
    $itemsPerPage = (int) ($_GET['ipp'] ?? 10);
    $search = $_GET['search'] ?? '';
    $branchId = $_GET['branch'] ?? '';
    $status = $_GET['status'] ?? '';
    
    $supplierModel = new SupplierModel();
    $supplier = $supplierModel->getSupplier($params['id']);

    $branchModel = new BranchModel();
    $branches = $branchModel->getBranches($page, $itemsPerPage, $search , $status);

    if (!$supplier) {
      $_SESSION['message'] = 'Supplier not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/suppliers');
    }
    $product = $supplierModel->getSupplierProducts($params['id']);
    $order = $supplierModel->getOrderDetails($params['id']);
    View::renderTemplate('SupplierDetails', [
      'title' => 'Supplier Details',
      'supplier' => $supplier,
      'branches' => $branches,
      'supplier_products' => $product,
      'orders' => $order,
    ]);
  }

  public function search(): void
  {
      $query = $_GET['q'] ?? ''; // Search query
      $page = max(1, (int) ($_GET['p'] ?? 1)); // Current page
      $itemsPerPage = (int) ($_GET['ipp'] ?? 10); // Items per page

      $supplierModel = new SupplierModel();
      $suppliers = $supplierModel->searchSuppliers($query, $page, $itemsPerPage); // Fetch filtered suppliers

      self::sendJSON([
          'success' => true,
          'data' => $suppliers,
      ]);
  }

  public function createSupplier(): void
  {
    $validator = new ValidationService();
    if (!$validator->validateCreateSupplier($_POST)) {
      $this->index($validator->getError(), 'error');
      return;
    }
    $supplierModel = new SupplierModel();
    $supplierModel->createSupplier($_POST);

    $_SESSION['message'] = 'Supplier created successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/suppliers');
  }

  public function updateSupplier(array $params): void
  {
    $validator = new ValidationService();
    if (!$validator->validateCreateSupplier($_POST)) {
      $this->details($params, $validator->getError(), 'error');
      return;
    }
    $supplierModel = new SupplierModel();
    $supplierModel->updateSupplier($params['id'], $_POST);

    $_SESSION['message'] = 'Supplier updated successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/suppliers/' . $params['id']);
  }

  public function deleteSupplier(array $params): void
  {
    $supplierModel = new SupplierModel();
    $supplierModel->deleteSupplier($params['id']);

    $_SESSION['message'] = 'Supplier deleted successfully';
    $_SESSION['message_type'] = 'success';
    View::redirect('/suppliers');
  }

  public function searchProducts(array $params): void
  {
    $query = $_GET['q'] ?? '';
    $page = $_GET['p'] ?? 1;
    $itemsPerPage = $_GET['ipp'] ?? 10;

    $supplierModel = new SupplierModel();
    $products = $supplierModel->searchProducts($params['id'], $query, $page, $itemsPerPage);

    self::sendJSON([
      'success' => true,
      'data' => $products,
    ]);
  }

  public function assignProduct(array $params): void
  {
    $supplierId = $params['id'];
    $supplierModel = new SupplierModel();
    $supplierModel->assignProduct($supplierId, $_POST); // Send post data to model

    $_SESSION['message'] = 'Product added successfully';
    $_SESSION['message_type'] = 'success';

    $supplierId = $_POST['supplier_id'];
    View::redirect('/suppliers/' . $params['id']);
  }

  public function deleteAssignedProduct(array $params): void
  {
    error_log("POST data: " . print_r($_POST, true));

      $productId = $_POST['product_id'] ?? null;
      if (!$productId) {
          $_SESSION['message'] = 'Invalid product';
          $_SESSION['message_type'] = 'error';
          View::redirect('/suppliers/' . $params['id']);
          return;
      }
  
      $supplierModel = new SupplierModel();
      $supplierModel->deleteAssignedProduct($productId, $params['id']);
;
  
      $_SESSION['message'] = 'Assigned product deleted successfully';
      $_SESSION['message_type'] = 'success';
      View::redirect('/suppliers/' . $params['id']);
  }
  

}
