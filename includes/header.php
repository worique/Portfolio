<?php
require_once __DIR__ . '/auth.php';

ensureSessionStarted();
$pageTitle = $pageTitle ?? 'Ilyes Bensaid | Full-Stack Web Developer';
$currentPage = $currentPage ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portfolio of Ilyes Bensaid, Full-Stack Web Developer specialized in modern web applications.">
    <title><?= e($pageTitle); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(appUrl('assets/css/style.css')); ?>">
</head>
<body>
<header class="site-header">
    <nav class="navbar container" aria-label="Main Navigation">
        <a href="<?= e(appUrl('index.php')); ?>" class="logo">Ilyes<span>Bensaid</span></a>

        <button class="menu-toggle" id="menuToggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="navLinks">
            <span></span><span></span><span></span>
        </button>

        <ul class="nav-links" id="navLinks">
            <li><a href="<?= e(appUrl('index.php')); ?>" class="<?= $currentPage === 'home' ? 'active' : ''; ?>">Home</a></li>
            <li><a href="<?= e(appUrl('about.php')); ?>" class="<?= $currentPage === 'about' ? 'active' : ''; ?>">About</a></li>
            <li><a href="<?= e(appUrl('projects.php')); ?>" class="<?= $currentPage === 'projects' ? 'active' : ''; ?>">Projects</a></li>
            <li><a href="<?= e(appUrl('contact.php')); ?>" class="<?= $currentPage === 'contact' ? 'active' : ''; ?>">Contact</a></li>
            <?php if (isLoggedIn()): ?>
                <li><a href="<?= e(appUrl('dashboard/index.php')); ?>" class="<?= $currentPage === 'dashboard' ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="<?= e(appUrl('dashboard/logout.php')); ?>">Logout</a></li>
            <?php else: ?>
                <li><a href="<?= e(appUrl('login.php')); ?>" class="<?= $currentPage === 'login' ? 'active' : ''; ?>">Login</a></li>
            <?php endif; ?>
        </ul>

        <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode">
            <span id="themeIcon">🌙</span>
        </button>
    </nav>
</header>
<main>
