<?php

namespace App\Models;

use App;

class User
{
    private $dbh;

    public function __construct()
    {
        $this->dbh = App\DB::getConnection();
    }

    public function getAllUsers(): array
    {
        $stmt = $this->dbh->prepare("
        SELECT
            u.id,
            u.email,
            u.role_id,
            u.branch_id,
            u.full_name,
            u.phone_number,
            u.address,
            u.joining_date,
            u.last_login,
            u.is_active,
            u.failed_login_attempts,
            u.account_locked_until,
            r.name AS role_name,
            r.description AS role_description,
            GROUP_CONCAT(DISTINCT p.name) AS permissions,
            b.name AS branch_name
        FROM
            user u
            LEFT JOIN role r ON u.role_id = r.id
            LEFT JOIN role_permission rp ON r.id = rp.role_id
            LEFT JOIN permission p ON rp.permission_id = p.id
            LEFT JOIN branch b ON u.branch_id = b.id
        WHERE
            1=1
        GROUP BY
            u.id
        ORDER BY
            u.joining_date DESC;
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUserById(int $id): array
    {
        $stmt = $this->dbh->prepare("
            SELECT 
                u.*,
                r.name as role_name,
                b.name as branch_name
            FROM user u
            JOIN role r ON u.role_id = r.id
            JOIN branch b ON u.branch_id = b.id
            WHERE u.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getUserByEmail(string $email): ?array
    {
        $stmt = $this->dbh->prepare("
            SELECT
                u.*,
                r.name as role_name,
                b.name as branch_name
            FROM user u
            JOIN role r ON u.role_id = r.id
            JOIN branch b ON u.branch_id = b.id
            WHERE u.email = :email
        ");

        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function hasPermission($permission): bool
    {
        $stmt = $this->dbh->prepare("
            SELECT
                COUNT(*)
            FROM
                role_permission rp
            INNER JOIN permission p ON
                rp.permission_id = p.id
            INNER JOIN role r ON
                rp.role_id = r.id
            WHERE
                r.id = :role_id AND p.name = :permission;
        ");

        $stmt->execute([
            'role_id' => $_SESSION['role_id'],
            'permission' => $permission
        ]);

        return (bool)$stmt->fetchColumn();
    }

    public function addUser(array $userData): int
    {
        // Check for existing email
        $checkStmt = $this->dbh->prepare("SELECT id FROM employee WHERE email = ?");
        $checkStmt->execute([$userData['email']]);
        if ($checkStmt->fetch()) {
            throw new \Exception("Email already exists");
        }

        $stmt = $this->dbh->prepare("
        INSERT INTO employee (
            email,
            password,
            role_id,
            branch_id,
            full_name,
            phone_number,
            address,
            joining_date,
            is_active,
            failed_login_attempts
        ) VALUES (
            :email,
            :password,
            :role_id,
            :branch_id,
            :full_name,
            :phone_number,
            :address,
            :joining_date,
            :is_active,
            0
        )
    ");

        $stmt->execute([
            'email' => $userData['email'],
            'password' => password_hash($userData['password'], PASSWORD_DEFAULT),
            'role_id' => $userData['role_id'],
            'branch_id' => $userData['branch_id'],
            'full_name' => $userData['full_name'],
            'phone_number' => $userData['phone_number'] ?? null,
            'address' => $userData['address'] ?? null,
            'joining_date' => $userData['joining_date'] ?? date('Y-m-d'),
            'is_active' => ((int) $userData['is_active']) ?? 1
        ]);

        return (int)$this->dbh->lastInsertId();
    }

    public function updateUser(array $userData): bool
    {
        $params = [
            'id' => $userData['id'],
            'email' => $userData['email'],
            'role_id' => $userData['role_id'],
            'branch_id' => $userData['branch_id'],
            'full_name' => $userData['full_name'],
            'phone_number' => $userData['phone_number'] ?? null,
            'address' => $userData['address'] ?? null,
            'is_active' => $userData['is_active'] ?? 1
        ];

        $query = "
        UPDATE employee 
        SET 
            email = :email,
            role_id = :role_id,
            branch_id = :branch_id,
            full_name = :full_name,
            phone_number = :phone_number,
            address = :address,
            is_active = :is_active
    ";

        // Only update password if provided
        if (!empty($userData['password'])) {
            $query .= ", password = :password";
            $params['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        }

        // Check if updating joining date
        if (!empty($userData['joining_date'])) {
            $query .= ", joining_date = :joining_date";
            $params['joining_date'] = $userData['joining_date'];
        }

        $query .= " WHERE id = :id";

        $stmt = $this->dbh->prepare($query);
        return $stmt->execute($params);
    }

    public function deleteUser(int $id): bool
    {
        // Soft delete by setting is_active to 0
        $stmt = $this->dbh->prepare("
        UPDATE employee 
        SET is_active = 0 
        WHERE id = :id
    ");

        return $stmt->execute(['id' => $id]);
    }

    public function getRoles(): array
    {
        $stmt = $this->dbh->prepare("SELECT id, name FROM role");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getBranches(): array
    {
        $stmt = $this->dbh->prepare("SELECT id, name FROM branch");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function authenticate(string $email, string $password): ?array
    {
        $stmt = $this->dbh->prepare("
            SELECT 
                u.*,
                r.name as role_name,
                b.name as branch_name
            FROM user u
            JOIN role r ON u.role_id = r.id
            JOIN branch b ON u.branch_id = b.id
            WHERE u.email = :email AND u.status = 1
        ");

        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }

        return null;
    }

    public function updateStatus(int $id, int $status): bool
    {
        $stmt = $this->dbh->prepare("
            UPDATE employee 
            SET status = :status 
            WHERE id = :id
        ");

        return $stmt->execute([
            'id' => $id,
            'status' => $status
        ]);
    }
}
