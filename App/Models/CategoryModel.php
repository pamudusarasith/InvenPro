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

  public function getCategories(int $page, int $itemsPerPage): array
  {
    $offset = ($page - 1) * $itemsPerPage;
    $sql = '
      SELECT
        c.id,
        c.category_name,
        c.description,
        pc.category_name AS parent_category_name
      FROM  category c  LEFT JOIN category pc ON c.parent_id = pc.id
      WHERE c.deleted_at IS NULL LIMIT ? OFFSET ?
    ';
    $stmt = self::$db->query($sql, [$itemsPerPage, $offset]);
    return $stmt->fetchAll();
  }


  public function getCategoriesCount(): int
  {
    $sql = 'SELECT COUNT(*) FROM category WHERE deleted_at IS NULL';
    $stmt = self::$db->query($sql);
    return $stmt->fetchColumn();
  }
}
