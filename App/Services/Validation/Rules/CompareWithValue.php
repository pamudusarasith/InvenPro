<?php

namespace App\Services\Validation\Rules;

use App\Services\Validation\Rule;
use DateTime;

class CompareWithValue extends Rule
{
  private $operator;
  private $compareValue;
  private $type;
  private $format;

  /**
   * @param string $operator The comparison operator ('==', '>', '<', etc.)
   * @param mixed $compareValue The value to compare against
   * @param string|null $type The type to use for comparison ('auto', 'numeric', 'string', 'date', 'boolean', 'array')
   * @param string|null $message Custom error message
   * @param string|null $format Format for date comparison (if applicable)
   */
  public function __construct(string $operator, $compareValue, ?string $type = 'auto', ?string $message = null, string $format = 'Y-m-d')
  {
    $this->operator = $operator;
    $this->compareValue = $compareValue;
    $this->type = $type;
    $this->format = $format;

    // If no custom message is provided, create a helpful default message
    if (!$message) {
      $message = $this->createDefaultMessage($operator, $compareValue, $type);
    }

    $this->message = $message;
  }

  public function apply($value, string $field, array $data): bool
  {
    if (in_array($value, [null, '', []])) {
      return true; // Skip validation if null (use Required rule for this check)
    }

    // Handle specific type conversions based on the specified type
    switch ($this->type) {
      case 'numeric':
        return $this->compareNumeric($value, $this->compareValue, $this->operator);

      case 'string':
        return $this->compareString((string)$value, (string)$this->compareValue, $this->operator);

      case 'date':
        $dateValue = $this->parseDate($value, $this->format);
        $dateCompareValue = $this->parseDate($this->compareValue, $this->format);

        if ($dateValue === null || $dateCompareValue === null) {
          return false; // Invalid date values
        }

        return $this->compareDates($dateValue, $dateCompareValue, $this->operator);

      case 'boolean':
        return $this->compareBoolean($value, $this->compareValue, $this->operator);

      case 'array':
        if (!is_array($value) || !is_array($this->compareValue)) {
          return false; // Both values must be arrays
        }
        return $this->compareArrays($value, $this->compareValue, $this->operator);

      case 'auto':
      default:
        // In auto mode, first check the type of compareValue and match
        // the input value to it if possible

        // If compareValue is DateTime
        if (
          ($value instanceof DateTime || $this->parseDate($value) !== null) &&
          ($this->compareValue instanceof DateTime || $this->parseDate($this->compareValue) !== null)
        ) {
          $dateValue = $this->parseDate($value);
          $dateCompareValue = $this->parseDate($this->compareValue);
          if ($dateValue !== null && $dateCompareValue !== null) {
            return $this->compareDates($dateValue, $dateCompareValue, $this->operator);
          }
        }

        // If compareValue is boolean
        if (is_bool($this->compareValue)) {
          // Only convert to boolean if value is scalar (can be sensibly cast to bool)
          if (!is_scalar($value) && !is_null($value)) {
            return false; // Cannot compare non-scalar to boolean
          }
          return $this->compareBoolean($value, $this->compareValue, $this->operator);
        }

        // If compareValue is array
        if (is_array($this->compareValue)) {
          if (!is_array($value)) {
            return false; // Cannot compare non-array to array
          }
          return $this->compareArrays($value, $this->compareValue, $this->operator);
        }

        // If compareValue is numeric
        if (is_numeric($this->compareValue)) {
          if (!is_numeric($value)) {
            return false; // Cannot compare non-numeric to numeric
          }
          return $this->compareNumeric($value, $this->compareValue, $this->operator);
        }

        try {
          $value = (string)$value;
          $this->compareValue = (string)$this->compareValue;
        } catch (\Throwable $e) {
          return false; // Cannot convert to string
        }
        return $this->compareString((string)$value, (string)$this->compareValue, $this->operator);
    }
  }

  /**
   * Compare numeric values
   */
  private function compareNumeric($value, $compareValue, string $operator): bool
  {
    $value = (float) $value;
    $compareValue = (float) $compareValue;

    return $this->compare($value, $compareValue, $operator);
  }

  /**
   * Compare string values
   */
  private function compareString(string $value, string $compareValue, string $operator): bool
  {
    return $this->compare($value, $compareValue, $operator);
  }

  /**
   * Compare boolean values
   */
  private function compareBoolean($value, $compareValue, string $operator): bool
  {
    $value = (bool) $value;
    $compareValue = (bool) $compareValue;

    // Only certain operators make sense for booleans
    switch ($operator) {
      case '=':
      case '==':
      case '===':
        return $value === $compareValue;
      case '!=':
      case '<>':
      case '!==':
        return $value !== $compareValue;
      default:
        // For boolean, other operators don't make much sense
        // but we can still interpret them based on false < true
        return $this->compare($value, $compareValue, $operator);
    }
  }

  /**
   * Compare array values
   */
  private function compareArrays(array $value, array $compareValue, string $operator): bool
  {
    switch ($operator) {
      case '=':
      case '==':
        return $value == $compareValue;
      case '===':
        return $value === $compareValue;
      case '!=':
      case '<>':
        return $value != $compareValue;
      case '!==':
        return $value !== $compareValue;
      case '>':
        return count($value) > count($compareValue);
      case '>=':
        return count($value) >= count($compareValue);
      case '<':
        return count($value) < count($compareValue);
      case '<=':
        return count($value) <= count($compareValue);
      default:
        throw new \InvalidArgumentException("Unsupported operator: {$operator}");
    }
  }

  /**
   * General comparison function
   */
  private function compare($value, $compareValue, string $operator): bool
  {
    switch ($operator) {
      case '=':
      case '==':
        return $value == $compareValue;
      case '===':
        return $value === $compareValue;
      case '!=':
      case '<>':
        return $value != $compareValue;
      case '!==':
        return $value !== $compareValue;
      case '>':
        return $value > $compareValue;
      case '>=':
        return $value >= $compareValue;
      case '<':
        return $value < $compareValue;
      case '<=':
        return $value <= $compareValue;
      default:
        throw new \InvalidArgumentException("Unsupported operator: {$operator}");
    }
  }

  /**
   * Compare two DateTime objects with the given operator
   */
  private function compareDates(DateTime $dateValue, DateTime $compareValue, string $operator): bool
  {
    switch ($operator) {
      case '=':
      case '==':
      case '===':
        return $dateValue == $compareValue;
      case '!=':
      case '<>':
      case '!==':
        return $dateValue != $compareValue;
      case '>':
        return $dateValue > $compareValue;
      case '>=':
        return $dateValue >= $compareValue;
      case '<':
        return $dateValue < $compareValue;
      case '<=':
        return $dateValue <= $compareValue;
      default:
        return false;
    }
  }

  /**
   * Create a user-friendly error message based on operator and value type
   */
  private function createDefaultMessage(string $operator, $compareValue, ?string $type): string
  {
    // Get user-friendly version of the operator
    $operatorText = $this->getOperatorText($operator);

    // Format the comparison value based on its type
    $formattedValue = $this->formatValueForMessage($compareValue, $type);

    // Create appropriate message based on type
    switch ($type) {
      case 'numeric':
        return "This field must be {$operatorText} {$formattedValue}";

      case 'string':
        return "This text must be {$operatorText} \"{$formattedValue}\"";

      case 'date':
        return "This date must be {$operatorText} {$formattedValue}";

      case 'boolean':
        return "This field must be {$operatorText} {$formattedValue}";

      case 'array':
        return "This array must be {$operatorText} {$formattedValue}";

      case 'auto':
      default:
        return "This field must be {$operatorText} {$formattedValue}";
    }
  }

  /**
   * Convert operator symbols to user-friendly text
   */
  private function getOperatorText(string $operator): string
  {
    switch ($operator) {
      case '=':
      case '==':
      case '===':
        return "equal to";
      case '!=':
      case '<>':
      case '!==':
        return "not equal to";
      case '>':
        return "greater than";
      case '>=':
        return "greater than or equal to";
      case '<':
        return "less than";
      case '<=':
        return "less than or equal to";
      default:
        return $operator; // Fallback to original operator if unknown
    }
  }

  /**
   * Format value for display in error messages
   */
  private function formatValueForMessage($value, ?string $type): string
  {
    if ($value === null) {
      return "null";
    }

    if (is_bool($value)) {
      return $value ? "true" : "false";
    }

    if (is_array($value)) {
      if ($type === 'array' && in_array($this->operator, ['>', '>=', '<', '<='])) {
        return "an array with " . count($value) . " items";
      }
      return "the specified array";
    }

    if ($value instanceof DateTime) {
      return $value->format('Y-m-d H:i:s');
    }

    if (is_object($value)) {
      return "the specified " . get_class($value);
    }

    // For other types, just convert to string
    return (string)$value;
  }
}
