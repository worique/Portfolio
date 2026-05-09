<?php
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: ' . appUrl('dashboard/index.php'));
    exit;
}

$pageTitle = 'Admin Login | Ilyes Bensaid Portfolio';
$currentPage = 'login';
$error = '';
$csrfToken = generateCsrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $rememberMe = !empty($_POST['remember_me']);

    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid security token. Please refresh and try again.';
    } elseif ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } else {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT id, username, email, password FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            loginUser($user, $rememberMe);
            header('Location: ' . appUrl('dashboard/index.php'));
            exit;
        }

        $error = 'Invalid username or password.';
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container narrow">
        <h1>Admin Login</h1>
        <p>Use your admin credentials to manage portfolio content.</p>

        <form method="POST" class="contact-form auth-form">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken); ?>">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-check">
                <input type="checkbox" id="remember_me" name="remember_me" value="1">
                <label for="remember_me">Remember me for 30 days</label>
            </div>

            <?php if ($error !== ''): ?>
                <p class="form-status error"><?= e($error); ?></p>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
