<?php

namespace App\Models;

use App;

class Roles
{
  private $dbh;

  public function __construct()
  {
    $this->dbh = App\DB::getConnection();
  }

  // Role Management Methods
  public function getAllRoles(): array
  {
    $stmt = $this->dbh->prepare("
      SELECT 
        r.id,
        r.name,
        r.description,
        r.is_active,
        GROUP_CONCAT(
          JSON_OBJECT(
            'id', p.id,
            'name', p.name,
            'description', p.description
          )
        ) as permissions
      FROM role r
      LEFT JOIN role_permission rp ON r.id = rp.role_id
      LEFT JOIN permission p ON rp.permission_id = p.id
      GROUP BY r.id
      ORDER BY r.name
    ");
    
    $stmt->execute();
    $roles = $stmt->fetchAll();

    // Parse JSON string to array for permissions
    foreach ($roles as &$role) {
      $role['permissions'] = $role['permissions'] ? json_decode("[" . $role['permissions'] . "]") : [];
    }

    return $roles;
  }

  public function getRoleById(int $id): ?array
  {
    $stmt = $this->dbh->prepare("
      SELECT 
        r.*,
        GROUP_CONCAT(p.id) as permission_ids
      FROM role r
      LEFT JOIN role_permission rp ON r.id = rp.role_id
      LEFT JOIN permission p ON rp.permission_id = p.id
      WHERE r.id = :id
      GROUP BY r.id
    ");
    
    $stmt->execute(['id' => $id]);
    $role = $stmt->fetch();
    
    if ($role) {
      $role['permission_ids'] = $role['permission_ids'] ? 
        explode(',', $role['permission_ids']) : [];
    }
    
    return $role ?: null;
  }

  public function createRole(array $roleData): int
  {
    try {
      $this->dbh->beginTransaction();

      $stmt = $this->dbh->prepare("
        INSERT INTO role (name, description, is_active)
        VALUES (:name, :description, :is_active)
      ");

      $stmt->execute([
        'name' => $roleData['name'],
        'description' => $roleData['description'] ?? null,
        'is_active' => $roleData['is_active'] ?? 1
      ]);

      $roleId = $this->dbh->lastInsertId();

      // Add permissions if any
      if (!empty($roleData['permissions'])) {
        $this->updateRolePermissions($roleId, $roleData['permissions']);
      }

      $this->dbh->commit();
      return $roleId;

    } catch (\Exception $e) {
      $this->dbh->rollBack();
      throw $e;
    }
  }

  public function updateRole(int $id, array $roleData): bool
  {
    try {
      $this->dbh->beginTransaction();

      $stmt = $this->dbh->prepare("
        UPDATE role 
        SET name = :name,
          description = :description,
          is_active = :is_active
        WHERE id = :id
      ");

      $stmt->execute([
        'id' => $id,
        'name' => $roleData['name'],
        'description' => $roleData['description'] ?? null,
        'is_active' => $roleData['is_active'] ?? 1
      ]);

      // Update permissions
      if (isset($roleData['permissions'])) {
        $this->updateRolePermissions($id, $roleData['permissions']);
      }

      $this->dbh->commit();
      return true;

    } catch (\Exception $e) {
      $this->dbh->rollBack();
      throw $e;
    }
  }

  public function deleteRole(int $id): bool
  {
    try {
      $this->dbh->beginTransaction();

      // First delete role permissions
      $stmt = $this->dbh->prepare("DELETE FROM role_permission WHERE role_id = :id");
      $stmt->execute(['id' => $id]);

      // Then delete the role
      $stmt = $this->dbh->prepare("DELETE FROM role WHERE id = :id");
      $result = $stmt->execute(['id' => $id]);

      $this->dbh->commit();
      return $result;

    } catch (\Exception $e) {
      $this->dbh->rollBack();
      throw $e;
    }
  }

  // Permission Management Methods
  public function getAllPermissions(): array
  {
    $stmt = $this->dbh->prepare("
      SELECT id, name, description 
      FROM permission 
      ORDER BY name
    ");
    
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function getPermissionById(int $id): ?array
  {
    $stmt = $this->dbh->prepare("
      SELECT * FROM permission WHERE id = :id
    ");
    
    $stmt->execute(['id' => $id]);
    return $stmt->fetch() ?: null;
  }

  public function createPermission(array $permissionData): int
  {
    $stmt = $this->dbh->prepare("
      INSERT INTO permission (name, description)
      VALUES (:name, :description)
    ");

    $stmt->execute([
      'name' => $permissionData['name'],
      'description' => $permissionData['description'] ?? null
    ]);

    return (int)$this->dbh->lastInsertId();
  }

  public function updatePermission(int $id, array $permissionData): bool
  {
    $stmt = $this->dbh->prepare("
      UPDATE permission 
      SET name = :name,
        description = :description
      WHERE id = :id
    ");

    return $stmt->execute([
      'id' => $id,
      'name' => $permissionData['name'],
      'description' => $permissionData['description'] ?? null
    ]);
  }

  public function deletePermission(int $id): bool
  {
    try {
      $this->dbh->beginTransaction();

      // First delete from role_permission
      $stmt = $this->dbh->prepare("DELETE FROM role_permission WHERE permission_id = :id");
      $stmt->execute(['id' => $id]);

      // Then delete the permission
      $stmt = $this->dbh->prepare("DELETE FROM permission WHERE id = :id");
      $result = $stmt->execute(['id' => $id]);

      $this->dbh->commit();
      return $result;

    } catch (\Exception $e) {
      $this->dbh->rollBack();
      throw $e;
    }
  }

  // Helper Methods
  private function updateRolePermissions(int $roleId, array $permissionIds): void
  {
    // Delete existing permissions
    $stmt = $this->dbh->prepare("DELETE FROM role_permission WHERE role_id = :role_id");
    $stmt->execute(['role_id' => $roleId]);

    // Insert new permissions
    if (!empty($permissionIds)) {
      $stmt = $this->dbh->prepare("
        INSERT INTO role_permission (role_id, permission_id, granted_by)
        VALUES (:role_id, :permission_id, :granted_by)
      ");

      foreach ($permissionIds as $permissionId) {
        $stmt->execute([
          'role_id' => $roleId,
          'permission_id' => $permissionId,
          'granted_by' => $_SESSION['user_id'] ?? null
        ]);
      }
    }
  }
}