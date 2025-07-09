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
        $subpath = implode('/', array_slice($pathParts, 1));
        
        // Validate language - use default language if invalid
        if (!in_array($language, $this->languages)) {
            $language = $this->defaultLanguage;
            $subpath = trim($path, '/');
        }
        
        // Determine template name
        if (empty($subpath)) {
            $templateName = $language . '/index.twig';
        } else {
            $templateName = $language . '/' . $subpath . '.twig';
        }
        
        // Prepare template data
        $data = [
            'user' => $this->getUserData($app),
            'jwt' => $app->getJwt(),
            'path' => $path,
            'request' => $request,
            'lang' => $language
        ];

        try {
            $content = $templateEngine->render($templateName, $data);
            return new Response($content, 200, ['Content-Type' => 'text/html']);
        } catch (\Exception $e) {
            // Only fall back to index if the subpath is empty (i.e., root)
            if (empty($subpath)) {
                try {
                    $fallbackTemplate = $language . '/index.twig';
                    $content = $templateEngine->render($fallbackTemplate, $data);
                    return new Response($content, 200, ['Content-Type' => 'text/html']);
                } catch (\Exception $e2) {
                    // Try 404 template
                    try {
                        $content = $templateEngine->render('404.twig', $data);
                        return new Response($content, 404, ['Content-Type' => 'text/html']);
                    } catch (\Exception $e3) {
                        return new Response('Page not found', 404);
                    }
                }
            } else {
                // For missing subpages, go straight to 404
                try {
                    $content = $templateEngine->render('404.twig', $data);
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
