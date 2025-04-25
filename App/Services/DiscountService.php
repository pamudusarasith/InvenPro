<?php

namespace App\Services;

use DateTime;

class DiscountService
{
  /**
   * Calculate and determine the optimal set of discounts for the given cart items
   * 
   * @param array $cartItems Array of cart items with product_id, batch_id, quantity, unit_price
   * @param array $discounts Array of available discounts
   * @param float|null $loyaltyPoints Customer's loyalty points (if available)
   * @return array Array of applicable discounts
   */
  public static function calculateOptimalDiscounts(
    array $cartItems, 
    array $discounts, 
    ?float $loyaltyPoints = null
  ): array
  {
    $eligibleDiscounts = [];
    $now = new DateTime();
    
    // First, filter discounts that are active and within date range
    foreach ($discounts as $discount) {
      // Skip inactive discounts
      if (!$discount['is_active']) {
        continue;
      }
      
      // Check date range
      $startDate = new DateTime($discount['start_date']);
      if ($startDate > $now) {
        continue; // Discount hasn't started yet
      }
      
      if (!empty($discount['end_date'])) {
        $endDate = new DateTime($discount['end_date']);
        if ($endDate < $now) {
          continue; // Discount has expired
        }
      }
      
      // Check if discount meets its conditions
      if (self::checkDiscountConditions($discount, $cartItems, $loyaltyPoints)) {
        $eligibleDiscounts[] = $discount;
      }
    }
    
    // Get the best combination of discounts
    return self::getBestDiscountCombination($eligibleDiscounts, $cartItems);
  }
  
  /**
   * Check if a discount's conditions are met
   *
   * @param array $discount The discount to check
   * @param array $cartItems Array of cart items
   * @param float|null $loyaltyPoints Customer's loyalty points (if available)
   * @return bool True if conditions are met, false otherwise
   */
  private static function checkDiscountConditions(array $discount, array $cartItems, ?float $loyaltyPoints = null): bool
  {
    // If no conditions, discount is always applicable
    if (empty($discount['conditions'])) {
      return true;
    }

    foreach ($discount['conditions'] as $condition) {
      $conditionType = $condition['condition_type'];
      $conditionValue = is_string($condition['condition_value'])
        ? json_decode($condition['condition_value'], true)
        : $condition['condition_value'];

      switch ($conditionType) {
        case 'min_amount':
          if (!self::checkMinAmountCondition($conditionValue, $cartItems)) {
            return false;
          }
          break;

        case 'min_quantity':
          if (!self::checkMinQuantityCondition($conditionValue, $cartItems)) {
            return false;
          }
          break;

        case 'time_of_day':
          if (!self::checkTimeOfDayCondition($conditionValue)) {
            return false;
          }
          break;

        case 'day_of_week':
          if (!self::checkDayOfWeekCondition($conditionValue)) {
            return false;
          }
          break;

        case 'loyalty_points':
          if (!self::checkLoyaltyPointsCondition($conditionValue, $loyaltyPoints)) {
            return false;
          }
          break;
      }
    }

    // All conditions passed
    return true;
  }

  /**
   * Check if minimum amount condition is met
   *
   * @param array $condition The condition to check
   * @param array $cartItems Array of cart items
   * @return bool True if condition is met, false otherwise
   */
  private static function checkMinAmountCondition(array $condition, array $cartItems): bool
  {
    $minAmount = $condition['min_amount'] ?? 0;
    $totalAmount = 0;

    foreach ($cartItems as $item) {
      $totalAmount += ($item['quantity'] * $item['unit_price']);
    }

    return $totalAmount >= $minAmount;
  }

  /**
   * Check if minimum quantity condition is met
   *
   * @param array $condition The condition to check
   * @param array $cartItems Array of cart items
   * @return bool True if condition is met, false otherwise
   */
  private static function checkMinQuantityCondition(array $condition, array $cartItems): bool
  {
    $requiredProductId = $condition['product_id'] ?? null;
    $minQuantity = $condition['min_quantity'] ?? 0;

    // Min quantity conditions always require a specific product
    if (!$requiredProductId) {
      return false;
    }

    // Check quantity for the specific product
    $productQuantity = 0;
    foreach ($cartItems as $item) {
      if ($item['product_id'] == $requiredProductId) {
        $productQuantity += $item['quantity'];
      }
    }

    return $productQuantity >= $minQuantity;
  }

  /**
   * Check if time of day condition is met
   *
   * @param array $condition The condition to check
   * @return bool True if condition is met, false otherwise
   */
  private static function checkTimeOfDayCondition(array $condition): bool
  {
    $now = new DateTime();
    $currentTime = $now->format('H:i:s');

    $startTime = $condition['start_time'] ?? '00:00:00';
    $endTime = $condition['end_time'] ?? '23:59:59';

    return $currentTime >= $startTime && $currentTime <= $endTime;
  }

  /**
   * Check if day of week condition is met
   *
   * @param array $condition The condition to check
   * @return bool True if condition is met, false otherwise
   */
  private static function checkDayOfWeekCondition(array $condition): bool
  {
    $now = new DateTime();
    $currentDayOfWeek = $now->format('N'); // 1 (Monday) to 7 (Sunday)

    $allowedDays = $condition['days'] ?? [];

    return empty($allowedDays) || in_array($currentDayOfWeek, $allowedDays);
  }

  /**
   * Check if loyalty points condition is met
   *
   * @param array $condition The condition to check
   * @param float|null $loyaltyPoints Customer's loyalty points
   * @return bool True if condition is met, false otherwise
   */
  private static function checkLoyaltyPointsCondition(array $condition, ?float $loyaltyPoints): bool
  {
    if ($loyaltyPoints === null) {
      return false; // No loyalty points provided
    }

    $minPoints = $condition['min_points'] ?? 0;

    return $loyaltyPoints >= $minPoints;
  }

  /**
   * Calculate the discount amount for a single discount
   *
   * @param array $discount The discount to apply
   * @param array $cartItems Array of cart items
   * @return float The discount amount
   */
  private static function calculateDiscountAmount(array $discount, array $cartItems): float
  {
    $totalAmount = 0;
    foreach ($cartItems as $item) {
      $totalAmount += ($item['quantity'] * $item['unit_price']);
    }

    if ($discount['discount_type'] === 'percentage') {
      return $totalAmount * ($discount['value'] / 100);
    } else { // fixed amount
      return min($discount['value'], $totalAmount); // Don't discount more than the total
    }
  }

  /**
   * Get the best combination of discounts to apply
   * 
   * @param array $eligibleDiscounts Array of eligible discounts
   * @param array $cartItems Array of cart items
   * @return array The best discounts to apply
   */
  private static function getBestDiscountCombination(
    array $eligibleDiscounts, 
    array $cartItems
  ): array
  {
    // If no eligible discounts, return empty array
    if (empty($eligibleDiscounts)) {
      return [];
    }
    
    // If only one discount, return it
    if (count($eligibleDiscounts) === 1) {
      $discount = $eligibleDiscounts[0];
      $discount['calculated_amount'] = self::calculateDiscountAmount($discount, $cartItems);
      return [$discount];
    }
    
    // Group discounts into combinable and non-combinable
    $combinableDiscounts = [];
    $nonCombinableDiscounts = [];
    
    foreach ($eligibleDiscounts as $discount) {
      if ($discount['is_combinable']) {
        $combinableDiscounts[] = $discount;
      } else {
        $nonCombinableDiscounts[] = $discount;
      }
    }
    
    // Calculate the best non-combinable discount (if any)
    $bestNonCombinable = null;
    $bestNonCombinableAmount = 0;
    
    foreach ($nonCombinableDiscounts as $discount) {
      $amount = self::calculateDiscountAmount($discount, $cartItems);
      if ($amount > $bestNonCombinableAmount) {
        $bestNonCombinableAmount = $amount;
        $bestNonCombinable = $discount;
      }
    }
    
    // Calculate total combinable discount amount
    $totalCombinableAmount = 0;
    $appliedCombinableDiscounts = [];
    
    foreach ($combinableDiscounts as $discount) {
      $amount = self::calculateDiscountAmount($discount, $cartItems);
      $totalCombinableAmount += $amount;
      
      $discount['calculated_amount'] = $amount;
      $appliedCombinableDiscounts[] = $discount;
    }
    
    // Determine the best strategy
    if ($totalCombinableAmount >= $bestNonCombinableAmount) {
      // Using all combinable discounts is better
      return $appliedCombinableDiscounts;
    } else {
      // Using the best non-combinable discount is better
      $bestNonCombinable['calculated_amount'] = $bestNonCombinableAmount;
      return [$bestNonCombinable];
    }
  }
}
