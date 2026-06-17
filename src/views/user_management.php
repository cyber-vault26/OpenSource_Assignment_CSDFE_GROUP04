<?php
// src/views/user_management.php
if (!isset($authController) || !$authController->checkAuth() || $authController->getUserRole() !== 'admin') {
    header('Location: /login');
    exit();
}

require_once __DIR__ . '/layout/header.php';

$database = new Database();
$db = $database->connect();
$userModel = new User($db);

$users = $userModel->getAllUsers();
?>

<h1>User Management</h1>
<p>This section is only accessible by 'admin' users.</p>

<h2>All System Users</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Full Name</th>
            <th>Role</th>
            <th>Created At</th>
            <!-- Add actions like edit/delete if you want -->
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">No users found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
require_once __DIR__ . '/layout/footer.php';
?>