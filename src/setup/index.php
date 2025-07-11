<?php
require_once __DIR__ . '/vendor/autoload.php';

use Gemvc\Stcms\Core\Application;
use Gemvc\Stcms\Core\TemplateEngine;
use Gemvc\Stcms\Core\ApiClient;
use Symfony\Component\Dotenv\Dotenv;
use Gemvc\Stcms\Core\MultilingualRouter;

// Load environment variables
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/.env');
// Set default environment variables if not present
if (!isset($_ENV['API_BASE_URL'])) {
    $_ENV['API_BASE_URL'] = 'http://localhost:80';
}
if (!isset($_ENV['DEFAULT_LANGUAGE'])) {
    $_ENV['DEFAULT_LANGUAGE'] = 'en';
}
// Initialize core components
$apiClient = new ApiClient($_ENV['API_BASE_URL']);
$templateEngine = new TemplateEngine([
    __DIR__ . '/pages',
    __DIR__ . '/templates',
    __DIR__ . '/components',
]);

// Dynamically find supported languages by scanning the 'pages' directory
$supportedLanguages = [];
$pagesDir = __DIR__ . '/pages';
if (is_dir($pagesDir)) {
    $languageDirs = glob($pagesDir . '/*', GLOB_ONLYDIR);
    foreach ($languageDirs as $langDir) {
        $supportedLanguages[] = basename($langDir);
    }
}

// If for some reason no language directories are found, default to 'en'
if (empty($supportedLanguages)) {
    $supportedLanguages = ['en'];
}

// Validate that the DEFAULT_LANGUAGE from .env exists.
// If not, fall back to the first available language to prevent errors.
if (!in_array($_ENV['DEFAULT_LANGUAGE'], $supportedLanguages)) {
    $_ENV['DEFAULT_LANGUAGE'] = $supportedLanguages[0];
}

$router = new MultilingualRouter($supportedLanguages);

// Create and run the application
$app = new Application($router, $templateEngine, $apiClient);
$app->run(); 
