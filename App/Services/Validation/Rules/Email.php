<?php

namespace App\Services\Validation\Rules;

use App\Services\Validation\Rule;

class Email extends Rule
{
  public function __construct(?string $message = null)
  {
    $this->message = $message ?: "This field must be a valid email address";
  }

  public function apply($value, string $field, array $data): bool
  {
    if (empty($value)) {
      return true; // Skip validation if empty (use Required rule for this check)
    }

    return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
  }
}
