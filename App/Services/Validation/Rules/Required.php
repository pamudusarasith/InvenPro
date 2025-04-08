<?php

namespace App\Services\Validation\Rules;

use App\Services\Validation\Rule;

class Required extends Rule
{
  public function __construct(?string $message = null)
  {
    $this->message = $message ?: "This field is required";
  }

  public function apply($value, string $field, array $data): bool
  {
    if ($value === null) {
      return false; // Null values are invalid for required fields
    } elseif (is_string($value)) {
      return trim($value) !== '';
    } elseif (is_array($value)) {
      return !empty($value);
    }

    return true; // Any other non-null values are considered valid
  }
}
