<?php
declare(strict_types=1);

namespace Canteen\Config;

use Dotenv\Dotenv;
use PDO;
use PDOException;
use RuntimeException;

/**
 * Attempt to load Composer's autoloader if available. We support both the
 * backend/ vendor directory (expected when running `composer install` from the
 * backend folder) and a repository root level vendor directory. When neither
 * is present we fall back to a lightweight .env loader so the APIs can still
 * operate with manually provided environment variables instead of fatally
 * erroring.
 */
$autoloadLoaded = false;
$autoloadCandidates = [
    __DIR__ . '/../vendor/autoload.php',
    dirname(__DIR__, 2) . '/vendor/autoload.php',
];

foreach ($autoloadCandidates as $autoloadCandidate) {
    if (is_file($autoloadCandidate)) {
        require_once $autoloadCandidate;
        $autoloadLoaded = true;
        break;
    }
}

$root = dirname(__DIR__, 2);

if ($autoloadLoaded && class_exists(Dotenv::class, false)) {
    $dotenv = Dotenv::createImmutable($root);
    $dotenv->safeLoad();
} elseif (is_file($root . '/.env')) {
    // Minimal .env loader to keep the application functional without Composer.
    $lines = file($root . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $key = trim($key);
        if ($key === '') {
            continue;
        }
        $value = trim($value);
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
        }
        if (!array_key_exists($key, $_SERVER)) {
            $_SERVER[$key] = $value;
        }
        putenv($key . '=' . $value);
    }
}

date_default_timezone_set(getenv('TZ') ?: 'Africa/Accra');

/**
 * @return PDO
 */
function getCanteenPdo(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', getenv('DB_HOST') ?: '127.0.0.1', getenv('DB_PORT') ?: '3306', getenv('DB_DATABASE') ?: 'canteen_db');
    try {
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
        $pdo = new PDO($dsn, getenv('DB_USERNAME') ?: 'root', getenv('DB_PASSWORD') ?: 'admin', [
=======
        $pdo = new PDO($dsn, getenv('DB_USERNAME') ?: 'root', getenv('DB_PASSWORD') ?: '', [
>>>>>>> theirs
=======
        $pdo = new PDO($dsn, getenv('DB_USERNAME') ?: 'root', getenv('DB_PASSWORD') ?: '', [
>>>>>>> theirs
=======
        $pdo = new PDO($dsn, getenv('DB_USERNAME') ?: 'root', getenv('DB_PASSWORD') ?: '', [
>>>>>>> theirs
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $exception) {
        throw new RuntimeException('Database connection failed: ' . $exception->getMessage());
    }
    return $pdo;
}

/**
 * @return PDO
 */
function getIngressPdo(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', getenv('INGRESS_DB_HOST') ?: '127.0.0.1', getenv('INGRESS_DB_PORT') ?: '3306', getenv('INGRESS_DB_DATABASE') ?: 'ingress');
    try {
        $pdo = new PDO($dsn, getenv('INGRESS_DB_USERNAME') ?: 'ingress', getenv('INGRESS_DB_PASSWORD') ?: 'ingress', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $exception) {
        throw new RuntimeException('Ingress database connection failed: ' . $exception->getMessage());
    }
    return $pdo;
<<<<<<< ours
<<<<<<< ours
<<<<<<< ours
}
=======
}
>>>>>>> theirs
=======
}
>>>>>>> theirs
=======
}
>>>>>>> theirs
