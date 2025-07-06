<?php
/**
 * Router Class
 * Handles URL parsing and routing logic
 */
class Router {
    
    private $supportedLanguages = ['en', 'de'];
    private $supportedTemplates = ['docs', 'landing'];
    
    /**
     * Parse the current URL and extract routing information
     * 
     * @return array Array containing language, template, and page
     */
    public function parseUrl() {
        // Get the request URI
        $requestUri = $_SERVER['REQUEST_URI'];
        
        // Remove query string if present
        $requestUri = strtok($requestUri, '?');
        
        // Remove leading and trailing slashes
        $requestUri = trim($requestUri, '/');
        
        // Split the URL into segments
        $segments = explode('/', $requestUri);
        
        // Default values
        $language = 'en';
        $template = 'landing';
        $page = 'home';
        
        // Parse language (first segment)
        if (!empty($segments[0]) && in_array($segments[0], $this->supportedLanguages)) {
            $language = $segments[0];
            array_shift($segments);
        }
        
        // Parse template (second segment)
        if (!empty($segments[0]) && in_array($segments[0], $this->supportedTemplates)) {
            $template = $segments[0];
            array_shift($segments);
        }
        
        // Parse page (remaining segments)
        if (!empty($segments[0])) {
            $page = $segments[0];
        }
        
        return [
            'language' => $language,
            'template' => $template,
            'page' => $page
        ];
    }
    
    /**
     * Get supported languages
     * 
     * @return array Array of supported language codes
     */
    public function getSupportedLanguages() {
        return $this->supportedLanguages;
    }
    
    /**
     * Get supported templates
     * 
     * @return array Array of supported template types
     */
    public function getSupportedTemplates() {
        return $this->supportedTemplates;
    }
    
    /**
     * Build URL for a specific page
     * 
     * @param string $language Language code
     * @param string $template Template type
     * @param string $page Page name
     * @return string Generated URL
     */
    public function buildUrl($language = 'en', $template = 'landing', $page = 'home') {
        $url = "/{$language}";
        
        if ($template !== 'landing') {
            $url .= "/{$template}";
        }
        
        if ($page !== 'home') {
            $url .= "/{$page}";
        }
        
        return $url;
    }
}
?> 