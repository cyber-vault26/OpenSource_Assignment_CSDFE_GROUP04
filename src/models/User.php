<?php
// src/models/User.php

class User {
    private $conn;
    private $table = 'users';

    public $id;
    public $username;
    public $password_hash;
    public $email;
    public $first_name;
    public $last_name;
    public $role;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Find user by username
    public function findByUsername($username) {
        $query = "SELECT id, username, password_hash, email, first_name, last_name, role, created_at FROM " . $this->table . " WHERE username = :username LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create a new user 
    public function create() {
        $query = "INSERT INTO " . $this->table . " (username, password_hash, email, first_name, last_name, role) VALUES (:username, :password_hash, :email, :first_name, :last_name, :role)";
        $stmt = $this->conn->prepare($query);

        // Sanitize and bind values
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password_hash = $this->password_hash; // Already hashed
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->role = htmlspecialchars(strip_tags($this->role));

        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password_hash', $this->password_hash);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':role', $this->role);

        if ($stmt->execute()) {
            return true;
        }
        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // Get all users (for user management display)
    public function getAllUsers() {
        $query = "SELECT id, username, email, first_name, last_name, role, created_at FROM " . $this->table . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>