-- Create the database if it doesn't exist (optional)
CREATE DATABASE IF NOT EXISTS security_incidents_db;

-- Use the database
USE security_incidents_db;

-- Drop tables if they already exist to avoid errors on re-import ( for development)
DROP TABLE IF EXISTS incidents;
DROP TABLE IF EXISTS users;

-- Create the users table for user management
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL, -- Store hashed passwords
    email VARCHAR(100) NOT NULL UNIQUE,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    role ENUM('admin', 'analyst', 'responder') DEFAULT 'analyst', -- Example roles
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create the incidents table for security incident reporting
CREATE TABLE incidents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    incident_id VARCHAR(20) NOT NULL UNIQUE, -- e.g., INC-2026-0001
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    incident_type ENUM('Phishing', 'Malware', 'DDoS', 'Data Breach', 'Insider Threat', 'Other') NOT NULL,
    severity ENUM('Low', 'Medium', 'High', 'Critical') NOT NULL,
    status ENUM('New', 'Acknowledged', 'Investigating', 'Contained', 'Resolved', 'Closed') DEFAULT 'New',
    reported_by_user_id INT NOT NULL, -- Foreign key to users table
    report_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_updated_date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    resolution_notes TEXT NULL, -- Nullable
    resolution_date DATETIME NULL, -- Nullable
    FOREIGN KEY (reported_by_user_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Optional: Insert an initial admin user (for development purposes)
-- IMPORTANT: In a real application, you'd have a registration page.
-- Replace 'admin_password_hash' with a real hashed password for 'adminuser'
-- You can generate a hash in PHP with password_hash('your_password', PASSWORD_DEFAULT);
INSERT INTO users (username, password_hash, email, first_name, last_name, role) VALUES
('adminuser', '$2y$10$YourRealHashedPasswordHerePleaseGenerateThisInPHP', 'admin@example.com', 'Admin', 'User', 'admin');
-- Example placeholder hash, GENERATE A REAL ONE!
-- e.g., use `php -r "echo password_hash('YourSecureAdminPassword', PASSWORD_DEFAULT);"` in your VM CLI
