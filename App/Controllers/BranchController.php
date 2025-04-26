<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\BranchModel;
use App\Services\ValidationService;

class BranchController extends Controller
{

  public function index(): void
  {
      $page = $_GET['p'] ?? 1; // Current page number
      $itemsPerPage = $_GET['ipp'] ?? 10; // Items per page
      $page = max(1, (int) ($_GET['p'] ?? 1)); // Ensure the page is at least 1
      $itemsPerPage = (int) ($_GET['ipp'] ?? 10); // Convert items per page to an integer
      $search = $_GET['search'] ?? ''; // Search query
      $status = $_GET['status'] ?? ''; // Status filter (active/inactive)

      $branchModel = new BranchModel();
      $branches = $branchModel->getBranches($page, $itemsPerPage, $search, $status); // Fetch paginated branches
      $totalRecords = $branchModel->getBranchesCount($search, $status); // Get total branch count
      $totalPages = ceil($totalRecords / $itemsPerPage); // Calculate total pages

      View::renderTemplate('Branches', [
          'title' => 'Branches',
          'branches' => $branches,
          'page' => $page,
          'itemsPerPage' => $itemsPerPage,
          'totalPages' => $totalPages,
          'totalRecords' => $totalRecords,
          'search' => $search,
          'status' => $status
      ]);
  }
  public function createBranch(): void
  {
      $validator = new ValidationService();
      if (!$validator->validateCreateBranch($_POST)) {
          $this->index($validator->getError(), 'error');
          return;
      }
      $branchModel = new BranchModel();
      $branchModel->createBranch($_POST);

      $_SESSION['message'] = 'Branch created successfully';
      $_SESSION['message_type'] = 'success';
      View::redirect('/branches');
  }

  public function updateBranch(array $params)
  {
      if (!$this->validator->validateUpdateBranch($_POST)) {
          $_SESSION['message'] = $this->validator->getError();
          $_SESSION['message_type'] = 'error';
          View::redirect('/branches');
      }

      $branchModel = new BranchModel();
      $_POST['id'] = $params['id'];
      $branchModel->updateBranch($_POST);
      $_SESSION['message'] = 'Branch updated successfully';
      $_SESSION['message_type'] = 'success';
      View::redirect('/branches');
  }


  public function updateDeletedAt(array $params)
  {
      $branchModel = new BranchModel();
      $deletedAt = $params['deleted_at'] ?? null;
  
      if (!$deletedAt) {
          $_SESSION['message'] = 'Invalid data provided for branch status update.';
          $_SESSION['message_type'] = 'error';
          View::redirect('/branches');
      }
  
      $branchModel->updateDeletedAt($params['id'], $deletedAt);
      $_SESSION['message'] = $deletedAt ? 'Branch deactivated successfully' : 'Branch restored successfully';
      $_SESSION['message_type'] = 'success';
      View::redirect('/branches');
  }


  public function search(): void
  {
      $query = $_GET['q'] ?? ''; // Search query
      $page = max(1, (int) ($_GET['p'] ?? 1)); // Current page
      $itemsPerPage = (int) ($_GET['ipp'] ?? 10); // Items per page

      $branchModel = new BranchModel();
      $branches = $branchModel->searchBranches($query, $page, $itemsPerPage); // Fetch filtered branches

      self::sendJSON([
          'success' => true,
          'data' => $branches,
      ]);
  }

  
  
}
