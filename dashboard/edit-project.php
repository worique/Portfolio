<?php
require_once __DIR__ . '/../includes/auth.php';
checkAuth();

$pageTitle = 'Edit Project | Dashboard';
$currentPage = 'dashboard';
$csrfToken = generateCsrfToken();

$projectId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($projectId <= 0) {
    header('Location: ' . appUrl('dashboard/index.php'));
    exit;
}

$pdo = getPDO();
$stmt = $pdo->prepare('SELECT id, title, description, technologies, image_url, github_link, demo_link FROM projects WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $projectId]);
$project = $stmt->fetch();

if (!$project) {
    header('Location: ' . appUrl('dashboard/index.php'));
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Invalid security token.';
    }

    $updatedData = [
        'id' => $projectId,
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'technologies' => trim($_POST['technologies'] ?? ''),
        'image_url' => trim($_POST['image_url'] ?? ''),
        'github_link' => trim($_POST['github_link'] ?? ''),
        'demo_link' => trim($_POST['demo_link'] ?? ''),
    ];

    if (mb_strlen($updatedData['title']) < 3) {
        $errors[] = 'Title must be at least 3 characters.';
    }
    if (mb_strlen($updatedData['description']) < 20) {
        $errors[] = 'Description must be at least 20 characters.';
    }

    foreach (['image_url', 'github_link', 'demo_link'] as $urlField) {
        if (!filter_var($updatedData[$urlField], FILTER_VALIDATE_URL)) {
            $errors[] = ucfirst(str_replace('_', ' ', $urlField)) . ' must be a valid URL.';
        }
    }

    if (empty($errors)) {
        $updateStmt = $pdo->prepare('UPDATE projects SET title = :title, description = :description, technologies = :technologies, image_url = :image_url, github_link = :github_link, demo_link = :demo_link WHERE id = :id');
        $updateStmt->execute($updatedData);
        header('Location: ' . appUrl('dashboard/index.php'));
        exit;
    }

    $project = array_merge($project, $updatedData);
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="section dashboard-section">
    <div class="container narrow">
        <h1>Edit Project</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert error">
                <?php foreach ($errors as $error): ?>
                    <p><?= e($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="contact-form">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken); ?>">

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="<?= e($project['title']); ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="6" required><?= e($project['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="technologies">Technologies</label>
                <input type="text" id="technologies" name="technologies" value="<?= e($project['technologies']); ?>" required>
            </div>

            <div class="form-group">
                <label for="image_url">Image URL</label>
                <input type="text" id="image_url" name="image_url" value="<?= e($project['image_url']); ?>" required>
            </div>

            <div class="form-group">
                <label for="github_link">GitHub Link</label>
                <input type="text" id="github_link" name="github_link" value="<?= e($project['github_link']); ?>" required>
            </div>

            <div class="form-group">
                <label for="demo_link">Demo Link</label>
                <input type="text" id="demo_link" name="demo_link" value="<?= e($project['demo_link']); ?>" required>
            </div>

            <button class="btn btn-primary" type="submit">Update Project</button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
