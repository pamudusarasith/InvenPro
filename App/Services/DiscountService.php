<?php

namespace App\Services;

use App\Core\DB;
use App\Models\DiscountModel;
use DateTime;

class DiscountService
{
  /**
   * Get all eligible discounts for a customer's cart
   * 
   * @param int|null $customerId The ID of the customer, null for guest
   * @param int $branchId The ID of the branch
   * @param array $cartItems Array of cart items [product_id, quantity, price, batch_id]
   * @param string|null $couponCode Optional coupon code
   * @param int $loyaltyPoints Customer's loyalty points (0 for guests)
   * @return array Eligible discounts with calculated amounts
   */
  public static function getEligibleDiscounts(
    array $cartItems,
    array $discounts,
    ?int $loyaltyPoints = null

  ): array {
    $eligibleDiscounts = [];
    $cartTotal = self::calculateCartTotal($cartItems);

    foreach ($discounts as $discount) {
      // Skip if discount is not active
      if (!$discount['is_active']) {
        continue;
      }

      // Check all conditions for the discount
      $conditions = $discount['conditions'] ?? [];
      $isEligible = self::checkDiscountEligibility(
        $conditions,
        $cartTotal,
        $cartItems,
        $loyaltyPoints
      );

      if ($isEligible) {
        // Calculate the discount amount
        $discountAmount = self::calculateDiscountAmount($discount, $cartTotal);

        $eligibleDiscounts[] = [
          'id' => $discount['id'],
          'name' => $discount['name'],
          'description' => $discount['description'],
          'discount_type' => $discount['discount_type'],
          'value' => $discount['value'],
          'calculated_amount' => $discountAmount,
          'application_method' => $discount['application_method'],
          'coupons' => $discount['coupons'] ?? [],
        ];
      }
    }

    return $eligibleDiscounts;
  }

  /**
   * Check if discount conditions are met
   *
   * @param array $conditions Discount conditions
   * @param float $cartTotal Cart total
   * @param float $totalQuantity Total quantity
   * @param array $cartItems Cart items
   * @param int $loyaltyPoints Customer loyalty points
   * @return bool Whether the discount is eligible
   */
  private static function checkDiscountEligibility(
    array $conditions,
    float $cartTotal,
    array $cartItems,
    ?int $loyaltyPoints
  ): bool {
    if (empty($conditions)) {
      // No conditions means always eligible
      return true;
    }

    foreach ($conditions as $condition) {
      $conditionType = $condition['condition_type'];
      $conditionValue = $condition['condition_value'];

      switch ($conditionType) {
        case 'min_amount':
          if ($cartTotal < $conditionValue['amount']) {
            return false;
          }
          break;

        // case 'min_quantity':
        //   if ($totalQuantity < $conditionValue['quantity']) {
        //     return false;
        //   }
        //   break;

        case 'time_of_day':
          $currentHour = (new DateTime())->format('H');
          if (
            $currentHour < $conditionValue['start_hour'] ||
            $currentHour > $conditionValue['end_hour']
          ) {
            return false;
          }
          break;

        case 'day_of_week':
          $currentDayOfWeek = (new DateTime())->format('N'); // 1 (Monday) to 7 (Sunday)
          if (!in_array($currentDayOfWeek, $conditionValue['days'])) {
            return false;
          }
          break;

        case 'loyalty_points':
          if (is_null($loyaltyPoints) || $loyaltyPoints < $conditionValue['points']) {
            return false;
          }
          break;

        default:
          break;
      }
    }

    return true;
  }

  /**
   * Calculate the discount amount
   *
   * @param array $discount Discount data
   * @param float $cartTotal Cart total
   * @return float Calculated discount amount
   */
  private static function calculateDiscountAmount(array $discount, float $cartTotal): float
  {
    if ($discount['discount_type'] === 'percentage') {
      return round($cartTotal * ($discount['value'] / 100), 2);
    } else { // fixed discount
      return min($discount['value'], $cartTotal); // Don't exceed cart total
    }
  }

  /**
   * Calculate the total amount of items in the cart
   *
   * @param array $cartItems Cart items
   * @return float Total amount
   */
  private static function calculateCartTotal(array $cartItems): float
  {
    $total = 0;
    foreach ($cartItems as $item) {
      $total += $item['price'] * $item['quantity'];
    }
    return $total;
  }
}
