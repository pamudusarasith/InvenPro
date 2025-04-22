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

  public function createUser(array $data): void
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

    $auditLogModel = new AuditLogModel();
    $auditLogModel->logAction(
        tableName: 'user',
        recordId: $id,
        actionType: 'CREATE',
        changes: json_encode($data),
        metadata: json_encode(['ip' => $_SERVER['REMOTE_ADDR'], 'user_agent' => $_SERVER['HTTP_USER_AGENT']]),
        changedBy: isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null,
        branchId: isset($data['branch_id']) ? $data['branch_id'] : null
    );

    $_SESSION['message'] = 'User created successfully';
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

    // Log the action
    $auditLogModel = new AuditLogModel();
    $auditLogModel->logAction(
        tableName: 'user',
        recordId: $id,
        actionType: 'UPDATE',
        changes: json_encode(['id' => $id, $data]),
        metadata: json_encode(['ip' => $_SERVER['REMOTE_ADDR'], 'user_agent' => $_SERVER['HTTP_USER_AGENT']]),
        changedBy: isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null,
        branchId: isset($data['branch_id']) ? $data['branch_id'] : null
    );

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

      // Log the action
      $auditLogModel = new AuditLogModel();
      $auditLogModel->logAction(
          tableName: 'user',
          recordId: $id,
          actionType: 'DELETE',
          changes: json_encode($user),
          metadata: json_encode(['ip' => $_SERVER['REMOTE_ADDR'], 'user_agent' => $_SERVER['HTTP_USER_AGENT']]),
          changedBy: isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null,
          branchId: isset($user['branch_id']) ? $user['branch_id'] : null
      );

      $_SESSION['message'] = 'User deleted successfully';
      $_SESSION['message_type'] = 'success';
  }
}

  