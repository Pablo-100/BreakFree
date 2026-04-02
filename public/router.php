<?php
/**
 * Router script for PHP built-in development server.
 * Usage: php -S localhost:8080 -t public public/router.php
 *
 * .htaccess does NOT work with PHP's built-in server, so we need this.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// If the requested file exists on disk (CSS, JS, images…), serve it directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Route everything else through index.php
require __DIR__ . '/index.php';
