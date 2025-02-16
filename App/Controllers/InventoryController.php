<?php

namespace App\Controllers;

use App\Core\{Controller, View};

class InventoryController extends Controller
{
  public function index()
  {
    View::renderTemplate('Inventory', [
      'title' => 'Inventory'
    ]);
  }
}
