<?php

namespace App\Models;

use App\Core\Model;

class UserModel extends Model
{
  public function findByEmail(string $email)
  {
    $sql = '
      SELECT
          u.*,
          r.role_name,
          b.branch_name
      FROM user u
      LEFT JOIN role r ON u.role_id = r.id
      LEFT JOIN branch b ON u.branch_id = b.id
      WHERE u.email = ?
      AND u.deleted_at IS NULL
    ';
    $stmt = self::$db->query($sql, [$email]);
    return $stmt->fetch();
  }

  public function getUsersCount(): int
  {
    $sql = 'SELECT COUNT(*) FROM user WHERE deleted_at IS NULL';
    $stmt = self::$db->query($sql);
    return (int) $stmt->fetchColumn();
  }

  public function getUsers(int $page, int $itemsPerPage)
  {
    $offset = ($page - 1) * $itemsPerPage;
    $sql = '
      SELECT
        u.id,
        u.display_name,
        u.email,
        r.role_name,
        b.branch_name,
        u.is_locked,
        u.last_login
      FROM user u
      LEFT JOIN role r ON u.role_id = r.id
      LEFT JOIN branch b ON u.branch_id = b.id
      WHERE u.deleted_at IS NULL
      ORDER BY u.id
      LIMIT ? OFFSET ?
    ';
    $stmt = self::$db->query($sql, [$itemsPerPage, $offset]);
    $result = $stmt->fetchAll();

    foreach ($result as &$user) {
      $user["status"] = $user["is_locked"] ? "Locked" : "Active";
    }
    unset($user);

    return $result;
  }

  public function getUserById(int $id)
  {
    $sql = '
      SELECT
        u.id,
        u.first_name,
        u.last_name,
        u.display_name,
        u.email,
        u.force_profile_setup,
        r.role_name,
        b.branch_name,
        u.is_locked,
        u.failed_login_attempts,
        u.last_login,
        u.last_login_ip,
        u.created_at
      FROM user u
      LEFT JOIN role r ON u.role_id = r.id
      LEFT JOIN branch b ON u.branch_id = b.id
      WHERE u.id = ?
      AND u.deleted_at IS NULL
    ';
    $stmt = self::$db->query($sql, [$id]);
    $result = $stmt->fetch();
    $result["status"] = $result["is_locked"] ? "Locked" : "Active";
    return $result;
  }

  public function createUser(array $data): void
  {
    $sql = '
      INSERT INTO user (email, password, role_id, branch_id)
      VALUES (?, ?, ?, ?)
    ';
    self::$db->query($sql, [
      $data['email'],
      password_hash($data['password'], PASSWORD_BCRYPT),
      $data['role_id'],
      $data['branch_id']
    ]);
  }

  public function updateUser(int $id, array $data): void
  {
    $sql = '
      UPDATE user
      SET
        first_name = ?,
        last_name = ?,
        email = ?,
        role_id = ?,
        branch_id = ?,
        is_locked = ?
      WHERE id = ?
    ';
    self::$db->query($sql, [
      $data['first_name'],
      $data['last_name'],
      $data['email'],
      $data['role_id'],
      $data['branch_id'],
      $data['is_locked'],
      $id
    ]);
  }

  public function deleteUser(int $id): void
  {
    $sql = 'UPDATE user SET deleted_at = NOW() WHERE id = ?';
    self::$db->query($sql, [$id]);
  }
}
