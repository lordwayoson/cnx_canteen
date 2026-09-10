<?php
declare(strict_types=1);

namespace Canteen\Config;

function projectRoot(): string
{
    return dirname(__DIR__);
}

function loadEnv(): void
{
    static $loaded = false;
    if ($loaded) {
        return;
    }

    $envFile = projectRoot() . '/.env';
    if (!is_file($envFile)) {
        $loaded = true;
        return;
    }

    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }

        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $key = trim($key);
        if ($key === '') {
            continue;
        }

        $value = trim($value);
        $value = trim($value, "\"'");
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
        }
        if (!array_key_exists($key, $_SERVER)) {
            $_SERVER[$key] = $value;
        }
        if (getenv($key) === false) {
            putenv($key . '=' . $value);
        }
    }

    $loaded = true;
}

function env(string $key, ?string $default = null): ?string
{
    loadEnv();

    $value = getenv($key);
    if ($value === false || $value === '') {
        return $default;
    }

    return (string) $value;
}

function normalizeBasePath(?string $path): string
{
    $path = trim((string) $path);
    if ($path === '' || $path === '/') {
        return '';
    }

    $path = parse_url($path, PHP_URL_PATH) ?: $path;
    return '/' . trim($path, '/');
}

function requestBasePath(): string
{
    $configuredPath = env('APP_BASE_PATH');
    if ($configuredPath !== null && trim($configuredPath) !== '') {
        return normalizeBasePath($configuredPath);
    }

    $configuredUrl = env('APP_BASE_URL');
    if ($configuredUrl !== null && trim($configuredUrl) !== '') {
        return normalizeBasePath($configuredUrl);
    }

    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    foreach (['/frontend', '/backend'] as $segment) {
        $pos = strpos($scriptName, $segment);
        if ($pos !== false) {
            return normalizeBasePath(substr($scriptName, 0, $pos));
        }
    }

    $dir = str_replace('\\', '/', dirname($scriptName));
    return normalizeBasePath($dir === '.' ? '' : $dir);
}

function frontendBasePath(): string
{
    return rtrim(requestBasePath() . '/frontend', '/');
}

function backendBasePath(): string
{
    return rtrim(requestBasePath() . '/backend', '/');
}

function frontendUrl(string $path = ''): string
{
    return buildUrl(frontendBasePath(), $path);
}

function backendUrl(string $path = ''): string
{
    return buildUrl(backendBasePath(), $path);
}

function apiUrl(string $path = ''): string
{
    return buildUrl(backendBasePath() . '/api', $path);
}

function assetUrl(string $path): string
{
    return frontendUrl($path);
}

function buildUrl(string $base, string $path = ''): string
{
    $base = rtrim($base, '/');
    $path = ltrim($path, '/');
    if ($path === '') {
        return $base === '' ? '/' : $base;
    }

    return ($base === '' ? '' : $base) . '/' . $path;
}

function absoluteUrl(string $path): string
{
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? '') === '443';
    $scheme = $isHttps ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    return $scheme . '://' . $host . buildUrl('', $path);
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
