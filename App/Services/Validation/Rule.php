<?php

namespace App\Services\Validation;

use DateTime;

abstract class Rule
{
  protected ?string $message;

  /**
   * Validate the value
   *
   * @param mixed $value The value to validate
   * @param string $field The field name
   * @param array $data The data array
   * @return bool True if valid, false otherwise
   */
  abstract public function apply($value, string $field, array $data): bool;

  /**
   * Get the error message
   *
   * @return string
   */
  public function getMessage(): string
  {
    return $this->message ?? 'Validation failed';
  }

  protected function parseDate($value, string $format = 'Y-m-d'): ?DateTime
  {
    if ($value instanceof DateTime) {
      return $value;
    }
    if (!is_string($value)) {
      return null;
    }

    $date = DateTime::createFromFormat($format, $value);
    return $date && $date->format($format) === $value ? $date : null;
  }
}
