<?php
/**
 * Database connection helper using PDO.
 * Update the variables below to match your hosting environment.
 */

declare(strict_types=1);

$dbHost = 'localhost';
$dbName = 'portfolio_db';
$dbUser = 'root';
$dbPass = '';
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

    global $dbHost, $dbName, $dbUser, $dbPass, $dbCharset;

    $dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$dbCharset}";

    try {
        $pdo = new PDO($dsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        exit('Database connection failed. Please check configuration.');
    }

    return $pdo;
}
