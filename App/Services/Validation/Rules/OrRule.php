<?php

namespace App\Services\Validation\Rules;

use App\Services\Validation\Rule;

class OrRule extends Rule
{
  private $rules = [];
  private $failedMessages = [];

  /**
   * Create a rule that passes if any of the provided rules pass
   *
   * @param array $rules Array of rules (Rule objects or callables)
   * @param string|null $message Custom error message
   */
  public function __construct(array $rules, ?string $message = null)
  {
    foreach ($rules as $rule) {
      if (!($rule instanceof Rule) && !is_callable($rule)) {
        throw new \InvalidArgumentException("All items must be instances of Rule or callable functions");
      }
    }

    $this->rules = $rules;
    $this->message = $message ?: "All validation conditions failed";
  }

  public function apply($value, string $field, array $data): bool
  {
    $this->failedMessages = [];

    foreach ($this->rules as $rule) {
      if ($rule instanceof Rule) {
        if ($rule->apply($value, $field, $data)) {
          return true; // Rule passed
        }
        // Store failed message
        $this->failedMessages[] = $rule->getMessage();
      } elseif (is_callable($rule)) {
        // Handle callable rule
        if (call_user_func($rule, $value, $field, $data)) {
          return true; // Callable rule passed
        }
        // Store a generic message for callable rules
        $this->failedMessages[] = "Callable validation failed";
      }
    }

    return false; // If all rules fail, return false
  }

  /**
   * Get the error message. If no custom message was provided,
   * returns all failed rule messages combined.
   */
  public function getMessage(): string
  {
    // If a custom message was provided, use it
    if ($this->message !== "All validation conditions failed") {
      return $this->message;
    }

    // Otherwise, combine all the failed messages
    if (count($this->failedMessages) > 0) {
      return "None of these conditions were met: " . implode("; ", $this->failedMessages);
    }

    return $this->message;
  }
}
