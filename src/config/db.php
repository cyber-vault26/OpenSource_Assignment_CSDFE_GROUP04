<?php

class Database {
    private $host = 'localhost'; // MariaDB server is on the same VM
    private $db_name = 'security_incidents_db';
    private $username = 'root'; 
    private $password = 'kali'; 
    private $conn;

    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
            
            exit(); // Stop execution if DB connection fails
        }
        return $this->conn;
    }
}
?>