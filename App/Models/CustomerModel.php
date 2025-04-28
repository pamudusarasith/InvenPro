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

  public function getCustomerByPhone(string $phone): array|bool
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

  public function getLoyaltyPoints(int $customerId): array | bool
  {
    $sql = '
    SELECT
      points
    FROM customer
    WHERE
      id = ?
      AND deleted_at IS NULL
    ';
    $stmt = self::$db->query($sql, [$customerId]);
    return $stmt->fetchColumn();
  }

  public function getActiveCustomersCount(): int
  {
    $sql = 'SELECT COUNT(*) FROM customer WHERE deleted_at IS NULL';
    $stmt = self::$db->query($sql);
    return (int) $stmt->fetchColumn();
  }
}
