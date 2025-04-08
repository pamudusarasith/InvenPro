<?php

namespace App\Services\Validation\Rules;

use App\Services\Validation\Rule;

class IsNumeric extends Rule
{
  public function __construct(?string $message = null)
  {
    $this->message = $message ?: "This field must be a number";
  }

  public function apply($value, string $field, array $data): bool
  {
    return is_numeric($value);
  }
}
