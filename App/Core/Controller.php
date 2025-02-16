<?php

namespace App\Core;

use App\Services\ValidationService;

class Controller
{
  protected $validator;

  public function __construct()
  {
    $this->validator = new ValidationService();
  }

  protected static function recvJSON(): array
  {
    return json_decode(file_get_contents('php://input'), true);
  }

  protected static function sendJSON(array $data): void
  {
    header('Content-Type: application/json');
    echo json_encode($data);
  }
}
