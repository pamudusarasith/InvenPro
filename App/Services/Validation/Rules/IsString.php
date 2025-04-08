<?php

namespace App\Services\Validation\Rules;

use App\Services\Validation\Rule;

class IsString extends Rule
{
  private $minLength;
  private $maxLength;

  public function __construct(?int $minLength = null, ?int $maxLength = null, ?string $message = null)
  {
    $this->minLength = $minLength;
    $this->maxLength = $maxLength;

    if ($minLength !== null && $maxLength !== null) {
      $this->message = $message ?: "This field must be a string with {$minLength} to {$maxLength} characters";
    } elseif ($minLength !== null) {
      $this->message = $message ?: "This field must be a string with at least {$minLength} characters";
    } elseif ($maxLength !== null) {
      $this->message = $message ?: "This field must be a string with no more than {$maxLength} characters";
    } else {
      $this->message = $message ?: "This field must be a string";
    }
  }

  public function apply($value, string $field, array $data): bool
  {
    if ($value === null || $value === '') {
      return true; // Skip validation if empty (use Required rule for this check)
    }

    if (!is_string($value)) {
      return false;
    }

    if ($this->minLength !== null && mb_strlen($value) < $this->minLength) {
      return false;
    }

    if ($this->maxLength !== null && mb_strlen($value) > $this->maxLength) {
      return false;
    }

    return true;
  }
}
