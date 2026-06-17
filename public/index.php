<?php
// public/index.php - Main Application Router

require_once __DIR__ . '/../src/config/db.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/controllers/IncidentController.php';

session_start(); // Start session for all requests

$authController = new AuthController();
$incidentController = new IncidentController();

// Basic routing logic
$requestUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$segments = explode('/', $requestUri);

// Default route for authenticated users
if ($authController->checkAuth()) {
    if (empty($segments[0]) || $segments[0] == 'dashboard') {
        require_once __DIR__ . '/../src/views/dashboard.php';
    } elseif ($segments[0] == 'report') {
        require_once __DIR__ . '/../src/views/incident_report_form.php';
    } elseif ($segments[0] == 'incidents') {
        if (isset($segments[1]) && is_numeric($segments[1])) {
            // View single incident: /incidents/123
            $incident_id = $segments[1];
            require_once __DIR__ . '/../src/views/incident_detail.php';
        } elseif (isset($segments[1]) && $segments[1] == 'search') {
            // Search incidents: /incidents/search?id=INC-2026-0001
            require_once __DIR__ . '/../src/views/incident_search_results.php';
        } else {
            // List all incidents: /incidents
            require_once __DIR__ . '/../src/views/incident_list.php';
        }
    } elseif ($segments[0] == 'users' && $authController->getUserRole() == 'admin') {
        require_once __DIR__ . '/../src/views/user_management.php'; // Admin only
    } elseif ($segments[0] == 'logout') {
        $authController->logout();
        header('Location: /login');
        exit();
    } else {
        // If authenticated but page not found, redirect to dashboard
        header('Location: /dashboard');
        exit();
    }
} else {
    // Unauthenticated users always go to login
    if ($segments[0] == 'login') {
        require_once __DIR__ . '/../src/views/login.php';
    } else {
        header('Location: /login');
        exit();
    }
}
?>