<?php

namespace App\Services\Validation;

use InvalidArgumentException;
use App\Services\Validation\Utils\DataAccessor;

class Validator
{
  private $data = [];
  private $errors = [];
  private $rules = [];

  public function __construct(array $data = [])
  {
    $this->data = $data;
  }

  public function setData(array $data)
  {
    $this->data = $data;
    return $this;
  }

  public function rule(string $field, $rule, ?string $message = null)
  {
    if (!isset($this->rules[$field])) {
      $this->rules[$field] = [];
    }

    // Validate rule is a proper type
    if (!($rule instanceof Rule) && !is_callable($rule)) {
      throw new InvalidArgumentException('Rule must implement Rule interface or be callable');
    }

    $this->rules[$field][] = [
      'rule' => $rule,
      'message' => $message
    ];

    return $this;
  }

  public function validate()
  {
    $this->errors = [];

    foreach ($this->rules as $field => $rules) {
      foreach ($rules as $ruleData) {
        $rule = $ruleData['rule'];
        $message = $ruleData['message'];

        // Get data for the field
        $value = $this->getData($field);

        // If field has wildcard, handle it with special validation
        if (strpos($field, '*') !== false) {
          $this->validateWithWildcards($field, $value, $rule, $message);
        } else {
          $this->applyRule($field, $value, $rule, $message);
        }
      }
    }

    return empty($this->errors);
  }

  private function validateWithWildcards($field, $value, $rule, $message)
  {
    // Handle case where DataAccessor::getValue() returned null for wildcard path
    if ($value === null) {
      $this->addError($field, "Path doesn't exist or cannot apply wildcard to non-array");
      return;
    }

    // If the value is empty array, no validation needed
    if (empty($value)) {
      return;
    }

    // Apply the rule to each value in the wildcard result
    foreach ($value as $index => $item) {
      // Create a specific field path for this item by replacing only the first wildcard
      $pos = strpos($field, '*');
      $itemField = substr_replace($field, $index, $pos, 1);

      // If the value is an array and the field still has wildcards, recursively validate
      if (is_array($item) && strpos($itemField, '*') !== false) {
        $this->validateWithWildcards($itemField, $item, $rule, $message);
      } else {
        // Apply the rule to this specific value
        $this->applyRule($itemField, $item, $rule, $message);
      }
    }
  }

  private function applyRule($field, $value, $rule, $message)
  {
    if ($rule instanceof Rule) {
      if (!$rule->apply($value, $field, $this->data)) {
        $this->addError($field, $message ?: $rule->getMessage());
      }
    } elseif (is_callable($rule)) {
      if (!call_user_func($rule, $value, $field, $this->data)) {
        $this->addError($field, $message ?: "Validation failed for {$field}");
      }
    }
  }

  public function errors()
  {
    return $this->errors;
  }

  private function getData($field)
  {
    return DataAccessor::getValue($this->data, $field);
  }

  private function addError($field, $message)
  {
    if (!isset($this->errors[$field])) {
      $this->errors[$field] = [];
    }

    $this->errors[$field][] = $message;
  }
}
