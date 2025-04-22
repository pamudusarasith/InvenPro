<?php

namespace App\Services\Validation\Rules;

use App\Services\Validation\Rule;
use App\Services\Validation\Utils\DataAccessor;
use DateTime;

class CompareWithField extends Rule
{
  private $operator;
  private $compareField;
  private $type;

  /**
   * @param string $operator The comparison operator ('==', '>', '<', etc.)
   * @param string $compareField The field name to compare with
   * @param string|null $type The type to use for comparison ('auto', 'numeric', 'string', 'date', 'boolean', 'array')
   * @param string|null $message Custom error message
   */
  public function __construct(string $operator, string $compareField, ?string $type = 'auto', ?string $message = null)
  {
    $this->operator = $operator;
    $this->compareField = $compareField;
    $this->type = $type;

    // If no custom message is provided, create a helpful default message
    if (!$message) {
      $message = $this->createDefaultMessage($operator, $compareField, $type);
    }

    $this->message = $message;
  }

  public function apply($value, string $field, array $data): bool
  {
    if (in_array($value, [null, '', []]) || in_array($this->compareField, [null, '', []])) {
      return true;
    }

    // Get the value of the field to compare with
    $compareValue = DataAccessor::getValue($data, $this->compareField);

    // Handle specific type conversions based on the specified type
    switch ($this->type) {
      case 'numeric':
        if (!is_numeric($value) || !is_numeric($compareValue)) {
          return false;
        }
        return $this->compareNumeric($value, $compareValue, $this->operator);

      case 'string':
        return $this->compareString((string)$value, (string)$compareValue, $this->operator);

      case 'date':
        $dateValue = $this->parseDate($value);
        $dateCompareValue = $this->parseDate($compareValue);

        if ($dateValue === null || $dateCompareValue === null) {
          return false; // Invalid date values
        }

        return $this->compareDates($dateValue, $dateCompareValue, $this->operator);

      case 'boolean':
        if (!$this->canCastToBoolean($value) || !$this->canCastToBoolean($compareValue)) {
          return false;
        }
        return $this->compareBoolean($value, $compareValue, $this->operator);

      case 'array':
        if (!is_array($value) || !is_array($compareValue)) {
          return false; // Both values must be arrays
        }
        return $this->compareArrays($value, $compareValue, $this->operator);

      case 'auto':
      default:
        // In auto mode, determine the type based on the compared field's value type
        if (
          ($value instanceof DateTime || $this->parseDate($value) !== null) &&
          ($compareValue instanceof DateTime || $this->parseDate($compareValue) !== null)
        ) {
          $dateValue = $this->parseDate($value);
          $dateCompareValue = $this->parseDate($compareValue);

          if ($dateValue !== null && $dateCompareValue !== null) {
            return $this->compareDates($dateValue, $dateCompareValue, $this->operator);
          }
        }

        if (is_bool($compareValue)) {
          if (!$this->canCastToBoolean($value)) {
            return false;
          }
          return $this->compareBoolean($value, $compareValue, $this->operator);
        }

        if (is_array($compareValue)) {
          if (!is_array($value)) {
            return false;
          }
          return $this->compareArrays($value, $compareValue, $this->operator);
        }

        if (is_numeric($compareValue)) {
          if (!is_numeric($value)) {
            return false;
          }
          return $this->compareNumeric($value, $compareValue, $this->operator);
        }

        // Default to string comparison
        try {
          $value = (string)$value;
          $compareValue = (string)$compareValue;
        } catch (\Throwable $e) {
          return false; // Cannot convert to string
        }

        return $this->compareString((string)$value, (string)$compareValue, $this->operator);
    }
  }

  /**
   * Check if a value can be safely cast to boolean
   */
  private function canCastToBoolean($value): bool
  {
    return is_scalar($value) || is_null($value);
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
   * Create a user-friendly error message based on operator and field
   */
  private function createDefaultMessage(string $operator, string $compareField, ?string $type): string
  {
    // Get user-friendly version of the operator
    $operatorText = $this->getOperatorText($operator);

    // Create appropriate message based on type
    switch ($type) {
      case 'numeric':
        return "This field must be {$operatorText} the value in {$compareField}";

      case 'string':
        return "This text must be {$operatorText} the text in {$compareField}";

      case 'date':
        return "This date must be {$operatorText} the date in {$compareField}";

      case 'boolean':
        return "This field must be {$operatorText} the value in {$compareField}";

      case 'array':
        if (in_array($this->operator, ['>', '>=', '<', '<='])) {
          return "This array must contain {$operatorText} items than the array in {$compareField}";
        }
        return "This array must be {$operatorText} the array in {$compareField}";

      case 'auto':
      default:
        return "This field must be {$operatorText} the value in {$compareField}";
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
}
