<?php
namespace Gemvc\Stcms\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;

class TemplateEngine
{
    private Environment $twig;

    /**
     * @param string|array $templatePaths One or more paths (e.g. [pages, templates])
     */
    public function __construct($templatePaths)
    {
        // Accept either a string or array of paths
        $paths = is_array($templatePaths) ? $templatePaths : [$templatePaths];
        $loader = new FilesystemLoader($paths);
        $this->twig = new Environment($loader, [
            'cache' => false, // Disable cache in development
            'debug' => true,
            'auto_reload' => true,
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
        // Add asset function for loading static files
        $this->twig->addFunction(new \Twig\TwigFunction('asset', function (string $path) {
            return '/assets/' . ltrim($path, '/');
        }));

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