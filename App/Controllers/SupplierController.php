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

    $supplierModel = new SupplierModel();
    $suppliers = $supplierModel->getSuppliers($page, $itemsPerPage);
    $totalRecords = $supplierModel->getSuppliersCount();
    $totalPages = ceil($totalRecords / $itemsPerPage);

    $branchModel = new BranchModel();
    $branches = $branchModel->getBranches();

    View::renderTemplate('Suppliers', [
      'title' => 'Suppliers',
      'suppliers' => $suppliers,
      'branches' => $branches,
      'page' => $page,
      'itemsPerPage' => $itemsPerPage,
      'totalPages' => $totalPages,
    ]);
  }

  public function details(array $params): void
  {
    $supplierModel = new SupplierModel();
    $supplier = $supplierModel->getSupplier($params['id']);

    $branchModel = new BranchModel();
    $branches = $branchModel->getBranches();

    if (!$supplier) {
      $_SESSION['message'] = 'Supplier not found';
      $_SESSION['message_type'] = 'error';
      View::redirect('/suppliers');
    }

    View::renderTemplate('SupplierDetails', [
      'title' => 'Supplier Details',
      'supplier' => $supplier,
      'branches' => $branches,
    ]);
  }

  public function search(): void
  {
    $query = $_GET['q'] ?? '';
    $page = $_GET['p'] ?? 1;
    $itemsPerPage = $_GET['ipp'] ?? 10;

    $supplierModel = new SupplierModel();
    $suppliers = $supplierModel->searchSuppliers($query, $page, $itemsPerPage);

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
}
