<?php
// src/views/incident_list.php
if (!isset($authController) || !$authController->checkAuth()) {
    header('Location: /login');
    exit();
}

require_once __DIR__ . '/layout/header.php';

$incidents = $incidentController->getIncidents();
?>

<h1>Incident List</h1>

<table>
    <thead>
        <tr>
            <th>Incident ID</th>
            <th>Title</th>
            <th>Type</th>
            <th>Severity</th>
            <th>Status</th>
            <th>Reported By</th>
            <th>Report Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($incidents->rowCount() > 0): ?>
            <?php while ($row = $incidents->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['incident_id']) ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['incident_type']) ?></td>
                    <td><?= htmlspecialchars($row['severity']) ?></td>
                    <td><span class="status-badge <?= htmlspecialchars($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                    <td><?= htmlspecialchars($row['reported_by_username']) ?></td>
                    <td><?= htmlspecialchars($row['report_date']) ?></td>
                    <td>
                        <a href="/incidents/<?= htmlspecialchars($row['id']) ?>" class="btn btn-primary btn-sm">View</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8">No incidents reported yet.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
require_once __DIR__ . '/layout/footer.php';
?>