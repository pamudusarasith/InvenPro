<?php

namespace App\Controllers;

use App;
use App\Consts;

class UserController 
{
    public function index()
    {
        App\Utils::requireAuth();

        $user = new App\Models\User();
        $users = $user->getAllUsers();
        $roles = $user->getRoles();
        $branches = $user->getBranches();

        App\View::render('Template', [
            'title' => 'Users',
            'view' => 'Users',
            'stylesheets' => ['users', 'search'],
            'scripts' => ['users', 'search'],
            'data' => [
                'users' => $users,
                'roles' => $roles,
                'branches' => $branches
            ]
        ]);
    }

    public function details()
    {
        App\Utils::requireAuth();

        $id = $_GET['id'];
        $user = new App\Models\User();
        $userData = $user->getUserById($id);

        App\View::render('Template', [
            'title' => 'User Details',
            'view' => 'UserDetails',
            'stylesheets' => ['userDetails', 'search'],
            'scripts' => ['userDetails', 'search'],
            'data' => [
                'user' => $userData,
                'roles' => $user->getRoles(),
                'branches' => $user->getBranches()
            ]
        ]);
    }

    public function addUser()
    {
        App\Utils::requireAuth();

        $userData = json_decode(file_get_contents('php://input'), true);
        
        if (!$this->validateUserData($userData)) {
            header(Consts::HEADER_JSON);
            echo json_encode(['success' => false, 'error' => 'Invalid user data']);
            return;
        }

        $user = new App\Models\User();
        $userId = $user->addUser($userData);

        header(Consts::HEADER_JSON);
        if ($userId) {
            $newUser = $user->getUserById($userId);
            echo json_encode(['success' => true, 'data' => $newUser]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to add user']);
        }
    }

    public function updateUser()
    {
        App\Utils::requireAuth();

        $userData = json_decode(file_get_contents('php://input'), true);
        
        if (!$this->validateUserData($userData, true)) {
            header(Consts::HEADER_JSON);
            echo json_encode(['success' => false, 'error' => 'Invalid user data']);
            return;
        }

        $user = new App\Models\User();
        $success = $user->updateUser($userData);

        header(Consts::HEADER_JSON);
        if ($success) {
            $updatedUser = $user->getUserById($userData['id']);
            echo json_encode(['success' => true, 'data' => $updatedUser]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update user']);
        }
    }

    public function search()
    {
        App\Utils::requireAuth();

        $query = $_GET['q'];
        $user = new App\Models\User();
        $users = $user->search($query);

        header(Consts::HEADER_JSON);
        echo json_encode(['success' => true, 'data' => ['query' => $query, 'results' => $users]]);
    }

    private function validateUserData(array $userData, bool $isUpdate = false): bool
    {
        $requiredFields = ['full_name', 'email', 'phone', 'role_id', 'branch_id'];
        if ($isUpdate) {
            $requiredFields[] = 'id';
        }
        
        foreach ($requiredFields as $field) {
            if (!isset($userData[$field]) || empty($userData[$field])) {
                return false;
            }
        }
        
        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        if (!preg_match('/^\+?[\d\s-]{10,}$/', $userData['phone'])) {
            return false;
        }
        
        return true;
    }
}