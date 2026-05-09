<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Projects | Ilyes Bensaid Portfolio';
$currentPage = 'projects';

$projects = [];
try {
    $pdo = getPDO();
    $stmt = $pdo->query('SELECT id, title, description, technologies, image_url, github_link, demo_link FROM projects ORDER BY created_at DESC');
    $projects = $stmt->fetchAll();
} catch (Throwable $e) {
    $projects = [];
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <h1>Projects</h1>
            <button class="btn btn-outline" id="reloadProjectsBtn" data-fetch-url="<?= e(appUrl('ajax/fetch-projects.php')); ?>">Reload via AJAX</button>
        </div>
        <p>Explore full-stack projects built with modern technologies and best coding practices.</p>
        <div class="project-grid" id="projectsContainer">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $project): ?>
                    <article class="project-card">
                        <img src="<?= e($project['image_url']); ?>" alt="<?= e($project['title']); ?>" loading="lazy">
                        <div class="project-content">
                            <h3><?= e($project['title']); ?></h3>
                            <p><?= e($project['description']); ?></p>
                            <p class="tech-tags"><?= e($project['technologies']); ?></p>
                            <div class="project-links">
                                <a href="<?= e($project['github_link']); ?>" target="_blank" rel="noopener">GitHub</a>
                                <a href="<?= e($project['demo_link']); ?>" target="_blank" rel="noopener">Live Demo</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-state">No projects found. Add projects from dashboard.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
