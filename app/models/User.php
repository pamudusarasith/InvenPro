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
                e.id,
                e.full_name as name,
                e.email,
                e.phone_number as phone,
                e.role_id,
                r.name as role,
                e.branch_id,
                b.name as branch,
                CASE 
                    WHEN e.status = 1 THEN 'active'
                    ELSE 'inactive'
                END as status
            FROM employee e
            JOIN role r ON e.role_id = r.id
            JOIN branch b ON e.branch_id = b.id
            ORDER BY e.id DESC
        ");
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