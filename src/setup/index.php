<?php
require_once __DIR__ . '/vendor/autoload.php';

use Gemvc\Stcms\Core\Application;
use Gemvc\Stcms\Core\Router;
use Gemvc\Stcms\Core\TemplateEngine;
use Gemvc\Stcms\Core\ApiClient;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Initialize core components
$apiClient = new ApiClient($_ENV['API_BASE_URL']);
$templateEngine = new TemplateEngine([
    __DIR__ . '/pages',
    __DIR__ . '/templates',
]);
$router = new Router();

// Create and run the application
$app = new Application($router, $templateEngine, $apiClient);
$app->run(); 