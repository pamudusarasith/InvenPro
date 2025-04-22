<?php

namespace App\Services\Validation\Rules;

use App\Services\Validation\Rule;

class IsDateTime extends Rule
{
  private string $format;

  public function __construct(string $format = 'Y-m-d', ?string $message = null)
  {
    $this->format = $format;
    $this->message = $message ?: "This field must be a valid date in format {$format}";
  }

  public function apply($value, string $field, array $data): bool
  {
    if (empty($value)) {
      return true; // Skip validation if empty (use Required rule for this check)
    }

    return $this->parseDate($value, $this->format) !== null;
  }
}
