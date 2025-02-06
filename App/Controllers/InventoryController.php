<?php

namespace App\Controllers;

use App\Core\View;

class InventoryController
{
  public function index()
  {
    View::renderTemplate('Inventory', [
      'title' => 'Inventory'
    ]);
  }
}
