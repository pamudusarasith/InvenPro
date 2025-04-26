<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;

class BranchController extends Controller
{
  public function index()
  {
    View::renderTemplate('Branches', [
      'title' => 'Manage Branches',
    ]);
  }
}
