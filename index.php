<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Home | Ilyes Bensaid Portfolio';
$currentPage = 'home';

$featuredProjects = [];
try {
    $pdo = getPDO();
    $stmt = $pdo->query('SELECT id, title, description, technologies, image_url, github_link, demo_link FROM projects ORDER BY created_at DESC LIMIT 3');
    $featuredProjects = $stmt->fetchAll();
} catch (Throwable $e) {
    $featuredProjects = [];
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="hero section">
    <div class="container hero-grid">
        <div>
            <p class="eyebrow">Hello, I'm</p>
            <h1>Ilyes Bensaid</h1>
            <p class="hero-title">Full-Stack Web Developer</p>
            <p class="hero-description">I build secure, scalable and modern web applications using PHP, JavaScript, and database-driven architectures.</p>
            <div class="hero-actions">
                <a href="<?= e(appUrl('projects.php')); ?>" class="btn btn-primary">View My Work</a>
                <a href="<?= e(appUrl('contact.php')); ?>" class="btn btn-outline">Hire Me</a>
            </div>
        </div>
        <div class="hero-card">
            <h3>Core Stack</h3>
            <ul>
                <li>PHP & MySQL</li>
                <li>JavaScript & React</li>
                <li>Node.js & REST APIs</li>
                <li>Responsive UI/UX</li>
            </ul>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2>Introduction</h2>
        <p>I am a dedicated full-stack developer passionate about creating fast, user-friendly, and maintainable software solutions. I enjoy transforming ideas into products with clean code and modern design principles.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2>Technical Skills</h2>
        <div class="skills-grid">
            <?php foreach (['HTML5','CSS3','JavaScript','PHP','MySQL','React','Node.js','Git & GitHub'] as $skill): ?>
                <article class="skill-card"><?= e($skill); ?></article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <h2>Featured Projects</h2>
            <a href="<?= e(appUrl('projects.php')); ?>" class="text-link">See all projects</a>
        </div>
        <div class="project-grid">
            <?php if (!empty($featuredProjects)): ?>
                <?php foreach ($featuredProjects as $project): ?>
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
                <p class="empty-state">Projects will appear here once the database is connected.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
