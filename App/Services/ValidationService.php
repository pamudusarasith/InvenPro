<?php

namespace App\Services;

class ValidationService
{
  /**
   * Error string to store validation error message
   */
  private string $error = '';

  public function validateLogin(array $data): bool
  {
    $this->error = '';

    $this->validateEmail($data);
    if (!array_key_exists('password', $data) || empty($data['password'])) {
      $this->error = 'Password is required';
    }

    return $this->error === '';
  }

  public function validateEmail(array $data): void
  {
    if (!array_key_exists('email', $data) || empty($data['email'])) {
      $this->error = 'Email is required';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
      $this->error = 'Invalid email format';
    }
  }

  public function validateCheckout(array $data): bool
  {
    $this->error = '';

    if (!array_key_exists('items', $data) || empty($data['items'])) {
      $this->error = 'Items are required';
    } elseif (!is_array($data['items'])) {
      $this->error = 'Items must be an array';
    } elseif (count($data['items']) === 0) {
      $this->error = 'Items cannot be empty';
    } elseif (!array_key_exists('payment_method', $data) || empty($data['payment_method'])) {
      $this->error = 'Payment method is required';
    }

    return $this->error === '';
  }

  /**
   * Get validation error
   *
   * @return string
   */
  public function getError(): string
  {
    return $this->error;
  }
}
