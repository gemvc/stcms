<?php
namespace Gemvc\Stcms\Core;
use Gemvc\Stcms\Core\Router;
use Gemvc\Stcms\Core\Application;
use Gemvc\Stcms\Core\Request;
use Gemvc\Stcms\Core\Response;

class MultilingualRouter extends Router
{
    private array $languages;
    private string $defaultLanguage;
    
    public function __construct(array $languages)
    {
        $this->languages = $languages;
        // Read default language from environment, fallback to 'en'
        $this->defaultLanguage = $_ENV['DEFAULT_LANGUAGE'] ?? 'en';
    }

    public function handle(Request $request, Application $app): Response
    {
        $method = $request->getMethod();
        $path = $request->getPath();
        
        // Check for exact route match
        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path];
            return $handler($request, $app);
        }

        // Check for dynamic routes (simple pattern matching)
        $params = [];
        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            if ($this->matchRoute($route, $path, $params)) {
                $request->setRouteParams($params);
                return $handler($request, $app);
            }
        }

        // Handle multilingual routing
        return $this->renderMultilingualTemplate($path, $request, $app);
    }
    
    private function renderMultilingualTemplate(string $path, Request $request, Application $app): Response
    {
        $templateEngine = $app->getTemplateEngine();

        // Extract language and subpath
        $pathParts = explode('/', trim($path, '/'));
        $language = $pathParts[0] ?? $this->defaultLanguage;

        // Validate language
        if (!in_array($language, $this->languages)) {
            $language = $this->defaultLanguage;
            $subpath = trim($path, '/');
        } else {
            $subpath = implode('/', array_slice($pathParts, 1));
        }

        // Prepare base template data, always available
        $data = [
            'user' => $this->getUserData($app),
            'jwt' => $app->getJwt(),
            'path' => $path,
            'request' => $request,
            'lang' => $language,
            'get_params' => $request->getQuery(),
        ];

        // --- NEW, SIMPLIFIED ROUTING LOGIC ---
        // The entire path now maps directly to a template file.

        $templateName = $language . '/' . (empty($subpath) ? 'index' : rtrim($subpath, '/')) . '.twig';
        $indexTemplateName = $language . '/' . (empty($subpath) ? 'index' : rtrim($subpath, '/')) . '/index.twig';

        try {
            // 1. First, try to match an exact template file e.g., /sub -> sub.twig
            $content = $templateEngine->render($templateName, $data);
            return new Response($content, 200, ['Content-Type' => 'text/html']);
        } catch (\Exception $e) {
            // 2. If exact match fails, check for an index file in a directory e.g., /sub -> /sub/index.twig
            try {
                $content = $templateEngine->render($indexTemplateName, $data);
                return new Response($content, 200, ['Content-Type' => 'text/html']);
            } catch (\Exception $e2) {
                // 3. If both fail, render 404 page
                try {
                    $content = $templateEngine->render($language . '/404.twig', $data);
                    return new Response($content, 404, ['Content-Type' => 'text/html']);
                } catch (\Exception $e3) {
                    return new Response('Page not found', 404);
                }
            }
        }
    }
    
    private function getUserData(Application $app): ?array
    {
        if (!$app->isAuthenticated()) {
            return null;
        }

        try {
            $apiClient = $app->getApiClient();
            $response = $apiClient->get('/user/profile', $app->getJwt());
            return $response['data'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
