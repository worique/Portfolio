<?php
require_once __DIR__ . '/../includes/auth.php';
checkAuth();

$pageTitle = 'Add Project | Dashboard';
$currentPage = 'dashboard';
$csrfToken = generateCsrfToken();

$errors = [];
$data = [
    'title' => '',
    'description' => '',
    'technologies' => '',
    'image_url' => '',
    'github_link' => '',
    'demo_link' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($data as $key => $_) {
        $data[$key] = trim($_POST[$key] ?? '');
    }

    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Invalid security token.';
    }

    if (mb_strlen($data['title']) < 3) {
        $errors[] = 'Title must be at least 3 characters.';
    }
    if (mb_strlen($data['description']) < 20) {
        $errors[] = 'Description must be at least 20 characters.';
    }
    if ($data['technologies'] === '') {
        $errors[] = 'Technologies field is required.';
    }

    foreach (['image_url', 'github_link', 'demo_link'] as $urlField) {
        if (!filter_var($data[$urlField], FILTER_VALIDATE_URL)) {
            $errors[] = ucfirst(str_replace('_', ' ', $urlField)) . ' must be a valid URL.';
        }
    }

    if (empty($errors)) {
        $pdo = getPDO();
        $stmt = $pdo->prepare('INSERT INTO projects (title, description, technologies, image_url, github_link, demo_link) VALUES (:title, :description, :technologies, :image_url, :github_link, :demo_link)');
        $stmt->execute($data);

        header('Location: ' . appUrl('dashboard/index.php'));
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="section dashboard-section">
    <div class="container narrow">
        <h1>Add New Project</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert error">
                <?php foreach ($errors as $error): ?>
                    <p><?= e($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="contact-form">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken); ?>">
            <?php foreach ($data as $field => $value): ?>
                <div class="form-group">
                    <label for="<?= e($field); ?>"><?= e(ucwords(str_replace('_', ' ', $field))); ?></label>
                    <?php if ($field === 'description'): ?>
                        <textarea id="<?= e($field); ?>" name="<?= e($field); ?>" rows="6" required><?= e($value); ?></textarea>
                    <?php else: ?>
                        <input type="text" id="<?= e($field); ?>" name="<?= e($field); ?>" value="<?= e($value); ?>" required>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <button class="btn btn-primary" type="submit">Save Project</button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
