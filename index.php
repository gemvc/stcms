<?php
/**
 * STCMS - Static Website CMS
 * Main Router Entry Point
 */

// Load Composer autoloader and .env configuration
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

// Include core classes
require_once 'code/Router.php';
require_once 'code/Cache.php';

// Initialize router
$router = new Router();

// Parse the URL and get routing information
$route = $router->parseUrl();

// Extract route components
$language = $route['language'];
$template = $route['template'];
$page = $route['page'];

// Define content and template file paths
$contentFile = "content/{$language}/{$template}/{$page}.php";
$templateFile = "templates/{$language}/{$template}.template.php";

// Check if content file exists
if (!file_exists($contentFile)) {
    // Redirect to 404 or default page
    header("HTTP/1.0 404 Not Found");
    $contentFile = "content/{$language}/{$template}/home.php";
    if (!file_exists($contentFile)) {
        die("Page not found");
    }
}

// Load content file (this will set SEO variables)
ob_start();
include $contentFile;
$content = ob_get_clean();

// Load template file
if (file_exists($templateFile)) {
    include $templateFile;
} else {
    // Fallback template
    echo "<!DOCTYPE html><html><head><title>{$page_title}</title></head><body>{$content}</body></html>";
}
?> 