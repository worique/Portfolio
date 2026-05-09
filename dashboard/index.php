<?php
require_once __DIR__ . '/../includes/auth.php';
checkAuth();

$pageTitle = 'Dashboard | Ilyes Bensaid Portfolio';
$currentPage = 'dashboard';
$user = getCurrentUser();

$pdo = getPDO();
$totalProjects = (int) $pdo->query('SELECT COUNT(*) FROM projects')->fetchColumn();
$totalMessages = (int) $pdo->query('SELECT COUNT(*) FROM contacts')->fetchColumn();

$projectsStmt = $pdo->query('SELECT id, title, technologies, created_at FROM projects ORDER BY created_at DESC');
$projects = $projectsStmt->fetchAll();

$messagesStmt = $pdo->query('SELECT id, name, email, subject, message, created_at FROM contacts ORDER BY created_at DESC');
$messages = $messagesStmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<section class="section dashboard-section">
    <div class="container">
        <div class="section-heading">
            <h1>Welcome, <?= e($user['username'] ?? 'Admin'); ?></h1>
            <a href="<?= e(appUrl('dashboard/add-project.php')); ?>" class="btn btn-primary">Add New Project</a>
        </div>

        <div class="stats-grid">
            <article class="stat-card">
                <h3><?= $totalProjects; ?></h3>
                <p>Total Projects</p>
            </article>
            <article class="stat-card">
                <h3><?= $totalMessages; ?></h3>
                <p>Total Messages</p>
            </article>
        </div>

        <h2>Projects</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Technologies</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td><?= e($project['title']); ?></td>
                            <td><?= e($project['technologies']); ?></td>
                            <td><?= e(date('Y-m-d', strtotime($project['created_at']))); ?></td>
                            <td>
                                <a href="<?= e(appUrl('dashboard/edit-project.php?id=' . (int) $project['id'])); ?>" class="table-link">Edit</a>
                                <a href="<?= e(appUrl('dashboard/delete-project.php?id=' . (int) $project['id'] . '&csrf_token=' . urlencode(generateCsrfToken()))); ?>" class="table-link danger" onclick="return confirm('Delete this project?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h2>Contact Messages</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $message): ?>
                        <tr>
                            <td><?= e($message['name']); ?></td>
                            <td><?= e($message['email']); ?></td>
                            <td><?= e($message['subject']); ?></td>
                            <td><?= e($message['message']); ?></td>
                            <td><?= e(date('Y-m-d', strtotime($message['created_at']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
