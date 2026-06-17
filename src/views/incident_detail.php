<?php
// src/views/incident_detail.php
if (!isset($authController) || !$authController->checkAuth()) {
    header('Location: /login');
    exit();
}

require_once __DIR__ . '/layout/header.php';

$incident_id_from_url = $segments[1] ?? null; // From public/index.php routing

$incident = null;
if ($incident_id_from_url) {
    $incident = $incidentController->getSingleIncident($incident_id_from_url);
}

$message = '';
// Handle update submission for Part C new feature
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'] ?? '';
    $resolution_notes = $_POST['resolution_notes'] ?? '';
    $resolution_date = $_POST['resolution_date'] ?? null;

    $update_result = $incidentController->updateIncidentStatus(
        $incident_id_from_url,
        $new_status,
        $resolution_notes,
        $resolution_date
    );

    if ($update_result['success']) {
        $message = '<div class="message success">' . htmlspecialchars($update_result['message']) . '</div>';
        // Refresh incident data after update
        $incident = $incidentController->getSingleIncident($incident_id_from_url);
    } else {
        $message = '<div class="message error">' . htmlspecialchars($update_result['message']) . '</div>';
    }
}

if ($incident):
?>

<h1>Incident Details: <?= htmlspecialchars($incident['incident_id']) ?></h1>
<?= $message ?>

<div class="incident-detail-card">
    <p><strong>Incident ID:</strong> <?= htmlspecialchars($incident['incident_id']) ?></p>
    <p><strong>Title:</strong> <?= htmlspecialchars($incident['title']) ?></p>
    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($incident['description'])) ?></p>
    <p><strong>Type:</strong> <?= htmlspecialchars($incident['incident_type']) ?></p>
    <p><strong>Severity:</strong> <?= htmlspecialchars($incident['severity']) ?></p>
    <p><strong>Status:</strong> <span class="status-badge <?= htmlspecialchars($incident['status']) ?>"><?= htmlspecialchars($incident['status']) ?></span></p>
    <p><strong>Reported By:</strong> <?= htmlspecialchars($incident['reported_by_username']) ?></p>
    <p><strong>Report Date:</strong> <?= htmlspecialchars($incident['report_date']) ?></p>
    <p><strong>Last Updated:</strong> <?= htmlspecialchars($incident['last_updated_date']) ?></p>
    <?php if ($incident['resolution_notes']): ?>
        <p><strong>Resolution Notes:</strong> <?= nl2br(htmlspecialchars($incident['resolution_notes'])) ?></p>
    <?php endif; ?>
    <?php if ($incident['resolution_date']): ?>
        <p><strong>Resolution Date:</strong> <?= htmlspecialchars($incident['resolution_date']) ?></p>
    <?php endif; ?>
</div>

<?php
// Allow updating status only for authorized roles (admin/responder)
if ($authController->getUserRole() == 'admin' || $authController->getUserRole() == 'responder'):
?>
<h2>Update Incident Status</h2>
<form action="/incidents/<?= htmlspecialchars($incident['id']) ?>" method="POST">
    <input type="hidden" name="update_status" value="1">
    <label for="status">Status:</label>
    <select id="status" name="status" required>
        <option value="New" <?= ($incident['status'] == 'New') ? 'selected' : '' ?>>New</option>
        <option value="Acknowledged" <?= ($incident['status'] == 'Acknowledged') ? 'selected' : '' ?>>Acknowledged</option>
        <option value="Investigating" <?= ($incident['status'] == 'Investigating') ? 'selected' : '' ?>>Investigating</option>
        <option value="Contained" <?= ($incident['status'] == 'Contained') ? 'selected' : '' ?>>Contained</option>
        <option value="Resolved" <?= ($incident['status'] == 'Resolved') ? 'selected' : '' ?>>Resolved</option>
        <option value="Closed" <?= ($incident['status'] == 'Closed') ? 'selected' : '' ?>>Closed</option>
    </select>

    <label for="resolution_notes">Resolution Notes:</label>
    <textarea id="resolution_notes" name="resolution_notes" rows="4"><?= htmlspecialchars($incident['resolution_notes'] ?? '') ?></textarea>

    <label for="resolution_date">Resolution Date (Optional - defaults to current time if left blank on update):</label>
    <input type="datetime-local" id="resolution_date" name="resolution_date" value="<?= (isset($incident['resolution_date']) && $incident['resolution_date']) ? date('Y-m-d\TH:i', strtotime($incident['resolution_date'])) : '' ?>">

    <button type="submit" class="btn btn-info">Update Incident</button>
</form>
<?php endif; ?>


<?php else: ?>
    <div class="message error">Incident not found.</div>
<?php endif; ?>

<?php
require_once __DIR__ . '/layout/footer.php';
?>