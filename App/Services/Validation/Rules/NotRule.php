<?php

namespace App\Services\Validation\Rules;

use App\Services\Validation\Rule;

class NotRule extends Rule
{
  private $rule;
  private $originalMessage;

  /**
   * Create a rule that passes if the provided rule fails
   *
   * @param Rule|callable $rule Rule or callable to negate
   * @param string|null $message Custom error message
   */
  public function __construct($rule, ?string $message = null)
  {
    if (!($rule instanceof Rule) && !is_callable($rule)) {
      throw new \InvalidArgumentException("Rule must be an instance of Rule or a callable");
    }

    $this->rule = $rule;
    $this->originalMessage = ($rule instanceof Rule) ? $rule->getMessage() : "Callable validation";
    $this->message = $message ?: "Validation condition should not be met";
  }

  public function apply($value, string $field, array $data): bool
  {
    // Return the negation of the rule's result
    if ($this->rule instanceof Rule) {
      return !$this->rule->apply($value, $field, $data);
    } else {
      // Handle callable rule
      return !call_user_func($this->rule, $value, $field, $data);
    }
  }

  /**
   * Get the error message
   */
  public function getMessage(): string
  {
    // If a custom message was provided, use it
    if ($this->message !== "Validation condition should not be met") {
      return $this->message;
    }

    // Otherwise, create a message referencing the negated rule
    return "Value should not: " . $this->originalMessage;
  }
}
