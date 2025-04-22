<?php

namespace App\Models;

use App\Core\Model;

class DiscountModel extends Model
{
  public function getDiscounts(): array
  {
    try {
      self::$db->beginTransaction();
      $sql = '
        SELECT
          id,
          name,
          description,
          discount_type,
          application_method,
          value,
          start_date,
          end_date,
          is_active
        FROM discount
        WHERE branch_id = ?
      ';

      $stmt = self::$db->query($sql, [$_SESSION['user']['branch_id']]);
      $discounts = $stmt->fetchAll();

      $sql = '
        SELECT
          discount_id,
          condition_type,
          condition_value
        FROM discount_condition
        WHERE discount_id = ?
      ';
      foreach ($discounts as &$discount) {
        $stmt = self::$db->query($sql, [$discount['id']]);
        $conditions = $stmt->fetchAll();
        foreach ($conditions as &$condition) {
          $condition['condition_value'] = json_decode($condition['condition_value'], true);
        }
        $discount['conditions'] = $conditions;
      }

      $sql = '
        SELECT
          discount_id,
          code,
          is_active
        FROM coupon
        WHERE discount_id = ?
      ';
      foreach ($discounts as &$discount) {
        if (!$discount['application_method'] == 'coupon') {
          continue;
        }
        $stmt = self::$db->query($sql, [$discount['id']]);
        $coupons = $stmt->fetchAll();
        foreach ($coupons as &$coupon) {
          $coupon['is_active'] = (bool)$coupon['is_active'];
        }
        $discount['coupons'] = $coupons;
      }

      self::$db->commit();
      return $discounts ?: [];
    } catch (\Exception $e) {
      self::$db->rollBack();
      throw $e;
    }
  }

  public function createDiscount(array $data): void
  {
    try {
      self::$db->beginTransaction();

      $sql = '
        INSERT INTO discount (branch_id, name, description, discount_type, application_method, value, start_date, end_date, is_active)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
      ';

      $stmt = self::$db->query($sql, [
        $_SESSION['user']['branch_id'],
        $data['name'],
        $data['description'],
        $data['discount_type'],
        $data['application_method'],
        $data['value'],
        $data['start_date'],
        $data['end_date'],
        1
      ]);

      $discountId = self::$db->lastInsertId();

      $sql = '
        INSERT INTO discount_condition (discount_id, condition_type, condition_value)
        VALUES (?, ?, ?)
      ';

      foreach ($data['conditions'] as $condition) {
        $stmt = self::$db->query($sql, [
          $discountId,
          $condition['condition_type'],
          $condition['condition_value']
        ]);
      }

      if ($data['application_method'] == 'coupon') {
        $sql = '
          INSERT INTO coupon (discount_id, code, is_active)
          VALUES (?, ?, ?)
        ';

        foreach ($data['coupons'] as $coupon) {
          $stmt = self::$db->query($sql, [
            $discountId,
            $coupon['code'],
            $coupon['is_active']
          ]);
        }
      }

      self::$db->commit();
    } catch (\Exception $e) {
      self::$db->rollBack();
      throw $e;
    }
  }
}
