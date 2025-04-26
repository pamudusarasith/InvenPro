<?php

namespace App\Models;

use App\Core\Model;
use App\Models\AuditLogModel;

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

  public function getUsersCount(string $search = '', string $roleId = '', string $branchId = '', string $status = ''): int
  {
    $sql = 'SELECT COUNT(*) FROM user u WHERE u.deleted_at IS NULL';
    $params = [];

    if ($search) {
      $sql .= ' AND (u.display_name LIKE ? OR u.email LIKE ?)';
      $params[] = "%$search%";
      $params[] = "%$search%";
    }

    if ($roleId) {
      $sql .= ' AND u.role_id = ?';
      $params[] = $roleId;
    }

    if ($branchId) {
      $sql .= ' AND u.branch_id = ?';
      $params[] = $branchId;
    }

    if ($status) {
      $sql .= ' AND u.is_locked = ?';
      $params[] = $status === 'locked' ? 1 : 0;
    }

    $stmt = self::$db->query($sql, $params);
    return (int) $stmt->fetchColumn();
  }

  public function getUsers(int $page, int $itemsPerPage, string $search = '', string $roleId = '', string $branchId = '', string $status = '')
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
    ';
    $params = [];

    if ($search) {
      $sql .= ' AND (u.display_name LIKE ? OR u.email LIKE ?)';
      $params[] = "%$search%";
      $params[] = "%$search%";
    }

    if ($roleId) {
      $sql .= ' AND u.role_id = ?';
      $params[] = $roleId;
    }

    if ($branchId) {
      $sql .= ' AND u.branch_id = ?';
      $params[] = $branchId;
    }

    if ($status) {
      $sql .= ' AND u.is_locked = ?';
      $params[] = $status === 'locked' ? 1 : 0;
    }

    $sql .= ' ORDER BY u.id LIMIT ? OFFSET ?';
    $params[] = $itemsPerPage;
    $params[] = $offset;

    $stmt = self::$db->query($sql, $params);
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
        b.id AS branch_id,
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

  public function createUser(array $data): int
  {
    $sql = '
      INSERT INTO user (first_name, last_name, email, password, role_id, branch_id)
      VALUES (?, ?, ?, ?, ?, ?)
    ';
    self::$db->query($sql, [
      $data['first_name'],
      $data['last_name'],
      $data['email'],
      password_hash($data['password'], PASSWORD_BCRYPT),
      $data['role_id'],
      $data['branch_id']
    ]);

    $id = self::$db->lastInsertId();


    return $id;
  }

  public function updateUserPassword(int $id, string $password): void
  {
    $sql = 'UPDATE user SET password = ?,  WHERE id = ?';
    self::$db->query($sql, [password_hash($password, PASSWORD_BCRYPT), $id]);

    $_SESSION['message'] = 'Password updated successfully';
    $_SESSION['message_type'] = 'success';
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

    $_SESSION['message'] = 'User updated successfully';
    $_SESSION['message_type'] = 'success';

  }

  public function deleteUser(int $id): void
  {
      // Fetch user details to get branch_id
      $user = $this->getUserById($id);
      if (!$user) {
          throw new \Exception('User not found');
      }

      // Soft delete the user
      $sql = 'UPDATE user SET deleted_at = NOW() WHERE id = ?';
      self::$db->query($sql, [$id]);

      $_SESSION['message'] = 'User deleted successfully';
      $_SESSION['message_type'] = 'success';
  }

  public function recordLastLogin(int $id): void
  {
    $ip = $_SERVER['REMOTE_ADDR'];
    $sql = 'UPDATE user SET last_login = NOW(), last_login_ip = ? WHERE id = ?';
    self::$db->query($sql, [$ip, $id]);
  }

  public function getFailedLoginAttempts(string $email): int
  {
    $sql = 'SELECT failed_login_attempts FROM user WHERE email = ?';
    $stmt = self::$db->query($sql, [$email]);
    return (int) $stmt->fetchColumn();
  }

  public function recordFailedLoginAttempt(string $email): void
  {
    $timestamp = date('Y-m-d H:i:s');
    
    $attempts = $this->getFailedLoginAttempts($email);
    if ($attempts >= 3) {
      return; // User is already locked out
    }

    $sql = 'UPDATE user SET failed_login_attempts = failed_login_attempts + 1, last_failed_login = ? WHERE email = ?';
    self::$db->query($sql, [$timestamp, $email]);
  }

  public function resetFailedLoginAttempts(string $email): void
  {
    $sql = 'UPDATE user SET failed_login_attempts = 0 WHERE email = ?';
    self::$db->query($sql, [$email]);
  }

  public function resetFailedLoginAttemptsById(int $id): void
  {
    $sql = 'UPDATE user SET failed_login_attempts = 0 WHERE id = ?';
    self::$db->query($sql, [$id]);
  }

  public function lockUser(int $id): void
  {
    $sql = 'UPDATE user SET is_locked = 1 WHERE id = ?';
    self::$db->query($sql, [$id]);
  }

}

  