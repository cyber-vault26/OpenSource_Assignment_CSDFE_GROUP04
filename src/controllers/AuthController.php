<?php


require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/db.php';

class AuthController {
    private $db;
    private $userModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->userModel = new User($this->db);
    }

    public function login($username, $password) {
        $user = $this->userModel->findByUsername($username);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Password is correct, start session
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // Store user role
            $_SESSION['logged_in'] = true;
            return true;
        }
        return false;
    }

    public function logout() {
        session_start();
        session_unset();    // Unset all session variables
        session_destroy();  // Destroy the session
        return true;
    }

    public function checkAuth() {
        session_start();
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public function getUserRole() {
        session_start();
        return $_SESSION['role'] ?? null;
    }

    public function getLoggedInUserId() {
        session_start();
        return $_SESSION['user_id'] ?? null;
    }
}
?>