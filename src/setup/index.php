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
]);
$router = new MultilingualRouter(['en','de']);

// Create and run the application
$app = new Application($router, $templateEngine, $apiClient);
$app->run(); 