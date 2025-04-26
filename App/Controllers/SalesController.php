<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;

class SalesController extends Controller
{
  public function index()
  {
    View::renderTemplate('Sales', [
      'title' => 'Sales',
    ]);
  }

  public function salesList()
  {
    View::renderTemplate('SalesList', [
      'title' => 'Sales List',
    ]);
  }
}