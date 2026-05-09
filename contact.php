<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Contact | Ilyes Bensaid Portfolio';
$currentPage = 'contact';
$csrfToken = generateCsrfToken();

require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container narrow">
        <h1>Contact Me</h1>
        <p>Have a project idea or collaboration opportunity? Send me a message.</p>

        <form id="contactForm" class="contact-form" novalidate data-endpoint="<?= e(appUrl('ajax/save-contact.php')); ?>">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken); ?>">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
                <small class="error-text" data-error-for="name"></small>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                <small class="error-text" data-error-for="email"></small>
            </div>

            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" required>
                <small class="error-text" data-error-for="subject"></small>
            </div>

            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="6" required></textarea>
                <small class="error-text" data-error-for="message"></small>
            </div>

            <button type="submit" class="btn btn-primary">Send Message</button>
            <p id="formStatus" class="form-status" aria-live="polite"></p>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
