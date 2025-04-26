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
    $page = $_GET['p'] ?? 1;
    $itemsPerPage = $_GET['ipp'] ?? 10;
    $page = max(1, (int) ($_GET['p'] ?? 1));
    $itemsPerPage = (int) ($_GET['ipp'] ?? 10);
    $branchModel = new BranchModel();
    $branches = $branchModel->getBranches($page, $itemsPerPage);
    
    

    View::renderTemplate('Branches', [
      'title' => 'Manage Branches',
      'branches' => $branches,
      'page' => $page,
      'itemsPerPage' => $itemsPerPage,
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
  
}
