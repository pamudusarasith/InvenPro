<?php

namespace App\Services\Validation\Rules;

use App\Services\Validation\Rule;

class Matches extends Rule
{
  private string $pattern;

  public function __construct(string $pattern, ?string $message = null)
  {
    $this->pattern = $pattern;
    $this->message = $message ?: "This field does not match the required pattern";
  }

  public function apply($value, string $field, array $data): bool
  {
    if (empty($value) && $value !== '0') {
      return true; // Skip validation if empty
    }

    return is_string($value) && preg_match($this->pattern, $value) === 1;
  }
}
