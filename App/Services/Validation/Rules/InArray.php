<?php

namespace App\Services\Validation\Rules;

use App\Services\Validation\Rule;

class InArray extends Rule
{
  private array $allowedValues;
  private bool $strict;

  public function __construct(array $allowedValues, ?string $message = null, bool $strict = true)
  {
    $this->allowedValues = $allowedValues;
    $this->strict = $strict;
    $this->message = $message ?: "This field must be one of the allowed values: " . implode(', ', $allowedValues);
  }

  public function apply($value, string $field, array $data): bool
  {
    if (empty($value) && $value !== false && $value !== 0 && $value !== '0') {
      return true; // Skip validation if empty
    }

    return in_array($value, $this->allowedValues, $this->strict);
  }
}
