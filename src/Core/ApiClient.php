<?php
namespace Gemvc\Stcms\Core;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ApiClient
{
    private Client $client;
    private string $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
            'headers' => [
                'User-Agent' => 'STCMS/1.0',
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    public function get(string $endpoint, ?string $jwt = null): array
    {
        $headers = [];
        if ($jwt) {
            $headers['Authorization'] = "Bearer $jwt";
        }

        try {
            $response = $this->client->get($endpoint, ['headers' => $headers]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            throw new \Exception("API request failed: " . $e->getMessage());
        }
    }

    public function post(string $endpoint, array $data = [], ?string $jwt = null): array
    {
        $headers = [];
        if ($jwt) {
            $headers['Authorization'] = "Bearer $jwt";
        }

        try {
            $response = $this->client->post($endpoint, [
                'headers' => $headers,
                'json' => $data
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            throw new \Exception("API request failed: " . $e->getMessage());
        }
    }

    public function put(string $endpoint, array $data = [], ?string $jwt = null): array
    {
        $headers = [];
        if ($jwt) {
            $headers['Authorization'] = "Bearer $jwt";
        }

        try {
            $response = $this->client->put($endpoint, [
                'headers' => $headers,
                'json' => $data
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            throw new \Exception("API request failed: " . $e->getMessage());
        }
    }

    public function delete(string $endpoint, ?string $jwt = null): array
    {
        $headers = [];
        if ($jwt) {
            $headers['Authorization'] = "Bearer $jwt";
        }

        try {
            $response = $this->client->delete($endpoint, ['headers' => $headers]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            throw new \Exception("API request failed: " . $e->getMessage());
        }
    }

    public function authenticate(string $email, string $password): ?string
    {
        try {
            $response = $this->post('/auth/login', [
                'email' => $email,
                'password' => $password
            ]);
            
            return $response['token'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
} 