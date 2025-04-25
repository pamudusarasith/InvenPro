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
   * @param array|null $appliedCoupons Array of already applied coupon codes
   * @return array Array of applicable discounts
   */
  public static function calculateOptimalDiscounts(
    array $cartItems, 
    array $discounts, 
    ?float $loyaltyPoints = null,
    ?array $appliedCoupons = null
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
    
    // Get the best combination of discounts, considering already applied coupons
    return self::getBestDiscountCombination($eligibleDiscounts, $cartItems, $appliedCoupons);
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
   * Get the best combination of discounts to apply, considering any already applied coupons
   * 
   * @param array $eligibleDiscounts Array of eligible discounts
   * @param array $cartItems Array of cart items
   * @param array|null $appliedCoupons Array of already applied coupon codes
   * @return array The best discounts to apply
   */
  private static function getBestDiscountCombination(
    array $eligibleDiscounts, 
    array $cartItems,
    ?array $appliedCoupons = null
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
    
    // Separate discounts into regular and coupon-based
    $regularDiscounts = [];
    $couponDiscounts = [];
    
    foreach ($eligibleDiscounts as $discount) {
      if ($discount['application_method'] === 'regular') {
        $regularDiscounts[] = $discount;
      } else {
        $couponDiscounts[] = $discount;
      }
    }
    
    // Check for already applied coupons
    $previouslyAppliedDiscounts = [];
    if (!empty($appliedCoupons)) {
      foreach ($couponDiscounts as $discount) {
        if (isset($discount['coupons'])) {
          foreach ($discount['coupons'] as $coupon) {
            if (in_array($coupon['code'], $appliedCoupons)) {
              $previouslyAppliedDiscounts[] = $discount;
              break;
            }
          }
        }
      }
    }
    
    // Further group discounts into combinable and non-combinable
    $combinableRegular = [];
    $nonCombinableRegular = [];
    $combinableCoupons = [];
    $nonCombinableCoupons = [];
    
    foreach ($regularDiscounts as $discount) {
      if ($discount['is_combinable']) {
        $combinableRegular[] = $discount;
      } else {
        $nonCombinableRegular[] = $discount;
      }
    }
    
    foreach ($couponDiscounts as $discount) {
      if ($discount['is_combinable']) {
        $combinableCoupons[] = $discount;
      } else {
        $nonCombinableCoupons[] = $discount;
      }
    }
    
    // Calculated the best discount options
    
    // 1. Calculate the best non-combinable regular discount (if any)
    $bestNonCombinableRegular = null;
    $bestNonCombinableRegularAmount = 0;
    
    foreach ($nonCombinableRegular as $discount) {
      $amount = self::calculateDiscountAmount($discount, $cartItems);
      if ($amount > $bestNonCombinableRegularAmount) {
        $bestNonCombinableRegularAmount = $amount;
        $bestNonCombinableRegular = $discount;
      }
    }
    
    // 2. Calculate the best non-combinable coupon discount (if any)
    $bestNonCombinableCoupon = null;
    $bestNonCombinableCouponAmount = 0;
    
    foreach ($nonCombinableCoupons as $discount) {
      $amount = self::calculateDiscountAmount($discount, $cartItems);
      if ($amount > $bestNonCombinableCouponAmount) {
        $bestNonCombinableCouponAmount = $amount;
        $bestNonCombinableCoupon = $discount;
      }
    }
    
    // 3. Calculate total combinable regular discount amount
    $totalCombinableRegularAmount = 0;
    $appliedCombinableRegular = [];
    
    foreach ($combinableRegular as $discount) {
      $amount = self::calculateDiscountAmount($discount, $cartItems);
      $totalCombinableRegularAmount += $amount;
      
      $discount['calculated_amount'] = $amount;
      $appliedCombinableRegular[] = $discount;
    }
    
    // 4. Calculate total combinable coupon discount amount
    $totalCombinableCouponAmount = 0;
    $appliedCombinableCoupons = [];
    
    foreach ($combinableCoupons as $discount) {
      // Skip applying new combinable coupons if we already have any non-combinable ones
      if (!empty($previouslyAppliedDiscounts) && !empty(array_filter($previouslyAppliedDiscounts, function($d) {
        return !$d['is_combinable'];
      }))) {
        continue;
      }
      
      $amount = self::calculateDiscountAmount($discount, $cartItems);
      $totalCombinableCouponAmount += $amount;
      
      $discount['calculated_amount'] = $amount;
      $appliedCombinableCoupons[] = $discount;
    }
    
    // Now determine the best strategy
    $finalDiscounts = [];
    $maxDiscountAmount = 0;
    
    // Calculate total amounts for different combinations
    
    // Option 1: All combinable discounts (regular + coupons)
    $option1Total = $totalCombinableRegularAmount + $totalCombinableCouponAmount;
    
    // Option 2: Combinable regular + best non-combinable coupon
    $option2Total = $totalCombinableRegularAmount + $bestNonCombinableCouponAmount;
    
    // Option 3: Best non-combinable regular + combinable coupons
    $option3Total = $bestNonCombinableRegularAmount + $totalCombinableCouponAmount;
    
    // Option 4: Best non-combinable regular only
    $option4Total = $bestNonCombinableRegularAmount;
    
    // Option 5: Best non-combinable coupon only
    $option5Total = $bestNonCombinableCouponAmount;
    
    // Select the best option
    
    // Special case: If we have previously applied coupons, we must include them
    if (!empty($previouslyAppliedDiscounts)) {
      $previousTotal = 0;
      foreach ($previouslyAppliedDiscounts as $discount) {
        $amount = self::calculateDiscountAmount($discount, $cartItems);
        $discount['calculated_amount'] = $amount;
        $previousTotal += $amount;
        $finalDiscounts[] = $discount;
      }
      
      // If previous coupons are all combinable, we can add combinable regular discounts
      $allPreviousCombinabe = !in_array(false, array_map(function($d) {
        return $d['is_combinable'];
      }, $previouslyAppliedDiscounts));
      
      if ($allPreviousCombinabe) {
        $finalDiscounts = array_merge($finalDiscounts, $appliedCombinableRegular);
        $maxDiscountAmount = $previousTotal + $totalCombinableRegularAmount;
      } else {
        $maxDiscountAmount = $previousTotal;
      }
      
      return $finalDiscounts;
    }
    
    // Find the maximum discount option
    if ($option1Total >= $option2Total && $option1Total >= $option3Total && 
        $option1Total >= $option4Total && $option1Total >= $option5Total) {
      // Option 1 is best: All combinable discounts
      $finalDiscounts = array_merge($appliedCombinableRegular, $appliedCombinableCoupons);
      $maxDiscountAmount = $option1Total;
    } elseif ($option2Total >= $option1Total && $option2Total >= $option3Total && 
              $option2Total >= $option4Total && $option2Total >= $option5Total && 
              $bestNonCombinableCoupon) {
      // Option 2 is best: Combinable regular + best non-combinable coupon
      $finalDiscounts = $appliedCombinableRegular;
      $bestNonCombinableCoupon['calculated_amount'] = $bestNonCombinableCouponAmount;
      $finalDiscounts[] = $bestNonCombinableCoupon;
      $maxDiscountAmount = $option2Total;
    } elseif ($option3Total >= $option1Total && $option3Total >= $option2Total && 
              $option3Total >= $option4Total && $option3Total >= $option5Total && 
              $bestNonCombinableRegular) {
      // Option 3 is best: Best non-combinable regular + combinable coupons
      $finalDiscounts = $appliedCombinableCoupons;
      $bestNonCombinableRegular['calculated_amount'] = $bestNonCombinableRegularAmount;
      $finalDiscounts[] = $bestNonCombinableRegular;
      $maxDiscountAmount = $option3Total;
    } elseif ($option4Total >= $option1Total && $option4Total >= $option2Total && 
              $option4Total >= $option3Total && $option4Total >= $option5Total &&
              $bestNonCombinableRegular) {
      // Option 4 is best: Best non-combinable regular only
      $bestNonCombinableRegular['calculated_amount'] = $bestNonCombinableRegularAmount;
      $finalDiscounts[] = $bestNonCombinableRegular;
      $maxDiscountAmount = $option4Total;
    } elseif ($bestNonCombinableCoupon) {
      // Option 5 is best: Best non-combinable coupon only
      $bestNonCombinableCoupon['calculated_amount'] = $bestNonCombinableCouponAmount;
      $finalDiscounts[] = $bestNonCombinableCoupon;
      $maxDiscountAmount = $option5Total;
    }
    
    return $finalDiscounts;
  }
}
