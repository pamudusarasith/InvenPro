<?php

namespace App\Models;

use App\Core\Model;

class CustomerModel extends Model
{
  public function createCustomer(array $data): void
  {
    $sql = 'INSERT INTO customer (first_name, last_name, email, phone, address)
            VALUES (:first_name, :last_name, :email, :phone, :address)';
    self::$db->query($sql, $data);
  }

  public function getCustomerByPhone(string $phone): array | bool
  {
    $sql = '
    SELECT
      id,
      CONCAT(first_name, " ", last_name) as name
    FROM customer
    WHERE
      deleted_at IS NULL
      AND phone = :phone
    ';
    $stmt = self::$db->query($sql, ['phone' => $phone]);
    return $stmt->fetch();
  }
}
