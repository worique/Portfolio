<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'About | Ilyes Bensaid Portfolio';
$currentPage = 'about';

require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container narrow">
        <h1>About Me</h1>
        <p>Ilyes Bensaid is a Full-Stack Web Developer focused on building high-quality web platforms with strong back-end architecture and intuitive front-end interfaces. I enjoy solving real-world problems with technology and continuously improving my skills.</p>
    </div>
</section>

<section class="section">
    <div class="container narrow">
        <h2>Education Timeline</h2>
        <div class="timeline">
            <article class="timeline-item">
                <h3>Bachelor in Computer Science (In Progress)</h3>
                <p>University Internet & Web Programming Track</p>
                <span>2023 - Present</span>
            </article>
            <article class="timeline-item">
                <h3>Web Development Specialization</h3>
                <p>Self-paced practical learning on modern full-stack technologies.</p>
                <span>2022 - 2024</span>
            </article>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2>Skills</h2>
        <div class="skills-grid">
            <?php foreach (['HTML5','CSS3','JavaScript','PHP','MySQL','REST APIs','React','Node.js','Git','Responsive Design','UI/UX','Problem Solving'] as $skill): ?>
                <article class="skill-card"><?= e($skill); ?></article>
            <?php endforeach; ?>
        </div>

        <div class="center mt-2">
            <a href="#" class="btn btn-primary">Download CV</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
