<?php
/**
 * BreakFree - Point d'entrée principal
 * Toutes les requêtes passent par ce fichier
 */

// ─── Error Reporting ───
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// ─── Paths ───
define('ROOT_PATH',        dirname(__DIR__));
define('CONFIG_PATH',      ROOT_PATH . '/config');
define('CONTROLLERS_PATH', ROOT_PATH . '/controllers');
define('MODELS_PATH',      ROOT_PATH . '/models');
define('VIEWS_PATH',       ROOT_PATH . '/views');
define('PUBLIC_PATH',      __DIR__);

// ─── Load Environment ───
require_once CONFIG_PATH . '/env.php';
loadEnv(ROOT_PATH . '/.env');

// ─── Base URL ───
$isProduction = env('APP_ENV', 'development') === 'production';
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$currentBaseUrl = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost:8000');
define('BASE_URL', rtrim($isProduction ? env('APP_URL', $currentBaseUrl) : $currentBaseUrl, '/'));

// ─── Helpers ───
require_once CONFIG_PATH . '/helpers.php';

// ─── Security Headers ───
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

if (env('APP_ENV') === 'production') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// ─── Session ───
session_start([
    'cookie_httponly' => true,
    'cookie_secure'   => env('APP_ENV') === 'production',
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true,
    'gc_maxlifetime'  => 3600,
]);

// ─── Router ───
require_once CONFIG_PATH . '/Router.php';

$router = new Router();
require_once ROOT_PATH . '/routes/web.php';

// ─── Dispatch ───
try {
    $router->dispatch();
} catch (Throwable $e) {
    error_log("BreakFree Error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());

    if (env('APP_ENV') === 'development') {
        http_response_code(500);
        echo "<h1>Erreur serveur</h1>";
        echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        http_response_code(500);
        require_once VIEWS_PATH . '/layouts/500.php';
    }
}
