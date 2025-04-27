<?php

namespace App\Models;

use App\Core\Model;

class SaleModel extends Model
{
  /**
   * Get sales on today
   * @return array
   */
  public function getSalesToday(): array
  {
    $branchCondition = ($_SESSION['user']['branch_id'] == 1) ? '' : 'AND branch_id = ?';
    $sql = "
      SELECT
        SUM(CASE WHEN DATE(sale_date) = CURDATE() THEN total ELSE 0 END) AS today_sales,
        SUM(CASE WHEN DATE(sale_date) = CURDATE() - INTERVAL 1 DAY THEN total ELSE 0 END) AS yesterday_sales
      FROM sale
      WHERE deleted_at IS NULL $branchCondition
    ";

    $params = ($_SESSION['user']['branch_id'] == 1) ? [] : [$_SESSION['user']['branch_id']];
    return self::$db->query($sql, $params)->fetch();
  }

  public function createSale(array $data): int|false
  {
    try {
      self::$db->beginTransaction();
      $sql = '
      INSERT INTO sale (
        branch_id,
        customer_id,
        user_id,
        subtotal,
        discount,
        total,
        payment_method,
        notes)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ';

      self::$db->query($sql, [
        $data['branch_id'],
        $data['customer_id'],
        $data['user_id'],
        $data['subtotal'],
        $data['discount'],
        $data['total'],
        $data['payment_method'],
        $data['notes'],
      ]);

      $saleId = self::$db->lastInsertId();
      if (!$saleId) {
        throw new \Exception('Failed to create sale');
      }

      foreach ($data['items'] as $item) {
        $sql = '
          UPDATE product_batch
          SET
            current_quantity = current_quantity - ?
          WHERE
            id = ?
        ';

        $stmt = self::$db->query($sql, [$item['quantity'], $item['batch_id']]);
        if ($stmt->rowCount() === 0) {
          throw new \Exception('Failed to update product batch');
        }

        $sql = '
          INSERT INTO sale_item (
            sale_id,
            product_id,
            batch_id,
            quantity,
            unit_price)
          VALUES (?, ?, ?, ?, ?)
        ';

        $stmt = self::$db->query($sql, [
          $saleId,
          $item['product_id'],
          $item['batch_id'],
          $item['quantity'],
          $item['unit_price'],
        ]);
        if ($stmt->rowCount() === 0) {
          throw new \Exception('Failed to create sale item');
        }
      }

      self::$db->commit();
      return $saleId;
    } catch (\Exception $e) {
      self::$db->rollBack();
      error_log($e->getMessage() . "\n" . $e->getTraceAsString());
      return false;
    }
  }

  /**
   * Get total revenue for the past month from today
   * @return int
   */
  public function getMonthlyRevenue(): int
  {
    $branchCondition = ($_SESSION['user']['branch_id'] == 1) ? '' : 'AND branch_id = ?';
    $sql = "
      SELECT SUM(total) AS total_revenue
      FROM sale
      WHERE DATE(sale_date) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
        AND deleted_at IS NULL $branchCondition AND created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
    ";

    $params = ($_SESSION['user']['branch_id'] == 1) ? [] : [$_SESSION['user']['branch_id']];
    return self::$db->query($sql, $params)->fetchColumn() ?: 0;
  }
}
