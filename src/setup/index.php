<?php
// We are in public/, so we need to go one level up to reach the project root.
require_once __DIR__ . '/../vendor/autoload.php';

use Gemvc\Stcms\Core\Bootstrap;

$projectRoot = dirname(__DIR__);

// Initialize and run application using Bootstrap
$bootstrap = new Bootstrap($projectRoot);
$bootstrap->runApp();