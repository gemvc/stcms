<?php
namespace Gemvc\Stcms\Core;

class Application
{
    private Router $router;
    private TemplateEngine $templateEngine;
    private ApiClient $apiClient;
    private array $session;

    public function __construct(Router $router, TemplateEngine $templateEngine, ApiClient $apiClient)
    {
        $this->router = $router;
        $this->templateEngine = $templateEngine;
        $this->apiClient = $apiClient;
        $this->session = $_SESSION ?? [];
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function run(): void
    {
        try {
            $request = $this->createRequest();
            $response = $this->router->handle($request, $this);
            $this->sendResponse($response);
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

    private function createRequest(): Request
    {
        return new Request(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            $_SERVER['REQUEST_URI'] ?? '/',
            $_GET,
            $_POST,
            $_SERVER['HTTP_AUTHORIZATION'] ?? null
        );
    }

    private function sendResponse(Response $response): void
    {
        // Set headers
        foreach ($response->getHeaders() as $name => $value) {
            header("$name: $value");
        }

        // Set status code
        http_response_code($response->getStatusCode());

        // Output content
        echo $response->getContent();
    }

    private function handleError(\Exception $e): void
    {
        http_response_code(500);
        echo "Internal Server Error: " . $e->getMessage();
    }

    public function getTemplateEngine(): TemplateEngine
    {
        return $this->templateEngine;
    }

    public function getApiClient(): ApiClient
    {
        return $this->apiClient;
    }

    public function getSession(): array
    {
        return $this->session;
    }

    public function setSession(string $key, $value): void
    {
        $this->session[$key] = $value;
        $_SESSION[$key] = $value;
    }

    public function getJwt(): ?string
    {
        return $this->session['jwt'] ?? null;
    }

    public function setJwt(string $jwt): void
    {
        $this->setSession('jwt', $jwt);
    }

    public function isAuthenticated(): bool
    {
        return isset($this->session['jwt']);
    }
} 