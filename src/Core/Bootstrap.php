<?php
namespace Gemvc\Stcms\Core;

use Symfony\Component\Dotenv\Dotenv;
use Gemvc\Stcms\Core\TemplateEngine;
use Gemvc\Stcms\Core\MultilingualRouter;
use Gemvc\Stcms\Core\Application;

/**
 * Bootstrap class for STCMS application initialization
 * Handles environment setup, .env loading, and configuration
 */
class Bootstrap
{
    private string $projectRoot;
    private array $config;

    public function __construct(string $projectRoot)
    {
        $this->projectRoot = $projectRoot;
        $this->config = [];
        
        // Initialize automatically on construction
        $this->initialize();
    }

    /**
     * Initialize the application environment
     * Loads .env file and sets up all required environment variables
     */
    public function initialize(): void
    {
        $this->loadEnvironmentFile();
        $this->setDefaultEnvironmentVariables();
        $this->validateConfiguration();
        $this->logInitialization();
        
        // Handle root URL redirect to default language
        $this->handleRootRedirect();
    }

    /**
     * Load .env file if it exists
     */
    private function loadEnvironmentFile(): void
    {
        $envPath = $this->projectRoot . '/.env';
        
        if (file_exists($envPath)) {
            $dotenv = new Dotenv();
            $dotenv->loadEnv($envPath);
            error_log('STCMS: Bootstrap - .env file loaded from: ' . $envPath);
        } else {
            error_log('STCMS: Bootstrap - No .env file found at: ' . $envPath);
        }
    }

    /**
     * Set default environment variables with fallbacks
     */
    private function setDefaultEnvironmentVariables(): void
    {
        // Critical: Environment mode (development/production)
        $_ENV['APP_ENV'] = $_ENV['APP_ENV'] ?? 'production';
        
        // API configuration
        $_ENV['API_BASE_URL'] = $_ENV['API_BASE_URL'] ?? 'http://localhost:80';
        
        // Vite configuration
        $_ENV['VITE_BASE_URL'] = $_ENV['VITE_BASE_URL'] ?? 'http://localhost:8000';
        
        // Cache configuration
        $_ENV['CACHE_DRIVER'] = $_ENV['CACHE_DRIVER'] ?? 'file';
        $_ENV['CACHE_TTL'] = $_ENV['CACHE_TTL'] ?? '3600';
        
        // Debug configuration
        $_ENV['DEBUG'] = $_ENV['DEBUG'] ?? 'false';
        
        // Security configuration
        $_ENV['SECURE_HEADERS'] = $_ENV['SECURE_HEADERS'] ?? 'true';
        
        // Auto-detect default language from pages directory
        $languages = $this->getSupportedLanguages();
        // Only set DEFAULT_LANGUAGE from .env if it exists in the languages list
        $defaultLang = $_ENV['DEFAULT_LANGUAGE'] ?? 'en';
        if (!in_array($defaultLang, $languages)) {
            $_ENV['DEFAULT_LANGUAGE'] = $languages[0] ?? 'en';
        } else {
            $_ENV['DEFAULT_LANGUAGE'] = $defaultLang;
        }
        
        // Store configuration for easy access
        $this->config = [
            'APP_ENV' => $_ENV['APP_ENV'],
            'API_BASE_URL' => $_ENV['API_BASE_URL'],
            'DEFAULT_LANGUAGE' => $_ENV['DEFAULT_LANGUAGE'],
            'VITE_BASE_URL' => $_ENV['VITE_BASE_URL'],
            'CACHE_DRIVER' => $_ENV['CACHE_DRIVER'],
            'CACHE_TTL' => $_ENV['CACHE_TTL'],
            'DEBUG' => $_ENV['DEBUG'],
            'SECURE_HEADERS' => $_ENV['SECURE_HEADERS']
        ];
    }

    /**
     * Validate critical configuration
     */
    private function validateConfiguration(): void
    {
        $requiredVars = ['APP_ENV'];
        
        foreach ($requiredVars as $var) {
            if (empty($_ENV[$var])) {
                throw new \RuntimeException("STCMS: Required environment variable '{$var}' is not set");
            }
        }

        // Validate APP_ENV value
        if (!in_array($_ENV['APP_ENV'], ['development', 'production'])) {
            throw new \RuntimeException("STCMS: Invalid APP_ENV value '{$_ENV['APP_ENV']}'. Must be 'development' or 'production'");
        }
    }

    /**
     * Log initialization details
     */
    private function logInitialization(): void
    {
        error_log('STCMS: Bootstrap - Initialization complete');
        error_log('STCMS: Bootstrap - APP_ENV: ' . $_ENV['APP_ENV']);
        error_log('STCMS: Bootstrap - DEFAULT_LANGUAGE: ' . $_ENV['DEFAULT_LANGUAGE']);
        error_log('STCMS: Bootstrap - VITE_BASE_URL: ' . $_ENV['VITE_BASE_URL']);
        error_log('STCMS: Bootstrap - API_BASE_URL: ' . $_ENV['API_BASE_URL']);
    }

    /**
     * Get configuration array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Get specific configuration value
     */
    public function getConfigValue(string $key): ?string
    {
        return $this->config[$key] ?? null;
    }

    /**
     * Check if running in development mode
     */
    public function isDevelopment(): bool
    {
        return $_ENV['APP_ENV'] === 'development';
    }

    /**
     * Check if running in production mode
     */
    public function isProduction(): bool
    {
        return $_ENV['APP_ENV'] === 'production';
    }

    /**
     * Get project root path
     */
    public function getProjectRoot(): string
    {
        return $this->projectRoot;
    }

    /**
     * Get supported languages from pages directory
     */
    public function getSupportedLanguages(): array
    {
        $languages = array_map('basename', glob($this->projectRoot . '/pages/*', GLOB_ONLYDIR));
        
        if (empty($languages)) {
            $languages = ['en'];
        }

        // Ensure default language is in supported languages
        $defaultLang = $_ENV['DEFAULT_LANGUAGE'] ?? 'en';
        if (!in_array($defaultLang, $languages)) {
            $_ENV['DEFAULT_LANGUAGE'] = $languages[0] ?? 'en';
            error_log("STCMS: Bootstrap - Default language '{$defaultLang}' not found in supported languages. Using '{$languages[0]}'");
        }

        return $languages;
    }

    /**
     * Handle root URL redirect to default language
     */
    public function handleRootRedirect(): void
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        
        if ($requestUri === '/' || $requestUri === '') {
            $defaultLang = $_ENV['DEFAULT_LANGUAGE'] ?? 'en';
            header("Location: /{$defaultLang}/");
            exit;
        }
    }

    /**
     * Run the complete application
     * Initializes all services and starts the application
     */
    public function runApp(): void
    {
        // Get configuration
        $config = $this->getConfig();
        
        // Initialize TemplateEngine
        $templateEngine = new TemplateEngine(
            [
                $this->projectRoot . '/pages',
                $this->projectRoot . '/templates',
                $this->projectRoot . '/components',
            ],
            $this->projectRoot,
            $config['APP_ENV'],
            $config['VITE_BASE_URL']
        );
        
        // Initialize Router
        $router = new MultilingualRouter($this->getSupportedLanguages());
        
        // Initialize and run Application
        $app = new Application($router, $templateEngine);
        $app->run();
    }
}
