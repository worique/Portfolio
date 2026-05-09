<?php
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = getPDO();
    $stmt = $pdo->query('SELECT id, title, description, technologies, image_url, github_link, demo_link, created_at FROM projects ORDER BY created_at DESC');
    echo json_encode([
        'success' => true,
        'projects' => $stmt->fetchAll(),
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unable to fetch projects at the moment.',
    ]);
}
