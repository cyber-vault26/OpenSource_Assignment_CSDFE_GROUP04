<?php
// src/views/dashboard.php
if (!isset($authController) || !$authController->checkAuth()) {
    header('Location: /login');
    exit();
}

// Include header for layout
require_once __DIR__ . '/layout/header.php';
?>

<h1>Dashboard</h1>
<p>Welcome to the Security Incident Reporting System, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
<p>Your role: <?= htmlspecialchars($_SESSION['role']) ?></p>

<!-- Add dashboard widgets here, e.g., incident count by status -->
<h2>Incident Summary</h2>
<div class="incident-summary-cards">
    <!-- Example: You would fetch real counts from the database -->
    <div class="card"><h3>Total Incidents: 10</h3></div>
    <div class="card"><h3>New Incidents: 3</h3></div>
    <div class="card"><h3>Investigating: 4</h3></div>
    <div class="card"><h3>Resolved: 3</h3></div>
</div>
<style>
    .incident-summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .incident-summary-cards .card {
        background-color: #ecf0f1;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .incident-summary-cards .card h3 {
        margin: 0;
        color: #34495e;
    }
</style>

<?php
// Include footer for layout
require_once __DIR__ . '/layout/footer.php';
?>