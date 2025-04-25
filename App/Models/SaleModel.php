<?php

namespace App\Models;

use App\Core\Model;

class SaleModel extends Model
{
  public function createSale(array $data): void
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

      foreach ($data['items'] as $item) {
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
            unit_price)
          VALUES (?, ?, ?, ?, ?)
        ';

        self::$db->query($sql, [
          $saleId,
          $item['product_id'],
          $item['batch_id'],
          $item['quantity'],
          $item['unit_price'],
        ]);
      }

      self::$db->commit();
    } catch (\Exception $e) {
      self::$db->rollBack();
      throw $e;
    }
  }
}
