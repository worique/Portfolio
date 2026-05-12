<?php

declare(strict_types=1);

$defaultHost = '127.0.0.1';
$defaultPort = '3306';
$defaultDatabase = 'portfolio_db';
$schemaPath = dirname(__DIR__) . '/database/portfolio.sql';

$messages = [];
$autoTestResults = [];
$success = false;

$formData = [
    'host' => $_POST['host'] ?? $defaultHost,
    'port' => $_POST['port'] ?? $defaultPort,
    'database' => $_POST['database'] ?? $defaultDatabase,
    'username' => $_POST['username'] ?? 'root',
    'password' => $_POST['password'] ?? '',
];

/**
 * Build DSN with host/port values.
 */
function buildDsn(string $host, string $port, ?string $database = null): string
{
    $base = "mysql:host={$host};port={$port};charset=utf8mb4";
    return $database ? $base . ";dbname={$database}" : $base;
}

/**
 * Parse SQL file into statements while ignoring comments.
 */
function parseSqlStatements(string $sql): array
{
    $statements = [];
    $buffer = '';

    foreach (preg_split('/\R/', $sql) as $line) {
        $trimmed = trim($line);

        if ($trimmed === '' || str_starts_with($trimmed, '--')) {
            continue;
        }

        $buffer .= $line . "\n";

        if (str_ends_with(trim($line), ';')) {
            $statement = trim($buffer);
            if ($statement !== '') {
                $statements[] = $statement;
            }
            $buffer = '';
        }
    }

    if (trim($buffer) !== '') {
        $statements[] = trim($buffer);
    }

    return $statements;
}

/**
 * Try common local MySQL credentials and return pass/fail results.
 */
function runAutoCredentialTests(string $host, string $port): array
{
    $tests = [
        ['username' => 'root', 'password' => ''],
        ['username' => 'root', 'password' => 'root'],
        ['username' => 'mysql', 'password' => 'mysql'],
    ];

    $results = [];

    foreach ($tests as $test) {
        try {
            new PDO(
                buildDsn($host, $port),
                $test['username'],
                $test['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            $results[] = [
                'username' => $test['username'],
                'password' => $test['password'],
                'status' => true,
                'message' => 'Connection successful',
            ];
        } catch (PDOException $e) {
            $results[] = [
                'username' => $test['username'],
                'password' => $test['password'],
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    return $results;
}

$autoTestResults = runAutoCredentialTests($formData['host'], $formData['port']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!is_file($schemaPath)) {
        $messages[] = ['type' => 'error', 'text' => 'SQL schema file not found: ' . htmlspecialchars($schemaPath, ENT_QUOTES, 'UTF-8')];
    } else {
        try {
            $pdo = new PDO(
                buildDsn($formData['host'], $formData['port']),
                $formData['username'],
                $formData['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            $pdo->exec(sprintf(
                'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
                str_replace('`', '``', $formData['database'])
            ));

            $pdo->exec('USE `' . str_replace('`', '``', $formData['database']) . '`');

            $sql = file_get_contents($schemaPath);
            if ($sql === false) {
                throw new RuntimeException('Could not read SQL schema file.');
            }

            $statements = parseSqlStatements($sql);
            foreach ($statements as $statement) {
                $normalized = strtolower(trim($statement));
                if (str_starts_with($normalized, 'create database') || str_starts_with($normalized, 'use ')) {
                    continue;
                }
                $pdo->exec($statement);
            }

            $success = true;
            $messages[] = ['type' => 'success', 'text' => 'Database setup completed successfully. You will be redirected to the homepage shortly.'];
            header('Refresh: 4; url=../index.php');
        } catch (Throwable $e) {
            $messages[] = ['type' => 'error', 'text' => 'Setup failed: ' . $e->getMessage()];
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Installer</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f4f6f8; color: #1f2937; }
        .container { max-width: 860px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 8px 30px rgba(0,0,0,.08); padding: 24px; }
        h1 { margin-top: 0; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        label { display: block; font-weight: 600; margin-bottom: 6px; }
        input { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; }
        button { margin-top: 16px; background: #0d6efd; border: 0; color: white; padding: 11px 16px; border-radius: 8px; cursor: pointer; }
        button:hover { background: #0b5ed7; }
        .msg { border-radius: 8px; padding: 12px; margin: 10px 0; }
        .msg.success { background: #ecfdf3; color: #166534; border: 1px solid #86efac; }
        .msg.error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; font-size: 14px; }
        th { background: #f9fafb; }
        .ok { color: #15803d; font-weight: 700; }
        .bad { color: #b91c1c; font-weight: 700; }
        .help { background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; padding: 12px; border-radius: 8px; margin-top: 16px; }
        @media (max-width: 720px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="container">
    <h1>Portfolio Database Installer</h1>
    <p>This tool checks common MySQL credentials, creates <strong>portfolio_db</strong>, and imports <code>database/portfolio.sql</code>.</p>

    <?php foreach ($messages as $message): ?>
        <div class="msg <?= $message['type'] === 'success' ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message['text'], ENT_QUOTES, 'UTF-8') ?>
            <?php if ($success): ?>
                <br><a href="../index.php">Go now</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <h2>Automatic Credential Test</h2>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Password</th>
                <th>Status</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($autoTestResults as $result): ?>
            <tr>
                <td><?= htmlspecialchars($result['username'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= $result['password'] === '' ? '(empty)' : htmlspecialchars($result['password'], ENT_QUOTES, 'UTF-8') ?></td>
                <td class="<?= $result['status'] ? 'ok' : 'bad' ?>"><?= $result['status'] ? 'PASS' : 'FAIL' ?></td>
                <td><?= htmlspecialchars($result['message'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Manual Setup Form</h2>
    <form method="post" action="">
        <div class="grid">
            <div>
                <label for="host">MySQL Host</label>
                <input id="host" name="host" required value="<?= htmlspecialchars($formData['host'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div>
                <label for="port">MySQL Port</label>
                <input id="port" name="port" required value="<?= htmlspecialchars($formData['port'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div>
                <label for="database">Database Name</label>
                <input id="database" name="database" required value="<?= htmlspecialchars($formData['database'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div>
                <label for="username">MySQL Username</label>
                <input id="username" name="username" required value="<?= htmlspecialchars($formData['username'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div>
                <label for="password">MySQL Password</label>
                <input id="password" name="password" type="password" value="<?= htmlspecialchars($formData['password'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
        </div>
        <button type="submit">Run Setup</button>
    </form>

    <div class="help">
        If setup fails, run <code>/check-db.php</code> for diagnostics and review <code>SETUP-GUIDE.md</code>.
    </div>
</div>
</body>
</html>
