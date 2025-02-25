<?php

namespace App\Models;

use App\Core\Model;

class CategoryModel extends Model
{
  public function getPrimaryCategories(): array
  {
    $sql = '
      SELECT
        id,
        category_name,
        description
      FROM category
      WHERE deleted_at IS NULL AND parent_id IS NULL
    ';
    $stmt = self::$db->query($sql);
    return $stmt->fetchAll();
  }

  public function search(string $query): array
  {
    $sql = '
      SELECT
        id,
        category_name,
        description
      FROM category
      WHERE deleted_at IS NULL AND category_name LIKE :query
    ';
    $stmt = self::$db->query($sql, ['query' => "%$query%"]);
    return $stmt->fetchAll();
  }
}