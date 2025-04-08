<?php

namespace App\Services\Validation\Rules;

use App\Services\Validation\Rule;

class IsArray extends Rule
{
  private $minSize;
  private $maxSize;

  public function __construct(?int $minSize = null, ?int $maxSize = null, ?string $message = null)
  {
    $this->minSize = $minSize;
    $this->maxSize = $maxSize;

    if ($minSize !== null && $maxSize !== null) {
      $this->message = $message ?: "This field must be an array with {$minSize} to {$maxSize} items";
    } elseif ($minSize !== null) {
      $this->message = $message ?: "This field must be an array with at least {$minSize} items";
    } elseif ($maxSize !== null) {
      $this->message = $message ?: "This field must be an array with no more than {$maxSize} items";
    } else {
      $this->message = $message ?: "This field must be an array";
    }
  }

  public function apply($value, string $field, array $data): bool
  {
    if ($value === null || $value === []) {
      return true; // Skip validation if empty (use Required rule for this check)
    }

    if (!is_array($value)) {
      return false;
    }

    if ($this->minSize !== null && count($value) < $this->minSize) {
      return false;
    }

    if ($this->maxSize !== null && count($value) > $this->maxSize) {
      return false;
    }

    return true;
  }
}
