<?php

namespace App\Models;

use App\Core\Model;

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
     * Get all roles with permission count and categories
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
                ORDER BY r.id';
        $stmt = self::$db->query($sql);
        $roles = $stmt->fetchAll();

        $result = [];
        foreach ($roles as $role) {
            $result[] = [
                'id' => $role['id'],
                'role_name' => $role['role_name'],
                'description' => $role['description'],
                'permission_count' => (int)$role['permission_count'],
                'permission_categories' => $role['permission_categories'] ? explode(',', $role['permission_categories']) : [],
                'created_at' => substr($role['created_at'], 0, 10)
            ];
        }

        return $result;
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
     * Get all permissions grouped by category
     * @return array
     */
    public function getAllPermissionsGrouped(): array
    {
        $sql = 'SELECT pc.id as category_id, pc.category_name,
                       p.id as permission_id, p.permission_name, p.description
                FROM permission p
                JOIN permission_category pc ON p.category_id = pc.id
                ORDER BY pc.category_name, p.permission_name';
        $stmt = self::$db->query($sql);
        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            $categoryKey = strtolower(str_replace(' ', '_', $row['category_name']));
            if (!isset($result[$categoryKey])) {
                $result[$categoryKey] = [
                    'category_name' => $row['category_name'],
                    'permissions' => []
                ];
            }
            $result[$categoryKey]['permissions'][] = [
                'id' => $row['permission_id'],
                'permission_name' => $row['permission_name'],
                'description' => $row['description'],
                'category_id' => $row['category_id']
            ];
        }

        return $result;
    }

    /**
     * Get permission names for a role (for $rolePermissions structure)
     * @param int $roleId
     * @return array
     */
    public function getRolePermissionNames(int $roleId): array
    {
        $sql = 'SELECT p.permission_name
                FROM permission p
                JOIN role_permission rp ON p.id = rp.permission_id
                WHERE rp.role_id = ?
                ORDER BY p.permission_name';
        $stmt = self::$db->query($sql, [$roleId]);
        $permissions = $stmt->fetchAll();

        return array_column($permissions, 'permission_name');
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
     * Get permission categories
     * @return array
     */
    public function getPermissionCategories(): array
    {
        $sql = 'SELECT id, category_name FROM permission_category ORDER BY category_name';
        $stmt = self::$db->query($sql);
        $categories = $stmt->fetchAll();

        $result = [];
        foreach ($categories as $category) {
            $key = strtolower(str_replace(' ', '_', $category['category_name']));
            $result[$key] = $category['category_name'];
        }

        return $result;
    }

    /**
     * Get user count by role
     * @return array
     */
    public function getUserCountByRole(): array
    {
        $sql = 'SELECT role_id, COUNT(*) as user_count FROM user GROUP BY role_id';
        $stmt = self::$db->query($sql);
        $counts = $stmt->fetchAll();

        $result = [];
        foreach ($counts as $count) {
            $result[$count['role_id']] = (int)$count['user_count'];
        }

        return $result;
    }

    /**
     * Create a new role
     * @param array $data
     * @return int The ID of the created role
     */
    public function createRole(array $data): int
    {
        $sql = 'INSERT INTO role (role_name, description, created_at) VALUES (?, ?, ?)';
        self::$db->query($sql, [
            $data['role_name'],
            $data['description'],
            $data['created_at'] ?? date('Y-m-d H:i:s')
        ]);
        return self::$db->lastInsertId();
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