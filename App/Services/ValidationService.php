<?php

namespace App\Services;

use App\Services\Validation\Rules\{
  CompareWithField,
  CompareWithValue,
  Email,
  InArray,
  IsArray,
  IsBoolean,
  IsDateTime,
  IsNumeric,
  IsString,
  Matches,
  NotRule,
  OrRule,
  Required
};
use App\Services\Validation\Validator;
use DateTime;

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



  public function validateUpdatePassword(array $data): bool
  {
    $validator = new Validator($data);
    $validator->rule('old_password', new Required('Password is required'))
      ->rule('new_password', new Required('Password is required'))
      ->rule('new_password', new IsString(8, 50, 'Password must be between 8 and 50 characters'))
      ->rule('confirm_password', new Required('Confirm Password is required'))
      ->rule('confirm_password', new IsString(8, 50, 'Confirm Password must be between 8 and 50 characters'))
      ->rule('confirm_password', new CompareWithField('==', 'password', 'string', 'Passwords do not match'));

    if (!$validator->validate()) {
      $this->errors = $validator->errors();
      return false;
    }
    return true;
  }

  public function validateUpdateProfile(array $data): bool
  {
    $validator = new Validator($data);
    $validator->rule('first_name', new Required('First Name is required'))
      ->rule('first_name', new IsString(0, 50, 'First Name must be a string between 0 and 50 characters'))
      ->rule('last_name', new Required('Last Name is required'))
      ->rule('last_name', new IsString(0, 50, 'Last Name must be a string between 0 and 50 characters'))
      ->rule('email', new Required('Email is required'))
      ->rule('email', new Email());

    if (!$validator->validate()) {
      $this->errors = $validator->errors();
      return false;
    }
    return true;
  }

  public function validateCreateUser(array $data): bool
  {
    $validator = new Validator($data);
    $validator->rule('first_name', new Required('First Name is required'))
      ->rule('first_name', new IsString(0, 50, 'First Name must be a string between 0 and 50 characters'))
      ->rule('last_name', new Required('Last Name is required'))
      ->rule('last_name', new IsString(0, 50, 'Last Name must be a string between 0 and 50 characters'))
      ->rule('email', new Required('Email is required'))
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
    $validator->rule('first_name', new Required('First Name is required'))
      ->rule('first_name', new IsString(0, 50, 'First Name must be a string between 0 and 50 characters'))
      ->rule('last_name', new Required('Last Name is required'))
      ->rule('last_name', new IsString(0, 50, 'Last Name must be a string between 0 and 50 characters'))->rule('email', new Required('Email is required'))
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



  public function validateRole(array $data): bool
  {
    $validator = new Validator($data);
    $validator->rule('role_name', new Required('Role Name is required'))
      ->rule('role_name', new IsString(0, 50, 'Role Name must be a string between 0 and 50 characters'))
      ->rule('description', new IsString(0, 255, 'Description must be a string between 0 and 255 characters'));
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

  public function validateCreateBranch(array $data): bool
  {
    $validator = new Validator($data);
    $validator->rule('branch_code', new Required('Branch Code is required'))
      ->rule('branch_code', new IsString(0, 50, 'Branch Code must be a string between 0 and 50 characters'))
      ->rule('branch_name', new Required('Branch Name is required'))
      ->rule('branch_name', new IsString(0, 50, 'Branch Name must be a string between 0 and 50 characters'))
      ->rule('address', new Required('Address is required'))
      ->rule('phone', new Required('Phone is required'))
      ->rule('phone', new Matches('/^\+?[0-9]{10,15}$/', 'Phone number must be valid'))
      ->rule('email', new Required('Email is required'))
      ->rule('email', new Email());
    if (!$validator->validate()) {
      $this->errors = $validator->errors();
      return false;
    }
    return true;
  }

  public function validateUpdateBranch(array $data): bool
  {
    $validator = new Validator($data);
    $validator->rule('branch_code', new Required('Branch Code is required'))
      ->rule('branch_code', new IsString(0, 50, 'Branch Code must be a string between 0 and 50 characters'))
      ->rule('branch_name', new Required('Branch Name is required'))
      ->rule('branch_name', new IsString(0, 50, 'Branch Name must be a string between 0 and 50 characters'))
      ->rule('address', new Required('Address is required'))
      ->rule('phone', new Required('Phone is required'))
      ->rule('phone', new Matches('/^\+?[0-9]{10,15}$/', 'Phone number must be valid'))
      ->rule('email', new Required('Email is required'))
      ->rule('email', new Email());
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
      ->rule('items.*.unit_price', new Required('Price is required'))
      ->rule('items.*.unit_price', new IsNumeric('Price must be numeric'))
      ->rule('items.*.unit_price', new CompareWithValue('>', 0, 'numeric', 'Price must be greater than 0'))
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
    // if ($data['reorder_level'] < 0) {
    //   $this->errors = [['Reo']];
    //   return false;
    // }

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
      ->rule('reorder_level', new CompareWithValue('>=', 0, 'numeric', 'Reorder Level must be greater than or equal to 0'))
      ->rule('reorder_quantity', new Required('Reorder Quantity is required'))
      ->rule('reorder_quantity', new IsNumeric('Reorder Quantity must be numeric'))
      ->rule('reorder_quantity', new CompareWithValue('>', 0, 'numeric', 'Reorder Quantity must be greater than 0'));
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
      ->rule('reorder_level', new CompareWithValue('>', 0, 'numeric', 'Reorder Level must be greater than 0'))
      ->rule('reorder_quantity', new Required('Reorder Quantity is required'))
      ->rule('reorder_quantity', new IsNumeric('Reorder Quantity must be numeric'))
      ->rule('reorder_quantity', new CompareWithValue('>', 0, 'numeric', 'Reorder Quantity must be greater than 0'));
    if (!$validator->validate()) {
      $this->errors = $validator->errors();
      return false;
    }
    return true;
  }

  public function validateCreateCategory(array $data): bool
  {
    $validator = new Validator($data);
    $validator->rule('category_name', new Required('Category Name is required'))
      ->rule('description', new IsString(0, 255, 'Description must be between 0 and 255 characters'));
    if (!$validator->validate()) {
      $this->errors = $validator->errors();
      return false;
    }
    return true;
  }

  public function validateUpdateCategory(array $data): bool
  {
    $validator = new Validator($data);
    $validator
      ->rule('category_name', new Required('Category Name is required'))
      ->rule('description', new IsString(0, 255, 'Description must be between 0 and 255 characters'));
    if (!$validator->validate()) {
      $this->errors = $validator->errors();
      return false;
    }
    return true;
  }



  public function validateCreateOrder(array $data): bool
  {
    $validator = new Validator($data);
    $validator->rule('reference', new Required('Reference is required'))
      ->rule('supplier_id', new Required('Supplier ID is required'))
      ->rule('supplier_id', new IsNumeric('Invalid Supplier ID'))
      ->rule('expected_date', new IsDateTime('Y-m-d', 'Invalid Expected Date'))
      ->rule('expected_date', new CompareWithValue('>=', date('Y-m-d'), 'date', 'Expected Date must be today or in the future'))
      ->rule('items', new Required('Items are required'))
      ->rule('items', new IsArray(1, null, 'Items must be an array'))
      ->rule('items.*.id', new Required('Product ID is required'))
      ->rule('items.*.id', new IsNumeric('Invalid Product ID'))
      ->rule('items.*.quantity', new Required('Quantity is required'))
      ->rule('items.*.quantity', new IsNumeric('Quantity must be numeric'))
      ->rule('items.*.quantity', new CompareWithValue('>', 0, 'numeric', 'Quantity must be greater than 0'))
      ->rule('notes', new IsString(0, 255, 'Notes must be a string between 0 and 255 characters'));
    if (!$validator->validate()) {
      $this->errors = $validator->errors();
      return false;
    }
    return true;
  }

  public function validateUpdateOrder(array $data): bool
  {
    $validator = new Validator($data);
    $validator->rule('reference', new Required('Reference is required'))
      ->rule('expected_date', new IsDateTime('Y-m-d', 'Invalid Expected Date'))
      ->rule('expected_date', new CompareWithField('>=', 'order_date', 'date', 'Expected Date must be later than Order Date'))
      ->rule('items', new Required('Items are required'))
      ->rule('items', new IsArray(1, null, 'Items must be an array'))
      ->rule('items.*.id', new Required('Product ID is required'))
      ->rule('items.*.id', new IsNumeric('Invalid Product ID'))
      ->rule('items.*.quantity', new Required('Quantity is required'))
      ->rule('items.*.quantity', new IsNumeric('Quantity must be numeric'))
      ->rule('items.*.quantity', new CompareWithValue('>', 0, 'numeric', 'Quantity must be greater than 0'))
      ->rule('notes', new IsString(0, 255, 'Notes must be a string between 0 and 255 characters'));
    if (!$validator->validate()) {
      $this->errors = $validator->errors();
      return false;
    }
    return true;
  }

  public function validateReceiveOrder(array $data): bool
  {
    $validator = new Validator($data);
    $validator->rule('batches', new Required('Batches are required'))
      ->rule('batches', new IsArray(0, null, 'Batches must be an array'));

    if (!$validator->validate()) {
      $this->errors = $validator->errors();
      return false;
    }

    $batchValidator = new Validator();
    $batchValidator->rule('product_id', new Required('Product ID is required'))
      ->rule('product_id', new IsNumeric('Invalid Product ID'))
      ->rule('batch_code', new Required('Batch Code is required'))
      ->rule('batch_code', new IsString(0, 50, 'Batch Code must be a string between 0 and 50 characters'))
      ->rule('received_qty', new Required('Quantity is required'))
      ->rule('received_qty', new IsNumeric('Quantity must be numeric'))
      ->rule('received_qty', new CompareWithValue('>', 0, 'numeric', 'Quantity must be greater than 0'))
      ->rule('unit_cost', new Required('Unit Cost is required'))
      ->rule('unit_cost', new IsNumeric('Unit Cost must be numeric'))
      ->rule('unit_cost', new CompareWithValue('>', 0, 'numeric', 'Unit Cost must be greater than 0'))
      ->rule('unit_price', new Required('Unit Price is required'))
      ->rule('unit_price', new IsNumeric('Unit Price must be numeric'))
      ->rule('unit_price', new CompareWithValue('>', 0, 'numeric', 'Unit Price must be greater than 0'))
      ->rule('manufactured_date', new IsDateTime('Y-m-d', 'Invalid Manufactured Date'))
      ->rule('expiry_date', new IsDateTime('Y-m-d', 'Invalid Expiry Date'))
      ->rule('expiry_date', new CompareWithField('>', 'manufactured_date', 'date', 'Expiry Date must be later than Manufactured Date'));

    foreach ($data['batches'] as $batch) {
      $batchValidator->setData($batch);
      if (!$batchValidator->validate()) {
        $this->errors = $batchValidator->errors();
        return false;
      }
    }
    return true;
  }

  public function validateCreateOrUpdateDiscount(array $data): bool
  {
    $validator = new Validator($data);
    $validator->rule('name', new Required('Name is required'))
      ->rule('name', new IsString(0, 100, 'Name must be a string between 0 and 100 characters'))
      ->rule('description', new IsString(0, 255, 'Description must be a string between 0 and 255 characters'))
      ->rule('discount_type', new Required('Discount Type is required'))
      ->rule('discount_type', new InArray(['percentage', 'fixed'], 'Invalid Discount Type'))
      ->rule('value', new Required('Value is required'))
      ->rule('value', new IsNumeric('Value must be numeric'))
      ->rule('value', new CompareWithValue('>', 0, 'numeric', 'Value must be greater than 0'))
      ->rule('start_date', new Required('Start Date is required'))
      ->rule('start_date', new IsDateTime('Y-m-d', 'Invalid Start Date'))
      ->rule('end_date', new IsDateTime('Y-m-d', 'Invalid End Date'))
      ->rule('is_combinable', new Required('Is Combinable is required'))
      ->rule('is_combinable', new IsBoolean(false, 'Is Combinable must be a boolean'))
      ->rule('conditions', new Required('Conditions are required'))
      ->rule('conditions', new IsArray(0, null, 'Conditions must be an array'))
      ->rule('conditions.*.condition_type', new Required('Condition Type is required'))
      ->rule('conditions.*.condition_type', new InArray(['min_quantity', 'min_amount', 'time_of_day', 'day_of_week', 'loyalty_points'], 'Invalid Condition Type'));

    if (!$validator->validate()) {
      $this->errors = $validator->errors();
      return false;
    }

    $minQuantityValidator = new Validator();
    $minQuantityValidator->rule('product_id', new Required('Product ID is required'))
      ->rule('product_id', new IsNumeric('Invalid Product ID'))
      ->rule('min_quantity', new Required('Minimum Quantity is required'))
      ->rule('min_quantity', new IsNumeric('Minimum Quantity must be numeric'))
      ->rule('min_quantity', new CompareWithValue('>', 0, 'numeric', 'Minimum Quantity must be greater than 0'));

    $minAmountValidator = new Validator();
    $minAmountValidator->rule('min_amount', new Required('Minimum Amount is required'))
      ->rule('min_amount', new IsNumeric('Minimum Amount must be numeric'))
      ->rule('min_amount', new CompareWithValue('>', 0, 'numeric', 'Minimum Amount must be greater than 0'));

    $timeOfDayValidator = new Validator();
    $timeOfDayValidator->rule('start_time', new Required('Start Time is required'))
      ->rule('start_time', new IsDateTime('H:i', 'Invalid Start Time'))
      ->rule('end_time', new Required('End Time is required'))
      ->rule('end_time', new IsDateTime('H:i', 'Invalid End Time'))
      ->rule('end_time', new CompareWithField('>', 'start_time', 'date', 'End Time must be later than Start Time', 'H:i'));

    $dayOfWeekValidator = new Validator();
    $dayOfWeekValidator->rule('days', new Required('Days are required'))
      ->rule('days', new IsArray(1, null, 'Days must be an array'))
      ->rule('days.*', new IsNumeric('Invalid Day of Week'))
      ->rule('days.*', new CompareWithValue('>=', 1, 'numeric', 'Day of Week must be between 1 and 7'))
      ->rule('days.*', new CompareWithValue('<=', 7, 'numeric', 'Day of Week must be between 1 and 7'));

    $loyaltyPointsValidator = new Validator();
    $loyaltyPointsValidator->rule('min_points', new Required('Minimum Points is required'))
      ->rule('min_points', new IsNumeric('Minimum Points must be numeric'))
      ->rule('min_points', new CompareWithValue('>', 0, 'numeric', 'Minimum Points must be greater than 0'));

    foreach ($data['conditions'] as $condition) {
      switch ($condition['condition_type']) {
        case 'min_quantity':
          $minQuantityValidator->setData($condition['condition_value']);
          if (!$minQuantityValidator->validate()) {
            $this->errors = $minQuantityValidator->errors();
            return false;
          }
          break;
        case 'min_amount':
          $minAmountValidator->setData($condition['condition_value']);
          if (!$minAmountValidator->validate()) {
            $this->errors = $minAmountValidator->errors();
            return false;
          }
          break;
        case 'time_of_day':
          $timeOfDayValidator->setData($condition['condition_value']);
          if (!$timeOfDayValidator->validate()) {
            $this->errors = $timeOfDayValidator->errors();
            return false;
          }
          break;
        case 'day_of_week':
          $dayOfWeekValidator->setData($condition['condition_value']);
          if (!$dayOfWeekValidator->validate()) {
            $this->errors = $dayOfWeekValidator->errors();
            return false;
          }
          break;
        case 'loyalty_points':
          $loyaltyPointsValidator->setData($condition['condition_value']);
          if (!$loyaltyPointsValidator->validate()) {
            $this->errors = $loyaltyPointsValidator->errors();
            return false;
          }
          break;
      }
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
