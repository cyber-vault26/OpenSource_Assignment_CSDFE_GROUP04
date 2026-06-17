<?php
// src/views/layout/header.php
$currentPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Incident Reporting System</title>
    <link rel="stylesheet" href="/css/style.css">
    <!-- Add any other CSS frameworks here, e.g., Bootstrap CSS -->
</head>
<body>
    <div class="sidebar">
        <div class="logo">CSDFE Incident System</div>
        <nav>
            <ul>
                <li class="<?= ($currentPath == 'dashboard' || $currentPath == '') ? 'active' : '' ?>">
                    <a href="/dashboard">Dashboard</a>
                </li>
                <li class="<?= ($currentPath == 'report') ? 'active' : '' ?>">
                    <a href="/report">Report Incident</a>
                </li>
                <li class="<?= (strpos($currentPath, 'incidents') !== false) ? 'active' : '' ?>">
                    <a href="/incidents">Incident List</a>
                </li>
                <?php if ($authController->getUserRole() == 'admin'): ?>
                <li class="<?= ($currentPath == 'users') ? 'active' : '' ?>">
                    <a href="/users">User Management</a>
                </li>
                <?php endif; ?>
                <li><a href="/logout">Logout (<?= $_SESSION['username'] ?? 'Guest' ?>)</a></li>
            </ul>
        </nav>
    </div>
    <div class="main-content">
        <header class="navbar">
            <div class="search-bar">
                <form action="/incidents/search" method="GET">
                    <input type="text" name="id" placeholder="Search by Incident ID..." value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">
                    <button type="submit">Search</button>
                </form>
            </div>
            <div class="user-info">
                <span>Welcome, <?= $_SESSION['username'] ?? 'Guest' ?> (<?= $_SESSION['role'] ?? '' ?>)</span>
            </div>
        </header>
        <div class="container">