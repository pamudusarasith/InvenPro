<?php

namespace App\Services;

use App\Services\Validation\Rules\{
  CompareWithField,
  CompareWithValue,
  Email,
  InArray,
  IsArray,
  IsBoolean,
  IsDate,
  IsNumeric,
  IsString,
  Matches,
  NotRule,
  OrRule,
  Required
};
use App\Services\Validation\Validator;

class ValidationService
{
  /**
   * Error string to store validation error message
   */
  private array $errors = [];

  public function validateLogin(array $data): bool
  {
    $validator = new Validator($data);
    $validator->rule('email', new Required('Email is required'))
      ->rule('email', new Email())
      ->rule('password', new Required('Password is required'));

    if (!$validator->validate()) {
      $this->errors = $validator->errors();
      return false;
    }
    return true;
  }

  public function validateCreateUser(array $data): bool
  {
    $validator = new Validator($data);
    $validator->rule('email', new Required('Email is required'))
      ->rule('email', new Email())
      ->rule('role_id', new Required('Role ID is required'))
      ->rule('role_id', new IsNumeric('Invalid Role ID'))
      ->rule('branch_id', new Required('Branch ID is required'))
      ->rule('branch_id', new IsNumeric('Invalid Branch ID'));

    if (!$validator->validate()) {
      $this->errors = $validator->errors();
      return false;
    }
    return true;
  }

  public function validateUpdateUser(array $data): bool
  {
    $validator = new Validator($data);
    $validator->rule('email', new Required('Email is required'))
      ->rule('email', new Email())
      ->rule('role_id', new Required('Role ID is required'))
      ->rule('role_id', new IsNumeric('Invalid Role ID'))
      ->rule('branch_id', new Required('Branch ID is required'))
      ->rule('branch_id', new IsNumeric('Invalid Branch ID'))
      ->rule('status', new Required('Status is required'))
      ->rule('status', new InArray(['active', 'locked'], 'Status must be either active or locked'));
    if (!$validator->validate()) {
      $this->errors = $validator->errors();
      return false;
    }
    return true;
  }

  public function validateCreateSupplier(array $data): bool
  {
    $validator = new Validator($data);
    $validator->rule('email', new Required('Email is required'))
      ->rule('email', new Email())
      ->rule('supplier_name', new Required('Supplier Name is required'))
      ->rule('contact_person', new Required('Contact Person is required'))
      ->rule('phone', new Required('Phone is required'))
      ->rule('phone', new Matches('/^\+?[0-9]{10,15}$/', 'Phone number must be valid'))
      ->rule('branch_id', new Required('Branch ID is required'))
      ->rule('branch_id', new IsNumeric('Invalid Branch ID'))
      ->rule('address', new Required('Address is required'));
    if (!$validator->validate()) {
      $this->errors = $validator->errors();
      return false;
    }
    return true;
  }

  public function validateCheckout(array $data): bool
  {
    $validator = new Validator($data);
    $validator->rule('items', new Required('Items are required'))
      ->rule('items', new IsArray(1, null, 'Items must be an array'))
      ->rule('items.*.product_id', new Required('Product ID is required'))
      ->rule('items.*.product_id', new IsNumeric('Invalid Product ID'))
      ->rule('items.*.quantity', new Required('Quantity is required'))
      ->rule('items.*.quantity', new IsNumeric('Quantity must be numeric'))
      ->rule('items.*.quantity', new CompareWithValue('>', 0, 'numeric', 'Quantity must be greater than 0'))
      ->rule('items.*.price', new Required('Price is required'))
      ->rule('items.*.price', new IsNumeric('Price must be numeric'))
      ->rule('items.*.price', new CompareWithValue('>', 0, 'numeric', 'Price must be greater than 0'))
      ->rule('payment_method', new Required('Payment method is required'))
      ->rule('payment_method', new InArray(['cash', 'card'], 'Invalid payment method'));
    if (!$validator->validate()) {
      $this->errors = $validator->errors();
      return false;
    }
    return true;
  }

  public function validateCreateCustomer(array $data): bool
  {
    $validator = new Validator($data);
    $validator->rule('email', new Required('Email is required'))
      ->rule('email', new Email())
      ->rule('first_name', new Required('First Name is required'))
      ->rule('last_name', new Required('Last Name is required'))
      ->rule('phone', new Required('Phone is required'))
      ->rule('phone', new Matches('/^\+?[0-9]{10,15}$/', 'Phone number must be valid'))
      ->rule('address', new Required('Address is required'));
    if (!$validator->validate()) {
      $this->errors = $validator->errors();
      return false;
    }
    return true;
  }

  public function validateCreateProduct(array $data): bool
  {
    $validator = new Validator($data);
    $validator->rule('product_name', new Required('Product Name is required'))
      ->rule('product_code', new Required('Product Code is required'))
      ->rule('unit_id', new Required('Unit ID is required'))
      ->rule('unit_id', new IsNumeric('Invalid Unit ID'))
      ->rule('categories', new Required('Categories are required'))
      ->rule('categories', new IsArray(1, null, 'Categories must be an array'))
      ->rule('categories.*', new IsNumeric('Invalid Category ID'))
      ->rule('reorder_level', new Required('Reorder Level is required'))
      ->rule('reorder_level', new IsNumeric('Reorder Level must be numeric'))
      ->rule('reorder_quantity', new Required('Reorder Quantity is required'))
      ->rule('reorder_quantity', new IsNumeric('Reorder Quantity must be numeric'));
    if (!$validator->validate()) {
      $this->errors = $validator->errors();
      return false;
    }
    return true;
  }

  public function validateUpdateProduct(array $data): bool
  {
    $validator = new Validator($data);
    $validator->rule('product_name', new Required('Product Name is required'))
      ->rule('product_code', new Required('Product Code is required'))
      ->rule('unit_id', new Required('Unit ID is required'))
      ->rule('unit_id', new IsNumeric('Invalid Unit ID'))
      ->rule('categories', new Required('Categories are required'))
      ->rule('categories', new IsArray(1, null, 'Categories must be an array'))
      ->rule('categories.*', new IsNumeric('Invalid Category ID'))
      ->rule('reorder_level', new Required('Reorder Level is required'))
      ->rule('reorder_level', new IsNumeric('Reorder Level must be numeric'))
      ->rule('reorder_quantity', new Required('Reorder Quantity is required'))
      ->rule('reorder_quantity', new IsNumeric('Reorder Quantity must be numeric'));
    if (!$validator->validate()) {
      $this->errors = $validator->errors();
      return false;
    }
    return true;
  }

  /**
   * Get validation error
   *
   * @return string
   */
  public function getError(): string
  {
    return array_values($this->errors)[0][0] ?? '';
  }
}
