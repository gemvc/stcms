<?php
namespace Gemvc\Stcms\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;

class TemplateEngine
{
    private Environment $twig;
    private ?array $manifest = null;
    private bool $isDev = false;
    private string $viteBase;

    /**
     * @param string|array $templatePaths One or more paths (e.g. [pages, templates])
     * @param string $projectRoot The absolute path to the project root.
     * @param string $appEnv The application environment ('development' or 'production').
     * @param string $viteBaseUrl The base URL for the Vite dev server.
     */
    public function __construct($templatePaths, string $projectRoot, string $appEnv = 'production', string $viteBaseUrl = 'http://localhost:5173')
    {
        $this->isDev = ($appEnv === 'development');
        $this->viteBase = $viteBaseUrl;

        // In production, load the Vite manifest file to get asset paths.
        if (!$this->isDev) {
            $manifestPath = $projectRoot . '/public/assets/build/manifest.json';
            if (file_exists($manifestPath)) {
                $this->manifest = json_decode(file_get_contents($manifestPath), true);
            }
        }

        $paths = is_array($templatePaths) ? $templatePaths : [$templatePaths];
        $loader = new FilesystemLoader($paths);
        $this->twig = new Environment($loader, [
            'cache' => $this->isDev ? false : rtrim(sys_get_temp_dir(), '/') . '/stcms_cache',
            'debug' => $this->isDev,
            'auto_reload' => $this->isDev,
        ]);

        // Add debug extension
        $this->twig->addExtension(new DebugExtension());

        // Add custom functions
        $this->addCustomFunctions();
    }

    public function render(string $template, array $data = []): string
    {
        try {
            return $this->twig->render($template, $data);
        } catch (\Twig\Error\LoaderError $e) {
            throw new \Exception("Template not found: $template");
        } catch (\Twig\Error\RuntimeError $e) {
            throw new \Exception("Template error: " . $e->getMessage());
        } catch (\Twig\Error\SyntaxError $e) {
            throw new \Exception("Template syntax error: " . $e->getMessage());
        }
    }

    private function addCustomFunctions(): void
    {
        // Add asset function for loading Vite-managed static files
        $this->twig->addFunction(new \Twig\TwigFunction('asset', function (string $path) {
            if ($this->isDev) {
                // In development, assets are served by the Vite dev server.
                return $this->viteBase . '/' . ltrim($path, '/');
            }

            // In production, use the manifest file to get the final asset path.
            $manifestKey = ltrim($path, '/');
            if (isset($this->manifest[$manifestKey]['file'])) {
                return '/assets/build/' . $this->manifest[$manifestKey]['file'];
            }

            // Trigger a warning if an asset is not found in the manifest.
            trigger_error("Asset not found in manifest.json: {$path}", E_USER_WARNING);
            return '';
        }));

        // Add a function to include Vite's client and React Refresh preamble in development.
        $this->twig->addFunction(new \Twig\TwigFunction('vite_react_refresh', function () {
            if (!$this->isDev) {
                return '';
            }
            return <<<HTML
                <script type="module" src="{$this->viteBase}/@vite/client"></script>
            HTML;
        }, ['is_safe' => ['html']]));


        // Add route function for generating URLs
        $this->twig->addFunction(new \Twig\TwigFunction('route', function (string $name, array $params = []) {
            // Simple route generation - can be enhanced later
            return '/' . $name;
        }));

        // Add json_encode function
        $this->twig->addFunction(new \Twig\TwigFunction('json_encode', function ($data) {
            return json_encode($data);
        }));

        // Add is_authenticated function
        $this->twig->addFunction(new \Twig\TwigFunction('is_authenticated', function () {
            return isset($_SESSION['jwt']);
        }));
    }

    public function getTwig(): Environment
    {
        return $this->twig;
    }
} 