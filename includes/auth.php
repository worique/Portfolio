<?php
/**
 * Authentication and security helper functions.
 */

declare(strict_types=1);

require_once __DIR__ . '/db.php';

const REMEMBER_COOKIE = 'portfolio_remember';
const APP_SECRET_KEY = 'change_this_secret_in_production_ilyes_portfolio_2026';

function ensureSessionStarted(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function getBasePath(): string
{
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    if ($scriptDir === '.' || $scriptDir === '/') {
        return '';
    }

    $scriptDir = rtrim($scriptDir, '/');
    foreach (['/dashboard', '/ajax', '/includes', '/assets', '/database'] as $suffix) {
        if (str_ends_with($scriptDir, $suffix)) {
            $scriptDir = substr($scriptDir, 0, -strlen($suffix));
            break;
        }
    }

    return rtrim($scriptDir, '/');
}

function appUrl(string $path = ''): string
{
    $basePath = getBasePath();
    $cleanPath = ltrim($path, '/');
    if ($cleanPath === '') {
        return $basePath !== '' ? $basePath . '/' : '/';
    }
    return ($basePath !== '' ? $basePath : '') . '/' . $cleanPath;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function generateCsrfToken(): string
{
    ensureSessionStarted();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(?string $token): bool
{
    ensureSessionStarted();
    return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function isLoggedIn(): bool
{
    ensureSessionStarted();

    if (!empty($_SESSION['user'])) {
        return true;
    }

    return loginFromRememberCookie();
}

function getCurrentUser(): ?array
{
    ensureSessionStarted();
    return $_SESSION['user'] ?? null;
}

function checkAuth(): void
{
    if (!isLoggedIn()) {
        header('Location: ' . appUrl('login.php'));
        exit;
    }
}

function loginUser(array $user, bool $rememberMe = false): void
{
    ensureSessionStarted();
    session_regenerate_id(true);

    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
    ];

    if ($rememberMe) {
        $signature = hash_hmac('sha256', $user['id'] . '|' . $user['password'], APP_SECRET_KEY);
        $payload = base64_encode($user['id'] . ':' . $signature);
        setcookie(REMEMBER_COOKIE, $payload, [
            'expires' => time() + (86400 * 30),
            'path' => getBasePath() !== '' ? getBasePath() . '/' : '/',
            'secure' => !empty($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}

function logoutUser(): void
{
    ensureSessionStarted();

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    setcookie(REMEMBER_COOKIE, '', time() - 3600, getBasePath() !== '' ? getBasePath() . '/' : '/');
    session_destroy();
}

function loginFromRememberCookie(): bool
{
    ensureSessionStarted();

    if (empty($_COOKIE[REMEMBER_COOKIE])) {
        return false;
    }

    $decoded = base64_decode($_COOKIE[REMEMBER_COOKIE], true);
    if ($decoded === false || !str_contains($decoded, ':')) {
        return false;
    }

    [$userId, $signature] = explode(':', $decoded, 2);
    if (!ctype_digit($userId)) {
        return false;
    }

    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT id, username, email, password FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => (int) $userId]);
    $user = $stmt->fetch();

    if (!$user) {
        return false;
    }

    $expectedSignature = hash_hmac('sha256', $user['id'] . '|' . $user['password'], APP_SECRET_KEY);
    if (!hash_equals($expectedSignature, $signature)) {
        return false;
    }

    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
    ];

    return true;
}
