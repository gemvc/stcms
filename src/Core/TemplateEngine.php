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
        $this->isDev = ($appEnv === 'development');
        $this->viteBaseUrl = rtrim($viteBaseUrl, '/');
        $this->entrypoint = $entrypoint;

        $loader = new FilesystemLoader($templatePaths);
        $this->twig = new Environment($loader, [
            'debug' => $this->isDev,
            'auto_reload' => $this->isDev,
            'cache' => $this->isDev ? false : rtrim(sys_get_temp_dir(), '/') . '/stcms_cache',
        ]);

        if (!$this->isDev) {
            $manifestPath = $projectRoot . '/public/assets/build/manifest.json';
            if (file_exists($manifestPath)) {
                $this->manifest = json_decode(file_get_contents($manifestPath), true);
            }
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
            if ($this->isDev) {
                // In development, point to the Vite dev server.
                return <<<HTML
                    <script type="module" src="{$this->viteBaseUrl}/@vite/client"></script>
                    <script type="module" src="{$this->viteBaseUrl}/{$this->entrypoint}"></script>
                HTML;
            }

            // In production, use the manifest.json to get hashed filenames.
            if (!$this->manifest) {
                // Optionally return an error or a placeholder
                return '<!-- Vite manifest not found -->';
            }

            $entryData = $this->manifest[$this->entrypoint] ?? null;
            if (!$entryData) {
                 return '<!-- Vite entrypoint not found in manifest -->';
            }

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