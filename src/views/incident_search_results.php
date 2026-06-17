<?php
// src/views/incident_search_results.php
if (!isset($authController) || !$authController->checkAuth()) {
    header('Location: /login');
    exit();
}

require_once __DIR__ . '/layout/header.php';

$search_id = $_GET['id'] ?? '';
$results = null;

if (!empty($search_id)) {
    $results = $incidentController->searchIncidents($search_id);
}
?>

<h1>Search Results for "<?= htmlspecialchars($search_id) ?>"</h1>

<?php if ($results && $results->rowCount() > 0): ?>
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
            <?php while ($row = $results->fetch(PDO::FETCH_ASSOC)): ?>
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
        </tbody>
    </table>
<?php else: ?>
    <div class="message error">No incidents found matching "<?= htmlspecialchars($search_id) ?>".</div>
<?php endif; ?>

<?php
require_once __DIR__ . '/layout/footer.php';
?>