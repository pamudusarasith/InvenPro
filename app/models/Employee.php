<?php

namespace App\Models;

use App;

class Employee
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
            e.id,
            e.email,
            e.role_id,
            e.branch_id,
            e.full_name,
            e.phone_number,
            e.address,
            e.joining_date,
            e.last_login,
            e.is_active,
            e.failed_login_attempts,
            e.account_locked_until,
            r.name AS role_name,
            r.description AS role_description,
            GROUP_CONCAT(DISTINCT p.name) AS permissions,
            b.name AS branch_name
        FROM
            employee e
            LEFT JOIN role r ON e.role_id = r.id
            LEFT JOIN role_permission rp ON r.id = rp.role_id
            LEFT JOIN permission p ON rp.permission_id = p.id
            LEFT JOIN branch b ON e.branch_id = b.id
        WHERE
            1=1
        GROUP BY
            e.id
        ORDER BY
            e.joining_date DESC;");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUserById(int $id): array
    {
        $stmt = $this->dbh->prepare("
            SELECT 
                e.*,
                r.name as role_name,
                b.name as branch_name
            FROM employee e
            JOIN role r ON e.role_id = r.id
            JOIN branch b ON e.branch_id = b.id
            WHERE e.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getUserByEmail(string $email): array
    {
        $stmt = $this->dbh->prepare("
            SELECT
                e.*,
                r.name as role_name,
                b.name as branch_name
            FROM employee e
            JOIN role r ON e.role_id = r.id
            JOIN branch b ON e.branch_id = b.id
            WHERE e.email = :email
        ");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function addUser(array $userData): int
    {
        $stmt = $this->dbh->prepare("
            INSERT INTO employee (
                email, 
                password, 
                role_id, 
                branch_id, 
                full_name, 
                phone_number
            ) VALUES (
                :email,
                :password,
                :role_id,
                :branch_id,
                :full_name,
                :phone_number
            )
        ");

        $stmt->execute([
            'email' => $userData['email'],
            'password' => password_hash($userData['password'], PASSWORD_DEFAULT),
            'role_id' => $userData['role_id'],
            'branch_id' => $userData['branch_id'],
            'full_name' => $userData['full_name'],
            'phone_number' => $userData['phone']
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
            'phone_number' => $userData['phone']
        ];

        $query = "
            UPDATE employee
            SET
                email = :email,
                role_id = :role_id,
                branch_id = :branch_id,
                full_name = :full_name,
                phone_number = :phone_number
        ";

        if (!empty($userData['password'])) {
            $query .= ", password = :password";
            $params['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        }

        $query .= " WHERE id = :id";

        $stmt = $this->dbh->prepare($query);
        return $stmt->execute($params);
    }

    public function deleteUser(int $id): bool
    {
        $stmt = $this->dbh->prepare("
        DELETE FROM employee 
        WHERE id = :id
    ");

        return $stmt->execute([
            'id' => $id
        ]);
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
                e.*,
                r.name as role_name,
                b.name as branch_name
            FROM employee e
            JOIN role r ON e.role_id = r.id
            JOIN branch b ON e.branch_id = b.id
            WHERE e.email = :email AND e.status = 1
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
