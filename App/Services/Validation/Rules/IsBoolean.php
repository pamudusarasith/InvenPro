<?php

namespace App\Services\Validation\Rules;

use App\Services\Validation\Rule;

class IsBoolean extends Rule
{
  private bool $strict;

  public function __construct(bool $strict = false, ?string $message = null)
  {
    $this->strict = $strict;
    $this->message = $message ?: "This field must be a boolean value";
  }

  public function apply($value, string $field, array $data): bool
  {
    if ($value === null || $value === '') {
      return true; // Skip validation if empty
    }

    if ($this->strict) {
      return is_bool($value);
    }

    $falseValues = [false, 0, '0', 'false', 'no', 'off', ''];
    $trueValues = [true, 1, '1', 'true', 'yes', 'on'];

    return in_array($value, $falseValues, true) || in_array($value, $trueValues, true);
  }
}
