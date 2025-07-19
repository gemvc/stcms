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
        
        return $this->twig->render($template, $data);
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