<?php

/**
 * Load .env file manually (no putenv needed)
 */
function loadEnv(string $path): void
{
    if (!file_exists($path)) {
        throw new RuntimeException(".env file not found at: $path");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Skip comments
        if (str_starts_with(trim($line), '#')) continue;

        // Skip lines without =
        if (!str_contains($line, '=')) continue;

        [$key, $value] = explode('=', $line, 2);

        $key   = trim($key);
        $value = trim($value);

        // Strip surrounding quotes if present
        $value = trim($value, '"\'');

        // Only set if not already defined
        if (!isset($_ENV[$key])) {
            $_ENV[$key] = $value;
        }
    }
}

/**
 * Helper to get env value with optional default
 */
function env(string $key, mixed $default = null): mixed
{
    return $_ENV[$key] ?? $default;
}

/**
 * Return a singleton PDO connection
 */
function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        loadEnv(dirname(__DIR__) . '/.env');

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            env('DB_HOST', 'localhost'),
            env('DB_NAME', 'db'),
            env('DB_CHARSET', 'utf8mb4')
        );

        try {
            $pdo = new PDO($dsn, env('DB_USER'), env('DB_PASS'), [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            error_log('DB Connection failed: ' . $e->getMessage());
            die('Koneksi database gagal. Silakan hubungi administrator.');
        }
    }

    return $pdo;
}
