<?php

declare(strict_types=1);

$host = $_GET['host'] ?? '127.0.0.1';
$port = $_GET['port'] ?? '3306';
$user = $_GET['user'] ?? 'root';
$pass = $_GET['pass'] ?? '';
$targetDb = $_GET['db'] ?? 'portfolio_db';

$results = [
    'mysql_running' => ['ok' => false, 'message' => 'Not checked'],
    'db_connection' => ['ok' => false, 'message' => 'Not checked'],
    'db_exists' => ['ok' => false, 'message' => 'Not checked'],
];

$databases = [];
$mysqlVersion = 'Unknown';
$phpVersion = PHP_VERSION;

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    $results['mysql_running'] = ['ok' => true, 'message' => 'MySQL server is reachable.'];
    $results['db_connection'] = ['ok' => true, 'message' => 'Credentials are valid.'];

    $mysqlVersion = (string) $pdo->query('SELECT VERSION()')->fetchColumn();

    $stmt = $pdo->query('SHOW DATABASES');
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (in_array($targetDb, $databases, true)) {
        $results['db_exists'] = ['ok' => true, 'message' => "Database '{$targetDb}' exists."];
    } else {
        $results['db_exists'] = ['ok' => false, 'message' => "Database '{$targetDb}' not found."];
    }
} catch (PDOException $e) {
    $message = $e->getMessage();

    if (str_contains(strtolower($message), 'connection refused') || str_contains(strtolower($message), 'can\'t connect')) {
        $results['mysql_running'] = ['ok' => false, 'message' => 'MySQL is not reachable. Service may be stopped or wrong host/port used.'];
        $results['db_connection'] = ['ok' => false, 'message' => $message];
        $results['db_exists'] = ['ok' => false, 'message' => 'Cannot check database existence until connection works.'];
    } else {
        $results['mysql_running'] = ['ok' => true, 'message' => 'MySQL server responded, but authentication failed or access is restricted.'];
        $results['db_connection'] = ['ok' => false, 'message' => $message];
        $results['db_exists'] = ['ok' => false, 'message' => 'Cannot check database existence with current credentials.'];
    }
}

function badge(bool $ok): string
{
    return $ok
        ? '<span style="color:#166534;font-weight:700;">PASS</span>'
        : '<span style="color:#991b1b;font-weight:700;">FAIL</span>';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Checker</title>
    <style>
        body{font-family:Arial,sans-serif;background:#f3f4f6;margin:0;color:#111827}
        .wrap{max-width:960px;margin:36px auto;background:#fff;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,.08);padding:24px}
        h1,h2{margin-top:0}
        table{width:100%;border-collapse:collapse;margin-top:14px}
        th,td{border:1px solid #e5e7eb;padding:10px;text-align:left;font-size:14px}
        th{background:#f9fafb}
        .tips{background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:12px;border-radius:8px;margin-top:14px}
        .mono{font-family:ui-monospace,SFMono-Regular,Menlo,monospace}
        input{padding:8px;border:1px solid #d1d5db;border-radius:8px;margin:4px;width:180px}
        button{padding:8px 12px;border:0;border-radius:8px;background:#2563eb;color:#fff;cursor:pointer}
    </style>
</head>
<body>
<div class="wrap">
    <h1>Portfolio Database Configuration Checker</h1>
    <p>Use this page to verify MySQL, credentials, and database availability.</p>

    <form method="get" action="">
        <input name="host" value="<?= htmlspecialchars($host, ENT_QUOTES, 'UTF-8') ?>" placeholder="Host">
        <input name="port" value="<?= htmlspecialchars($port, ENT_QUOTES, 'UTF-8') ?>" placeholder="Port">
        <input name="user" value="<?= htmlspecialchars($user, ENT_QUOTES, 'UTF-8') ?>" placeholder="Username">
        <input name="pass" type="password" value="<?= htmlspecialchars($pass, ENT_QUOTES, 'UTF-8') ?>" placeholder="Password">
        <input name="db" value="<?= htmlspecialchars($targetDb, ENT_QUOTES, 'UTF-8') ?>" placeholder="Database">
        <button type="submit">Run Checks</button>
    </form>

    <h2>Status</h2>
    <table>
        <thead>
        <tr><th>Check</th><th>Result</th><th>Details</th></tr>
        </thead>
        <tbody>
        <tr>
            <td>MySQL Service Reachable</td>
            <td><?= badge($results['mysql_running']['ok']) ?></td>
            <td><?= htmlspecialchars($results['mysql_running']['message'], ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
        <tr>
            <td>Database Connection</td>
            <td><?= badge($results['db_connection']['ok']) ?></td>
            <td><?= htmlspecialchars($results['db_connection']['message'], ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
        <tr>
            <td>Database "<?= htmlspecialchars($targetDb, ENT_QUOTES, 'UTF-8') ?>" Exists</td>
            <td><?= badge($results['db_exists']['ok']) ?></td>
            <td><?= htmlspecialchars($results['db_exists']['message'], ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
        </tbody>
    </table>

    <h2>Environment Info</h2>
    <table>
        <tr><th>PHP Version</th><td class="mono"><?= htmlspecialchars($phpVersion, ENT_QUOTES, 'UTF-8') ?></td></tr>
        <tr><th>MySQL Version</th><td class="mono"><?= htmlspecialchars($mysqlVersion, ENT_QUOTES, 'UTF-8') ?></td></tr>
        <tr><th>Host/Port</th><td class="mono"><?= htmlspecialchars($host . ':' . $port, ENT_QUOTES, 'UTF-8') ?></td></tr>
    </table>

    <h2>Existing Databases</h2>
    <?php if ($databases): ?>
        <ul>
            <?php foreach ($databases as $database): ?>
                <li class="mono"><?= htmlspecialchars((string) $database, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No database list available. Fix connection first.</p>
    <?php endif; ?>

    <div class="tips">
        <strong>Helpful fixes:</strong>
        <ul>
            <li>Start MySQL in XAMPP/WAMP/MAMP control panel.</li>
            <li>Try user <code>root</code> with empty password, then <code>root/root</code>.</li>
            <li>Run installer: <a href="setup/install.php">setup/install.php</a></li>
            <li>Manual SQL import: <code>database/portfolio.sql</code> into <code>portfolio_db</code>.</li>
        </ul>
    </div>
</div>
</body>
</html>
