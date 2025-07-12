<?php
// We are in public/, so we need to go one level up to reach the project root.
require_once __DIR__ . '/../vendor/autoload.php';

use Gemvc\Stcms\Core\Application;
use Gemvc\Stcms\Core\TemplateEngine;
use Gemvc\Stcms\Core\ApiClient;
use Symfony\Component\Dotenv\Dotenv;
use Gemvc\Stcms\Core\MultilingualRouter;

$projectRoot = dirname(__DIR__);
$dotenv = new Dotenv();
$dotenv->loadEnv($projectRoot . '/.env');

// Set default environment variables
$_ENV['APP_ENV'] = $_ENV['APP_ENV'] ?? 'production';
$_ENV['API_BASE_URL'] = $_ENV['API_BASE_URL'] ?? 'http://localhost:80';
$_ENV['DEFAULT_LANGUAGE'] = $_ENV['DEFAULT_LANGUAGE'] ?? 'en';
$_ENV['VITE_BASE_URL'] = $_ENV['VITE_BASE_URL'] ?? 'http://localhost:5173';

$apiClient = new ApiClient($_ENV['API_BASE_URL']);
$templateEngine = new TemplateEngine(
    [
        $projectRoot . '/pages',
        $projectRoot . '/templates',
        $projectRoot . '/components',
    ],
    $projectRoot,
    $_ENV['APP_ENV'],
    $_ENV['VITE_BASE_URL']
);

$supportedLanguages = array_map('basename', glob($projectRoot . '/pages/*', GLOB_ONLYDIR));
if (empty($supportedLanguages)) {
    $supportedLanguages = ['en'];
}
if (!in_array($_ENV['DEFAULT_LANGUAGE'], $supportedLanguages)) {
    $_ENV['DEFAULT_LANGUAGE'] = $supportedLanguages[0];
}

$router = new MultilingualRouter($supportedLanguages);
$app = new Application($router, $templateEngine, $apiClient);
$app->run();