<?php

namespace App\Models;

use App\Core\Model;

use App\Models\AuditLogModel;

class RoleModel extends Model
{

    /**
     * Get a role by ID with permission count and categories
     * @param int $roleId
     * @return array|null
     */
    public function getRoleById(int $roleId): ?array
    {
        $sql = 'SELECT r.id, r.role_name, r.description, r.created_at,
                       COUNT(rp.permission_id) as permission_count,
                       GROUP_CONCAT(DISTINCT pc.category_name) as permission_categories
                FROM role r
                LEFT JOIN role_permission rp ON r.id = rp.role_id
                LEFT JOIN permission p ON rp.permission_id = p.id
                LEFT JOIN permission_category pc ON p.category_id = pc.id
                WHERE r.id = ?
                GROUP BY r.id';
        $stmt = self::$db->query($sql, [$roleId]);
        $role = $stmt->fetch();
        
        if (!$role) {
            return null;
        }

        return [
            'id' => $role['id'],
            'role_name' => $role['role_name'],
            'description' => $role['description'],
            'permission_count' => (int)$role['permission_count'],
            'permission_categories' => $role['permission_categories'] ? explode(',', $role['permission_categories']) : [],
            'created_at' => substr($role['created_at'], 0, 10) // Format as YYYY-MM-DD
        ];
    }

    /**
     * Get all roles with permission count and categories, ordered by role name
     * @return array
     */
    public function getAllRoles(): array
    {
        $sql = 'SELECT r.id, r.role_name, r.description, r.created_at,
                       COUNT(rp.permission_id) as permission_count,
                       GROUP_CONCAT(DISTINCT pc.category_name) as permission_categories
                FROM role r
                LEFT JOIN role_permission rp ON r.id = rp.role_id
                LEFT JOIN permission p ON rp.permission_id = p.id
                LEFT JOIN permission_category pc ON p.category_id = pc.id
                GROUP BY r.id
                ORDER BY r.role_name';
        $stmt = self::$db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Get all roles with permissions grouped by category
     * @return array
     */
    public function getAllRolesPermissionsGrouped(): array
    {
        $sql = '
        SELECT r.id as role_id, 
               r.role_name, 
               r.description,
               r.created_at,
               p.id as permission_id,
               p.permission_name, 
               pc.id as category_id,
               pc.category_name
        FROM role r
        LEFT JOIN role_permission rp ON r.id = rp.role_id
        LEFT JOIN permission p ON rp.permission_id = p.id
        LEFT JOIN permission_category pc ON p.category_id = pc.id
        WHERE r.deleted_at IS NULL
        ORDER BY r.id, pc.category_name, p.permission_name';
        $stmt = self::$db->query($sql);
        $roles = $stmt->fetchAll();


        $result = [];

        foreach ($roles as $role) {
            $roleId = $role['role_id'];
            $categoryKey = strtolower(str_replace(' ', '_', $role['category_name'] ?? ''));

            $userCountCurrentRole = $this->getUserCountByRole($roleId);

            if (!isset($result[$roleId])) {
                $result[$roleId] = [
                    'role_name' => $role['role_name'],
                    'description' => $role['description'],
                    'created_at' => substr($role['created_at'], 0, 10),
                    'permission_count' => 0,
                    'user_count' => $userCountCurrentRole ?? 0,
                    'permission_categories' => [],
                    'permissions' => []
                ];
            }

            if ($role['category_name'] && !in_array($role['category_name'], $result[$roleId]['permission_categories'])) {
                $result[$roleId]['permission_categories'][$role['category_id']] = str_replace(' ', '_', $role['category_name'] ?? '');
            }

            if ($role['permission_name']) {
                if (!isset($result[$roleId]['permissions'][$categoryKey])) {
                    $result[$roleId]['permissions'][$categoryKey] = [];
                }
                $result[$roleId]['permissions'][$categoryKey][] = $role['permission_name'];
                $result[$roleId]['permission_count']++;
            }
        }

        return $result;
    }

    /**
     * Get the count of users assigned to a specific role
     * @param int $roleId 
     * @return array
     */
    public function getUserCountByRole($roleId): int
    {
        $sql = 'SELECT COUNT(*) as user_count FROM user WHERE role_id = ? AND deleted_at IS NULL';
        $stmt = self::$db->query($sql, [$roleId]);
        $result = $stmt->fetch();
        return (int)$result['user_count'];
        
    }

    /**
     * Get a role by Role Name
     * @param string $roleName
     * @return array|null
     */
    public function getRoleByName(string $roleName): array
    {
        $sql = 'SELECT id, role_name, description, created_at FROM role WHERE role_name = ? AND deleted_at IS NULL';
        $stmt = self::$db->query($sql, [$roleName]);
        $result = $stmt ? $stmt->fetch() : null;
        return $result ?: [];
    }

    /**
     * Add a new role with permissions
     * @param array $data Contains role_name, description, permissions (array of permission IDs)
     * @return int|false Role ID or false on failure
     */
    public function addRole(array $data)
    {
        if (empty($data['role_name'])) {
            return false;
        }

        self::$db->beginTransaction();

        try {
            $sql = 'INSERT INTO role (role_name, description, created_at) VALUES (?, ?, ?)';
            $result = self::$db->query($sql, [
                $data['role_name'],
                $data['description'] ?? '',
                date('Y-m-d H:i:s')
            ]);

            if (!$result) {
                self::$db->rollBack();
                return false;
            }

            $roleId = self::$db->lastInsertId();

            if (!empty($data['permissions'])) {
                $insertSql = 'INSERT INTO role_permission (role_id, permission_id, granted_at) VALUES ';
                $insertParts = [];
                $params = [];
                foreach ($data['permissions'] as $permId) {
                    $insertParts[] = '(?, ?, ?)';
                    $params[] = $roleId;
                    $params[] = $permId;
                    $params[] = date('Y-m-d H:i:s');
                }

                $insertSql .= implode(', ', $insertParts);
                if (!self::$db->query($insertSql, $params)) {
                    self::$db->rollBack();
                    return false;
                }
            }

            self::$db->commit();
            return $roleId;
        } catch (\Exception $e) {
            self::$db->rollBack();
            error_log("Error adding role: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update a role by ID
     * @param array $data Contains role_name, description, permissions (array of permission IDs)
     * @return bool True on success, false on failure
     */
    public function updateRole(array $data): bool
    {
        if (empty($data['role_id'])) {
            return false;
        }

        self::$db->beginTransaction();

        try {
            // Fetch the current role details
            $currentRole = $this->getRoleById($data['role_id']);
            if (!$currentRole) {
                self::$db->rollBack();
                return false; // Role does not exist
            }

            if ($currentRole['role_name'] !== $data['role_name']) {
                $existingRole = $this->getRoleByName($data['role_name']);
                if ($existingRole) {
                    self::$db->rollBack();
                    return false; // Role name already exists
                }
            }

            // Update role details
            $sql = 'UPDATE role SET role_name = ?, description = ? WHERE id = ?';
            $result = self::$db->query($sql, [
                $data['role_name'],
                $data['description'] ?? '',
                $data['role_id']
            ]);

            if (!$result) {
                self::$db->rollBack();
                return false;
            }

            $roleId = $data['role_id'];
            $permissionsToAdd = $data['addedPermissions'] ?? [];
            $permissionsToRemove = $data['removedPermissions'] ?? [];

            // Add new permissions
            if (!empty($permissionsToAdd)) {
                $insertSql = 'INSERT INTO role_permission (role_id, permission_id, granted_at) VALUES ';
                $insertParts = [];
                $params = [];
                foreach ($permissionsToAdd as $permId) {
                    $insertParts[] = '(?, ?, ?)';
                    $params[] = $roleId;
                    $params[] = $permId;
                    $params[] = date('Y-m-d H:i:s');
                }
                $insertSql .= implode(', ', $insertParts);

                if (!self::$db->query($insertSql, $params)) {
                    self::$db->rollBack();
                    return false;
                }
            }

            // Remove permissions
            if (!empty($permissionsToRemove)) {
                $placeholders = implode(',', array_fill(0, count($permissionsToRemove), '?'));
                $deleteSql = "DELETE FROM role_permission WHERE role_id = ? AND permission_id IN ($placeholders)";
                $params = array_merge([$roleId], $permissionsToRemove);

                if (!self::$db->query($deleteSql, $params)) {
                    self::$db->rollBack();
                    return false;
                }
            }

            // Commit transaction
            self::$db->commit();

            return true;
        } catch (\Exception $e) {
            // Rollback transaction on error
            self::$db->rollBack();
            error_log("Error updating role: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a role by ID
     * @param int $roleId
     * @return bool True on success, false on failure
     */
    public function deleteRole(int $roleId): bool
    {
        if (empty($roleId)) {
            return false;
        }

        self::$db->beginTransaction();

        try {
            // Delete associated permissions
            $deletePermissionsSql = 'DELETE FROM role_permission WHERE role_id = ?';
            $deletePermissionsResult = self::$db->query($deletePermissionsSql, [$roleId]);

            if (!$deletePermissionsResult) {
                self::$db->rollBack();
                return false;
            }

            // Delete the role
            // Check if assigned users are soft deleted
            
            $activeUserCount= RoleModel::getUserCountByRole($roleId);

            if ($activeUserCount > 0) {
                self::$db->rollBack();
                return false;
            }

            $deleteRoleSql = 'UPDATE role SET deleted_at = ? WHERE id = ?';
            $deleteRoleResult = self::$db->query($deleteRoleSql, [date('Y-m-d H:i:s'), $roleId]);

            if (!$deleteRoleResult) {
                self::$db->rollBack();
                return false;
            }

            self::$db->commit();
            return true;
        } catch (\Exception $e) {
            self::$db->rollBack();
            error_log("Error deleting role: " . $e->getMessage());
            return false;
        }
    }

    
    
}