<?php
// src/views/login.php
require_once __DIR__ . '/../controllers/AuthController.php'; // Ensure controller is included

$authController = new AuthController(); // Re-instantiate if not already in scope

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($authController->login($username, $password)) {
        header('Location: /dashboard');
        exit();
    } else {
        $message = '<div class="message error">Invalid username or password.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Security Incident Reporting System</title>
    <link rel="stylesheet" href="/css/style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f4f7f6;
        }
        .login-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 30px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login to Incident System</h2>
        <?= $message ?>
        <form action="/login" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>