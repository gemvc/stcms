<?php
namespace Gemvc\Stcms\Core;

class Router
{
    protected array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$path] = $handler;
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

        // Default route - render template based on path
        return $this->renderTemplate($path, $request, $app);
    }

    protected function matchRoute(string $route, string $path, array &$params): bool
    {
        // Simple pattern matching for routes like /user/{id}
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route);
        $pattern = '#^' . $pattern . '$#';
        
        if (preg_match($pattern, $path, $matches)) {
            $params = [];
            preg_match_all('/\{([^}]+)\}/', $route, $paramNames);
            
            for ($i = 1; $i < count($matches); $i++) {
                $params[$paramNames[1][$i - 1]] = $matches[$i];
            }
            
            return true;
        }
        
        return false;
    }

    private function renderTemplate(string $path, Request $request, Application $app): Response
    {
        $templateEngine = $app->getTemplateEngine();
        
        // Convert path to template name
        $templateName = $this->pathToTemplate($path);
        
        // Prepare template data
        $data = [
            'user' => $this->getUserData($app),
            'jwt' => $app->getJwt(),
            'path' => $path,
            'request' => $request
        ];

        try {
            $content = $templateEngine->render($templateName, $data);
            return new Response($content, 200, ['Content-Type' => 'text/html']);
        } catch (\Exception $e) {
            // Try fallback template
            try {
                $content = $templateEngine->render('404.twig', $data);
                return new Response($content, 404, ['Content-Type' => 'text/html']);
            } catch (\Exception $e2) {
                return new Response('Page not found', 404);
            }
        }
    }

    private function pathToTemplate(string $path): string
    {
        // Convert /about to about.twig
        $template = trim($path, '/');
        if (empty($template)) {
            $template = 'index';
        }
        return $template . '.twig';
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