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

  public function search(string $query, int $page, int $itemsPerPage): array
  {
    $offset = ($page - 1) * $itemsPerPage;
    $sql = '
      SELECT
        id,
        category_name,
        description
      FROM category
      WHERE deleted_at IS NULL AND category_name LIKE ?
      LIMIT ? OFFSET ?
    ';
    $stmt = self::$db->query($sql, ["%$query%", $itemsPerPage, $offset]);
    return $stmt->fetchAll();
  }

  public function getCategories(string $query, int $page, int $itemsPerPage): array
  {
    $offset = ($page - 1) * $itemsPerPage;
    $sql = '
      SELECT
        c.id,
        c.category_name,
        c.description,
        pc.category_name AS parent_category_name,
        c.parent_id
      FROM  category c  LEFT JOIN category pc ON c.parent_id = pc.id
      WHERE c.deleted_at IS NULL AND c.category_name LIKE ? LIMIT ? OFFSET ? 
    ';
    $stmt = self::$db->query($sql, ["%$query%", $itemsPerPage, $offset]);
    return $stmt->fetchAll();
  }


  public function getCategoriesCount(string $query): int
  {
    $sql = 'SELECT COUNT(*) FROM category WHERE deleted_at IS NULL AND category_name LIKE ?';
    $stmt = self::$db->query($sql, ["%$query%"]);
    return $stmt->fetchColumn();
  }

  public function createCategory(array $data)
  {
    $sql = '
      INSERT INTO category (category_name, description, parent_id)
      VALUES (?, ?, ?)
    ';

    self::$db->query($sql, [
      $data['category_name'],
      $data['description'],
      $data['parent_id']
    ]);
  }

  public function updateCategory(array $data)
  {
    $sql = '
      UPDATE Category
      SET
        category_name = ?,
        description = ?,
        parent_id = ?
      WHERE id = ?  
    ';

    self::$db->query($sql, [
      $data['category_name'],
      $data['description'],
      $data['parent_id'],
      $data['id']
    ]);
  }


  public function deleteCategory(int $id)
  {
    $sql = '
    UPDATE category
    SET deleted_at = NOW()
    WHERE id = ?
    ';

    self::$db->query($sql, [$id]);
  }
}
