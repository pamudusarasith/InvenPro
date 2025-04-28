<?php

namespace App\Models;

use App\Core\Model;
use App\Models\AuditLogModel;
use Error;

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
        u.last_login,
        u.successfull_login_attempts as login_count
      FROM user u
      LEFT JOIN role r ON u.role_id = r.id
      LEFT JOIN branch b ON u.branch_id = b.id
      WHERE u.deleted_at IS NULL
      ORDER BY u.successfull_login_attempts DESC, u.created_at ASC
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

    $sql .= ' LIMIT ? OFFSET ?';
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
        u.created_at,
        u.successfull_login_attempts as login_count
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

    $auditLogModel = new AuditLogModel();
    $auditLogModel->logAction(
        'user',
        $id,
        'UPDATE_PASSWORD',
        json_encode(['id' => $id, 'password' => 'updated']),
        json_encode(['ip' => $_SERVER['REMOTE_ADDR'], 'user_agent' => $_SERVER['HTTP_USER_AGENT']]),
        isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null,
        isset($_SESSION['user']['branch_id']) ? $_SESSION['user']['branch_id'] : null
    );

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

    $auditLogModel = new AuditLogModel();
    $auditLogModel->logAction(
        'user',
        $id,
        'UPDATE',
        json_encode(['id' => $id, $data]),
        json_encode(['ip' => $_SERVER['REMOTE_ADDR'], 'user_agent' => $_SERVER['HTTP_USER_AGENT']]),
        isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null,
        isset($data['branch_id']) ? $data['branch_id'] : null
    );

    $_SESSION['message'] = 'User updated successfully';
    $_SESSION['message_type'] = 'success';
  }

  public function deleteUser(int $id): void
  {
    $user = $this->getUserById($id);
    if (!$user) {
      throw new \Exception('User not found');
    }

    $sql = 'UPDATE user SET deleted_at = NOW() WHERE id = ?';
    self::$db->query($sql, [$id]);

      $auditLogModel = new AuditLogModel();
      $auditLogModel->logAction(
          'user',
          $id,
          'DELETE',
          json_encode($user),
          json_encode(['ip' => $_SERVER['REMOTE_ADDR'], 'user_agent' => $_SERVER['HTTP_USER_AGENT']]),
          isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null,
          isset($user['branch_id']) ? $user['branch_id'] : null
      );

    $_SESSION['message'] = 'User deleted successfully';
    $_SESSION['message_type'] = 'success';
  }

  public function recordLastLogin(int $id): void
  {
    error_log('recordLastLogin called with id: ' . $id);
    $ip = $_SERVER['REMOTE_ADDR'];
    $sql = 'UPDATE user SET successfull_login_attempts = successfull_login_attempts + 1, last_login = NOW(), last_login_ip = ? WHERE id = ?';
    self::$db->query($sql, [$ip, $id]);

    $auditLogModel = new AuditLogModel();
    $auditLogModel->logAction(
        'user',
        $id,
        'LOGIN',
        json_encode(['id' => $id, 'last_login' => date('Y-m-d H:i:s'), 'last_login_ip' => $ip]),
        json_encode(['ip' => $ip, 'user_agent' => $_SERVER['HTTP_USER_AGENT']]),
        isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null,
        isset($_SESSION['user']['branch_id']) ? $_SESSION['user']['branch_id'] : null
    );
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
      return;
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

  public function getUsersByRole(int $roleId): array
  {
    $sql = 'SELECT id, display_name, email FROM user WHERE role_id = ? AND deleted_at IS NULL AND branch_id = ?';
    $stmt = self::$db->query($sql, [$roleId, $_SESSION['user']['branch_id']]);
    return $stmt->fetchAll();
  }

  public function getUsersByBranch(int $branchId): array
  {
    $sql = 'SELECT id, display_name, email FROM user WHERE branch_id = ? AND deleted_at IS NULL';
    $stmt = self::$db->query($sql, [$branchId]);
    return $stmt->fetchAll();
  }

  public function getUsersByPermission(string $permission): array
  {
    $sql = '
      SELECT u.id, u.display_name, u.email
      FROM user u
      JOIN role r ON u.role_id = r.id
      JOIN role_permission rp ON r.id = rp.role_id
      JOIN permission p ON rp.permission_id = p.id
      WHERE p.permission_name = ? AND u.deleted_at IS NULL AND u.branch_id = ?
    ';
    $stmt = self::$db->query($sql, [$permission, $_SESSION['user']['branch_id']]);
    return $stmt->fetchAll();
  }
}
