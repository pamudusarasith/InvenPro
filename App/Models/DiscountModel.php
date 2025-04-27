<?php

namespace App\Models;

use App\Core\Model;

class DiscountModel extends Model
{
  public function discountExists(int $discountId): bool
  {
    $sql = '
        SELECT COUNT(*) as count
        FROM discount
        WHERE id = ? AND branch_id = ?
      ';
    $stmt = self::$db->query($sql, [$discountId, $_SESSION['user']['branch_id']]);
    $result = $stmt->fetch();
    return (bool)$result['count'];
  }

  public function getDiscounts($page, $itemsPerPage, $query, $status, $from, $to, $type): array
  {
    try {
      self::$db->beginTransaction();
      $params = [$_SESSION['user']['branch_id']];

      $sql = '
        SELECT
          id,
          name,
          description,
          discount_type,
          value,
          start_date,
          end_date,
          is_active,
          is_combinable
        FROM discount
        WHERE branch_id = ?
          AND deleted_at IS NULL
      ';

      // Add filters
      if (!empty($query)) {
        $sql .= ' AND (name LIKE ? OR description LIKE ?)';
        $params[] = "%$query%";
        $params[] = "%$query%";
      }

      if ($status !== '') {
        $sql .= ' AND is_active = ?';
        $params[] = $status;
      }

      if (!empty($from)) {
        $sql .= ' AND start_date <= ?';
        $params[] = $from;
      }

      if (!empty($to)) {
        $sql .= ' AND (end_date >= ? OR end_date IS NULL)';
        $params[] = $to;
      }

      if (!empty($type)) {
        $sql .= ' AND discount_type = ?';
        $params[] = $type;
      }

      // Add pagination
      $sql .= ' ORDER BY id DESC';

      if ($page !== null && $itemsPerPage !== null) {
        $offset = ($page - 1) * $itemsPerPage;
        $sql .= ' LIMIT ? OFFSET ?';
        $params[] = $itemsPerPage;
        $params[] = $offset;
      }

      $stmt = self::$db->query($sql, $params);
      $discounts = $stmt->fetchAll();

      $sql = '
        SELECT
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

      self::$db->commit();
      return $discounts ?: [];
    } catch (\Exception $e) {
      self::$db->rollBack();
      throw $e;
    }
  }

  public function getDiscountsCount($query, $status, $from, $to, $type): int
  {
    $params = [$_SESSION['user']['branch_id']];

    $sql = '
        SELECT COUNT(*) as count
        FROM discount
        WHERE branch_id = ?
          AND deleted_at IS NULL
      ';

    // Add filters
    if (!empty($query)) {
      $sql .= ' AND (name LIKE ? OR description LIKE ?)';
      $params[] = "%$query%";
      $params[] = "%$query%";
    }

    if ($status !== '') {
      $sql .= ' AND is_active = ?';
      $params[] = $status;
    }

    if (!empty($from)) {
      $sql .= ' AND start_date >= ?';
      $params[] = $from;
    }

    if (!empty($to)) {
      $sql .= ' AND end_date <= ?';
      $params[] = $to;
    }

    if (!empty($type)) {
      $sql .= ' AND discount_type = ?';
      $params[] = $type;
    }

    $stmt = self::$db->query($sql, $params);
    $result = $stmt->fetch();

    return (int)$result['count'];
  }

  public function createDiscount(array $data): void
  {
    try {
      self::$db->beginTransaction();

      $sql = '
        INSERT INTO discount (branch_id, name, description, discount_type, value, start_date, end_date, is_active, is_combinable)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
      ';

      $stmt = self::$db->query($sql, [
        $_SESSION['user']['branch_id'],
        $data['name'],
        $data['description'],
        $data['discount_type'],
        $data['value'],
        $data['start_date'],
        $data['end_date'],
        1,
        $data['is_combinable']
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

      self::$db->commit();
    } catch (\Exception $e) {
      self::$db->rollBack();
      throw $e;
    }
  }

  public function updateDiscount(int $discountId, array $data): void
  {
    try {
      self::$db->beginTransaction();

      $sql = '
        UPDATE discount
        SET name = ?, description = ?, discount_type = ?, value = ?, start_date = ?, end_date = ?, is_combinable = ?
        WHERE id = ? AND branch_id = ?
      ';

      self::$db->query($sql, [
        $data['name'],
        $data['description'],
        $data['discount_type'],
        $data['value'],
        $data['start_date'],
        $data['end_date'],
        $data['is_combinable'],
        $discountId,
        $_SESSION['user']['branch_id']
      ]);

      // Update conditions
      $sql = '
        DELETE FROM discount_condition
        WHERE discount_id = ?
      ';
      self::$db->query($sql, [$discountId]);

      foreach ($data['conditions'] as $condition) {
        $sql = '
          INSERT INTO discount_condition (discount_id, condition_type, condition_value)
          VALUES (?, ?, ?)
        ';
        self::$db->query($sql, [
          $discountId,
          $condition['condition_type'],
          $condition['condition_value']
        ]);
      }

      self::$db->commit();
    } catch (\Exception $e) {
      self::$db->rollBack();
      throw $e;
    }
  }

  public function deleteDiscount(int $discountId): void
  {
    $sql = '
        UPDATE discount
        SET deleted_at = NOW()
        WHERE id = ? AND branch_id = ?
      ';
    self::$db->query($sql, [$discountId, $_SESSION['user']['branch_id']]);
  }

  public function changeStatus(int $discountId, int $status): void
  {
    $sql = '
        UPDATE discount
        SET is_active = ?
        WHERE id = ? AND branch_id = ?
      ';
    self::$db->query($sql, [$status, $discountId, $_SESSION['user']['branch_id']]);
  }
}
