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

  public function createSale(array $data): void
  {
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

    foreach ($data['items'] as $item) {
      $this->createSaleItem($saleId, $item);
    }

    self::$db->commit();
  }

  private function createSaleItem(int $saleId, array $item): void
  {
    $sql = '
      UPDATE product_batch
      SET
        current_quantity = current_quantity - ?
      WHERE
        id = ?
    ';

    self::$db->query($sql, [$item['quantity'], $item['batch_id']]);

    $sql = '
      INSERT INTO sale_item (
        sale_id,
        product_id,
        batch_id,
        quantity,
        unit_price,
        discount)
      VALUES (?, ?, ?, ?, ?, ?)
    ';

    self::$db->query($sql, [
      $saleId,
      $item['product_id'],
      $item['batch_id'],
      $item['quantity'],
      $item['unit_price'],
      $item['discount'],
    ]);
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
