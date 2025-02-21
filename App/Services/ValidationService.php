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

  public function validateCreateUser(array $data): bool
  {
    $this->error = '';

    $this->validateEmail($data);
    if (!array_key_exists('role_id', $data) || empty($data['role_id'])) {
      $this->error = 'Role is required';
    } elseif (!array_key_exists('branch_id', $data) || empty($data['branch_id'])) {
      $this->error = 'Branch is required';
    }

    return $this->error === '';
  }

  public function validateUpdateUser(array $data): bool
  {
    $this->error = '';

    $this->validateEmail($data);
    if (!array_key_exists('role_id', $data) || empty($data['role_id'])) {
      $this->error = 'Role is required';
    } elseif (!array_key_exists('branch_id', $data) || empty($data['branch_id'])) {
      $this->error = 'Branch is required';
    } elseif (!array_key_exists('status', $data) || empty($data['status'])) {
      $this->error = 'Status is required';
    }

    return $this->error === '';
  }

  public function validateCreateSupplier(array $data): bool
  {
    $this->error = '';

    $this->validateEmail($data);
    if (!array_key_exists('supplier_name', $data) || empty($data['supplier_name'])) {
      $this->error = 'Supplier name is required';
    } elseif (!array_key_exists('contact_person', $data) || empty($data['contact_person'])) {
      $this->error = 'Contact person is required';
    } elseif (!array_key_exists('phone', $data) || empty($data['phone'])) {
      $this->error = 'Phone is required';
    } elseif (!array_key_exists('branch_id', $data) || empty($data['branch_id'])) {
      $this->error = 'Branch is required';
    } elseif (!array_key_exists('address', $data) || empty($data['address'])) {
      $this->error = 'Address is required';
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
