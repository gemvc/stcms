<?php

namespace Gemvc\Stcms\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;
use Twig\TwigFunction;

class TemplateEngine
{
    private Environment $twig;
    private ?array $manifest = null;
    private bool $isDev;
    private string $viteBaseUrl;
    private string $entrypoint;

    public function __construct(
        array $templatePaths,
        string $projectRoot,
        string $appEnv = 'production',
        string $viteBaseUrl = 'http://localhost:5173',
        string $entrypoint = 'assets/js/app.jsx'
    ) {
        error_log('STCMS: TemplateEngine constructor called with appEnv: ' . $appEnv);
        $this->isDev = ($appEnv === 'development');
        error_log('STCMS: isDev set to: ' . ($this->isDev ? 'true' : 'false'));
        $this->viteBaseUrl = rtrim($viteBaseUrl, '/');
        $this->entrypoint = $entrypoint;

        $loader = new FilesystemLoader($templatePaths);
        $this->twig = new Environment($loader, [
            'debug' => $this->isDev,
            'auto_reload' => $this->isDev,
            'cache' => $this->isDev ? false : rtrim(sys_get_temp_dir(), '/') . '/stcms_cache',
        ]);

        if (!$this->isDev) {
            error_log('STCMS: Not in dev mode, trying to load manifest');
            // Try the standard Vite manifest location first
            $manifestPath = $projectRoot . '/public/assets/build/.vite/manifest.json';
            error_log('STCMS: Trying manifest path: ' . $manifestPath);
            if (!file_exists($manifestPath)) {
                // Fallback to the old location
                $manifestPath = $projectRoot . '/public/assets/build/manifest.json';
                error_log('STCMS: Fallback manifest path: ' . $manifestPath);
            }
            if (file_exists($manifestPath)) {
                $this->manifest = json_decode(file_get_contents($manifestPath), true);
                error_log('STCMS: Manifest loaded from ' . $manifestPath);
                error_log('STCMS: Manifest content: ' . json_encode($this->manifest));
            } else {
                error_log('STCMS: Manifest not found at ' . $manifestPath);
            }
        } else {
            error_log('STCMS: In dev mode, skipping manifest loading');
        }

        $this->addCustomFunctions();
    }

    public function render(string $template, array $data = []): string
    {
        // Add global variables available in all templates
        $this->twig->addGlobal('app_env', $this->isDev ? 'development' : 'production');
        
        try {
            error_log('STCMS: Attempting to render template: ' . $template);
            $result = $this->twig->render($template, $data);
            error_log('STCMS: Template rendered successfully: ' . $template);
            return $result;
        } catch (\Twig\Error\LoaderError $e) {
            error_log('STCMS: Template loader error for ' . $template . ': ' . $e->getMessage());
            error_log('STCMS: Available template paths: ' . implode(', ', $this->twig->getLoader()->getPaths()));
            
            if ($this->isDev) {
                return $this->renderErrorPage('Template Not Found', $e->getMessage(), $template, $e->getLine(), $e->getFile());
            }
            throw $e;
        } catch (\Twig\Error\SyntaxError $e) {
            error_log('STCMS: Template syntax error in ' . $template . ': ' . $e->getMessage());
            error_log('STCMS: Error line: ' . $e->getLine());
            error_log('STCMS: Error file: ' . $e->getFile());
            
            if ($this->isDev) {
                return $this->renderErrorPage('Template Syntax Error', $e->getMessage(), $template, $e->getLine(), $e->getFile());
            }
            throw $e;
        } catch (\Twig\Error\RuntimeError $e) {
            error_log('STCMS: Template runtime error in ' . $template . ': ' . $e->getMessage());
            error_log('STCMS: Error line: ' . $e->getLine());
            error_log('STCMS: Error file: ' . $e->getFile());
            
            if ($this->isDev) {
                return $this->renderErrorPage('Template Runtime Error', $e->getMessage(), $template, $e->getLine(), $e->getFile());
            }
            throw $e;
        } catch (\Exception $e) {
            error_log('STCMS: Unexpected error rendering template ' . $template . ': ' . $e->getMessage());
            
            if ($this->isDev) {
                return $this->renderErrorPage('Unexpected Error', $e->getMessage(), $template, $e->getLine(), $e->getFile());
            }
            throw $e;
        }
    }

    private function renderErrorPage(string $errorType, string $message, string $template, int $line, string $file): string
    {
        $availablePaths = implode(', ', $this->twig->getLoader()->getPaths());
        
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STCMS Template Error - {$errorType}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .error-code { font-family: 'Courier New', monospace; background: #1f2937; color: #f3f4f6; }
        .error-line { background: #dc2626; color: white; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="bg-red-600 text-white p-6 rounded-t-lg">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-2xl"></i>
                    <div>
                        <h1 class="text-2xl font-bold">STCMS Template Error</h1>
                        <p class="text-red-100">{$errorType}</p>
                    </div>
                </div>
            </div>

            <!-- Error Details -->
            <div class="bg-white p-6 border-l-4 border-red-600">
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Error Message -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-3">
                            <i class="fas fa-bug text-red-500 mr-2"></i>Error Message
                        </h2>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <p class="text-red-800 font-mono text-sm">{$message}</p>
                        </div>
                    </div>

                    <!-- Template Info -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-3">
                            <i class="fas fa-file-code text-blue-500 mr-2"></i>Template Information
                        </h2>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 space-y-2">
                            <p><strong>Template:</strong> <span class="font-mono text-sm">{$template}</span></p>
                            <p><strong>File:</strong> <span class="font-mono text-sm">{$file}</span></p>
                            <p><strong>Line:</strong> <span class="font-mono text-sm">{$line}</span></p>
                        </div>
                    </div>
                </div>

                <!-- Available Paths -->
                <div class="mt-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">
                        <i class="fas fa-folder-open text-green-500 mr-2"></i>Available Template Paths
                    </h2>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-green-800 font-mono text-sm">{$availablePaths}</p>
                    </div>
                </div>

                <!-- Common Solutions -->
                <div class="mt-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">
                        <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>Common Solutions
                    </h2>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <ul class="text-yellow-800 space-y-2 text-sm">
                            <li><i class="fas fa-check mr-2"></i>Check if the template file exists in the correct path</li>
                            <li><i class="fas fa-check mr-2"></i>Verify the template syntax (missing {% endblock %}, etc.)</li>
                            <li><i class="fas fa-check mr-2"></i>Ensure component paths are correct (e.g., "card_icon.twig")</li>
                            <li><i class="fas fa-check mr-2"></i>Check for typos in template names and paths</li>
                            <li><i class="fas fa-check mr-2"></i>Verify that all required blocks are defined</li>
                        </ul>
                    </div>
                </div>

                <!-- Development Mode Notice -->
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        <p class="text-blue-800 text-sm">
                            <strong>Development Mode:</strong> This detailed error page is only shown in development mode. 
                            In production, users will see a generic error page.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-800 text-white p-4 rounded-b-lg text-center">
                <p class="text-gray-300 text-sm">
                    <i class="fas fa-code mr-2"></i>STCMS Template Engine - Debug Mode
                </p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function addCustomFunctions(): void
    {
        $this->twig->addExtension(new DebugExtension());

        // This function injects the correct script and link tags for Vite.
        $this->twig->addFunction(new TwigFunction('vite_assets', function (): string {
            error_log('STCMS: vite_assets called, isDev: ' . ($this->isDev ? 'true' : 'false'));
            error_log('STCMS: entrypoint: ' . $this->entrypoint);
            error_log('STCMS: manifest exists: ' . ($this->manifest ? 'true' : 'false'));
            
            if ($this->isDev) {
                // In development, point to the Vite dev server.
                error_log('STCMS: Using development mode');
                return <<<HTML
                    <script type="module" src="{$this->viteBaseUrl}/@vite/client"></script>
                    <script type="module" src="{$this->viteBaseUrl}/{$this->entrypoint}"></script>
                HTML;
            }

            // In production, use the manifest.json to get hashed filenames.
            if (!$this->manifest) {
                error_log('STCMS: Manifest not found, falling back to development mode');
                // Fallback to development mode if manifest is not found
                return <<<HTML
                    <script type="module" src="{$this->viteBaseUrl}/@vite/client"></script>
                    <script type="module" src="{$this->viteBaseUrl}/{$this->entrypoint}"></script>
                HTML;
            }

            $entryData = $this->manifest[$this->entrypoint] ?? null;
            error_log('STCMS: Entry data: ' . json_encode($entryData));
            
            if (!$entryData) {
                error_log('STCMS: Entrypoint not found in manifest, falling back to development mode');
                // Fallback to development mode if entrypoint is not found
                return <<<HTML
                    <script type="module" src="{$this->viteBaseUrl}/@vite/client"></script>
                    <script type="module" src="{$this->viteBaseUrl}/{$this->entrypoint}"></script>
                HTML;
            }

            error_log('STCMS: Using production mode with manifest');
            $html = '';
            // Add the main JS file
            $html .= '<script type="module" src="/assets/build/' . $entryData['file'] . '"></script>';
            
            // Add any associated CSS files
            if (!empty($entryData['css'])) {
                foreach ($entryData['css'] as $cssFile) {
                    $html .= '<link rel="stylesheet" href="/assets/build/' . $cssFile . '">';
                }
            }
            
            return $html;
        }, ['is_safe' => ['html']]));

        // Deprecated, but kept for potential legacy use. `vite_assets` is preferred.
        $this->twig->addFunction(new TwigFunction('vite_react_refresh', function (): string {
            if ($this->isDev) {
                 return '<script type="module" src="' . $this->viteBaseUrl . '/@vite/client"></script>';
            }
            return '';
        }, ['is_safe' => ['html']]));
    }
} 
