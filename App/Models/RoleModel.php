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
        $sql = 'SELECT COUNT(*) as user_count FROM user WHERE role_id = ?';
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
        $sql = 'SELECT id, role_name, description, created_at FROM role WHERE role_name = ?';
        $stmt = self::$db->query($sql, [$roleName]);
        $result = $stmt ? $stmt->fetch() : null;
        return $result ?: [];
    }
    

    /**
     * Get permission names for a role, grouped by category
     * @param int $roleId
     * @return array
     */
    public function getPermissionsByRole(int $roleId): array
    {
        $sql = 'SELECT p.id, p.permission_name, p.description, pc.category_name
                FROM permission p
                JOIN role_permission rp ON p.id = rp.permission_id
                JOIN permission_category pc ON p.category_id = pc.id
                WHERE rp.role_id = ?
                ORDER BY pc.category_name, p.permission_name';
        $stmt = self::$db->query($sql, [$roleId]);
        $rows = $stmt->fetchAll();
    
        $result = [];
    
        foreach ($rows as $row) {
            $categoryKey = strtolower(str_replace(' ', '_', $row['category_name']));
            if (!isset($result[$roleId])) {
                $result[$roleId] = [];
            }
            if (!isset($result[$roleId][$categoryKey])) {
                $result[$roleId][$categoryKey] = [];
            }
            $result[$roleId][$categoryKey][] = $row['description'];
        }
    
        return $result;
    }

    

    /**
     * Get all permission categories by
     * @return array
     */
    public function getRolePermissionCategories(int $roleId): array
    {
        $sql = 'SELECT pc.category_name
                FROM permission p
                JOIN role_permission rp ON p.id = rp.permission_id
                JOIN permission_category pc ON p.category_id = pc.id
                WHERE rp.role_id = ?
                GROUP BY pc.category_name
                ORDER BY pc.category_name';
        $stmt = self::$db->query($sql, [$roleId]);
        $categories = $stmt->fetchAll();
        return array_column($categories, 'category_name');
    }


    /**
     * Get permission names for a role (for $rolePermissions structure)
     * @param int $roleId
     * @return array
     */
    public function getRolePermissionIds(int $roleId): array
    {
        $sql = 'SELECT pr.id
                FROM permission pr
                JOIN role_permission rp ON pr.id = rp.permission_id
                WHERE rp.role_id = ?
                ORDER BY pr.id';
        $stmt = self::$db->query($sql, [$roleId]);
        $permissions = $stmt->fetchAll();

        return array_column($permissions, 'id');
    }

    /**
     * Get permission names for a role, grouped by category
     * @param int $roleId
     * @return array
     */
    public function getRolePermissionNamesGrouped(int $roleId): array
    {
        $sql = 'SELECT pc.category_name, p.permission_name
                FROM permission p
                JOIN role_permission rp ON p.id = rp.permission_id
                JOIN permission_category pc ON p.category_id = pc.id
                WHERE rp.role_id = ?
                ORDER BY pc.category_name, p.permission_name';
        $stmt = self::$db->query($sql, [$roleId]);
        $rows = $stmt->fetchAll();
    
        $result = [];
    
        foreach ($rows as $row) {
            $categoryKey = strtolower(str_replace(' ', '_', $row['category_name']));
            if (!isset($result[$roleId])) {
                $result[$roleId] = [];
            }
            if (!isset($result[$roleId][$categoryKey])) {
                $result[$roleId][$categoryKey] = [];
            }
            $result[$roleId][$categoryKey][] = $row['permission_name'];
        }
    
        return $result;
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

        $sql = 'INSERT INTO role (role_name, description, created_at) VALUES (?, ?, ?)';
        $result = self::$db->query($sql, [
            $data['role_name'],
            $data['description'] ?? '',
            date('Y-m-d H:i:s')
        ]);

        if (!$result) {
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
                return false;
            }
        }

        $auditLogModel = new AuditLogModel();
        $auditLogModel->logAction(
        tableName: 'role',
        recordId: $roleId,
        actionType: 'CREATE',
        changes: json_encode(['id' => $roleId , $data]),
        metadata: json_encode(['ip' => $_SERVER['REMOTE_ADDR'], 'user_agent' => $_SERVER['HTTP_USER_AGENT']]),
        changedBy: isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null,
        branchId: isset($data['branch_id']) ? $data['branch_id'] : null  
        );


        return $roleId;
    }
    
    

    /**
     * Update an existing role
     * @param int $roleId
     * @param array $data
     * @return bool
     */
    public function updateRole(int $roleId, array $data): bool
    {
        $sql = 'UPDATE role SET role_name = ?, description = ?, created_at = ? WHERE id = ?';
        return self::$db->query($sql, [
            $data['role_name'],
            $data['description'],
            $data['created_at'] ?? date('Y-m-d H:i:s'),
            $roleId
        ]);

    }

    /**
     * Update permissions for a role
     * @param int $roleId
     * @param array $permissionIds
     * @return bool
     */
    public function updateRolePermissions(int $roleId, array $permissionIds): bool
    {
        self::$db->beginTransaction();

        try {
            // Delete existing permissions
            $deleteSql = 'DELETE FROM role_permission WHERE role_id = ?';
            self::$db->query($deleteSql, [$roleId]);

            // Insert new permissions
            if (!empty($permissionIds)) {
                $insertSql = 'INSERT INTO role_permission (role_id, permission_id, granted_at) VALUES ';
                $insertParts = [];
                $params = [];

                foreach ($permissionIds as $permId) {
                    $insertParts[] = '(?, ?, ?)';
                    $params[] = $roleId;
                    $params[] = $permId;
                    $params[] = date('Y-m-d H:i:s');
                }

                $insertSql .= implode(', ', $insertParts);
                self::$db->query($insertSql, $params);
            }

            self::$db->commit();
            return true;
        } catch (\Exception $e) {
            self::$db->rollBack();
            error_log('Error updating role permissions: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get role permissions details for display (similar to $rolePermissionsDetails)
     * @param int $roleId
     * @return array
     */
    public function getRolePermissionsDetails(int $roleId): array
    {
        $sql = 'SELECT p.id, p.permission_name, p.description, pc.category_name
                FROM permission p
                JOIN role_permission rp ON p.id = rp.permission_id
                JOIN permission_category pc ON p.category_id = pc.id
                WHERE rp.role_id = ?
                ORDER BY pc.category_name, p.permission_name';
        $stmt = self::$db->query($sql, [$roleId]);
        $permissions = $stmt->fetchAll();

        $result = [];
        foreach ($permissions as $perm) {
            $categoryKey = strtolower(str_replace(' ', '_', $perm['category_name']));
            if (!isset($result[$categoryKey])) {
                $result[$categoryKey] = [];
            }
            $result[$categoryKey][] = $perm['description'];
        }

        return $result;
    }
}
?>