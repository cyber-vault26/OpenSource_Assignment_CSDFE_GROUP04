<?php
// src/views/incident_report_form.php
if (!isset($authController) || !$authController->checkAuth()) {
    header('Location: /login');
    exit();
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $incident_data = [
        'title' => $_POST['title'] ?? '',
        'description' => $_POST['description'] ?? '',
        'incident_type' => $_POST['incident_type'] ?? '',
        'severity' => $_POST['severity'] ?? ''
    ];

    $result = $incidentController->reportIncident($incident_data);
    if ($result['success']) {
        $message = '<div class="message success">Incident ' . htmlspecialchars($result['incident_id']) . ' reported successfully!</div>';
    } else {
        $message = '<div class="message error">' . htmlspecialchars($result['message']) . '</div>';
    }
}

require_once __DIR__ . '/layout/header.php';
?>

<h1>Report New Incident</h1>
<?= $message ?>
<form action="/report" method="POST">
    <label for="title">Incident Title:</label>
    <input type="text" id="title" name="title" required>

    <label for="description">Description:</label>
    <textarea id="description" name="description" rows="6" required></textarea>

    <label for="incident_type">Incident Type:</label>
    <select id="incident_type" name="incident_type" required>
        <option value="">Select Type</option>
        <option value="Phishing">Phishing</option>
        <option value="Malware">Malware</option>
        <option value="DDoS">DDoS</option>
        <option value="Data Breach">Data Breach</option>
        <option value="Insider Threat">Insider Threat</option>
        <option value="Other">Other</option>
    </select>

    <label for="severity">Severity:</label>
    <select id="severity" name="severity" required>
        <option value="">Select Severity</option>
        <option value="Low">Low</option>
        <option value="Medium">Medium</option>
        <option value="High">High</option>
        <option value="Critical">Critical</option>
    </select>

    <button type="submit">Submit Incident Report</button>
</form>

<?php
require_once __DIR__ . '/layout/footer.php';
?>