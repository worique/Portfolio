<?php
require_once __DIR__ . '/../includes/auth.php';
checkAuth();

$projectId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$csrfToken = $_GET['csrf_token'] ?? null;

if ($projectId > 0 && verifyCsrfToken($csrfToken)) {
    $pdo = getPDO();
    $stmt = $pdo->prepare('DELETE FROM projects WHERE id = :id');
    $stmt->execute(['id' => $projectId]);
}

header('Location: ' . appUrl('dashboard/index.php'));
exit;
