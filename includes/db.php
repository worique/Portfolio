<?php
/**
 * Database connection helper using PDO.
 *
 * Common local defaults:
 * - XAMPP (Windows/macOS/Linux): host=127.0.0.1, user=root, pass='' OR pass='root'
 * - MAMP (macOS default): host=127.0.0.1, port=8889, user=root, pass=root
 * - WAMP (Windows): host=127.0.0.1, user=root, pass=''
 *
 * Hosting providers usually require custom host/user/password values from control panel.
 */

declare(strict_types=1);

$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbPort = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_NAME') ?: 'portfolio_db';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: 'b7ae8522158'; 
$dbPass = $dbPass === false ? '' : $dbPass;
$dbCharset = 'utf8mb4';

/**
 * Returns a singleton PDO instance.
 */
function getPDO(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    global $dbHost, $dbPort, $dbName, $dbUser, $dbPass, $dbCharset;

    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset={$dbCharset}";

    $credentialAttempts = [
        ['user' => $dbUser, 'pass' => $dbPass, 'label' => 'configured credentials'],
        ['user' => 'root', 'pass' => '', 'label' => "fallback 'root' + empty password"],
        ['user' => 'root', 'pass' => 'root', 'label' => "fallback 'root' + 'root' password"],
    ];

    // Remove duplicate attempts.
    $credentialAttempts = array_values(array_reduce($credentialAttempts, static function (array $carry, array $attempt): array {
        $key = $attempt['user'] . "\0" . $attempt['pass'];
        if (!isset($carry[$key])) {
            $carry[$key] = $attempt;
        }
        return $carry;
    }, []));

    $errors = [];

    foreach ($credentialAttempts as $attempt) {
        try {
            $pdo = new PDO($dsn, $attempt['user'], $attempt['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            return $pdo;
        } catch (PDOException $e) {
            $errors[] = $attempt['label'] . ': ' . $e->getMessage();
        }
    }

    http_response_code(500);

    $safeHost = htmlspecialchars((string) $dbHost, ENT_QUOTES, 'UTF-8');
    $safePort = htmlspecialchars((string) $dbPort, ENT_QUOTES, 'UTF-8');
    $safeDb = htmlspecialchars((string) $dbName, ENT_QUOTES, 'UTF-8');

    exit(
        '<h2>Database connection failed</h2>' .
        '<p>Could not connect to MySQL for database <code>' . $safeDb . '</code> on <code>' . $safeHost . ':' . $safePort . '</code>.</p>' .
        '<p><strong>Quick fixes:</strong></p>' .
        '<ol>' .
        '<li>Ensure MySQL service is running in XAMPP/WAMP/MAMP.</li>' .
        '<li>Run <a href="/portfolio/check-db.php">/check-db.php</a> for diagnostics.</li>' .
        '<li>Run <a href="/portfolio/setup/install.php">/setup/install.php</a> for automatic setup/import.</li>' .
        '<li>Update credentials in <code>includes/db.php</code> or set DB_HOST/DB_PORT/DB_NAME/DB_USER/DB_PASS environment variables.</li>' .
        '</ol>' .
        '<details><summary>Connection errors</summary><pre>' . htmlspecialchars(implode("\n", $errors), ENT_QUOTES, 'UTF-8') . '</pre></details>'
    );
}
