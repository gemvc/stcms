<?php
/**
 * Cache Class
 * Hybrid APCu + file-based caching system
 */
class Cache {
    
    private $cacheDir = 'cache/';
    private $defaultExpiry = 3600; // 1 hour
    private $driver;
    private $apcuAvailable;
    
    public function __construct() {
        $this->driver = getenv('CACHE_DRIVER') ?: 'file';
        $this->apcuAvailable = function_exists('apcu_store') && ini_get('apc.enabled');
        // Create cache directory if it doesn't exist
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    /**
     * Generate cache key from URL and parameters
     * 
     * @param string $key Cache key
     * @return string Generated cache filename
     */
    private function getCacheFile($key) {
        return $this->cacheDir . md5($key) . '.cache';
    }
    
    /**
     * Get cached content
     * 
     * @param string $key Cache key
     * @return mixed Cached content or false if not found/expired
     */
    public function get($key) {
        if ($this->driver === 'apcu' && $this->apcuAvailable) {
            $success = false;
            $value = apcu_fetch($key, $success);
            if ($success) return $value;
            // fallback to file
        }
        $cacheFile = $this->getCacheFile($key);
        
        if (!file_exists($cacheFile)) {
            return false;
        }
        
        $data = unserialize(file_get_contents($cacheFile));
        
        // Check if cache has expired
        if (time() > $data['expiry']) {
            unlink($cacheFile);
            return false;
        }
        
        return $data['content'];
    }
    
    /**
     * Set cached content
     * 
     * @param string $key Cache key
     * @param mixed $content Content to cache
     * @param int $expiry Expiry time in seconds (optional)
     * @return bool Success status
     */
    public function set($key, $content, $expiry = null) {
        if ($expiry === null) {
            $expiry = $this->defaultExpiry;
        }
        
        if ($this->driver === 'apcu' && $this->apcuAvailable) {
            apcu_store($key, $content, $expiry);
            // Also write to file as fallback
        }
        
        $data = [
            'content' => $content,
            'expiry' => time() + $expiry
        ];
        
        $cacheFile = $this->getCacheFile($key);
        
        return file_put_contents($cacheFile, serialize($data)) !== false;
    }
    
    /**
     * Delete cached content
     * 
     * @param string $key Cache key
     * @return bool Success status
     */
    public function delete($key) {
        if ($this->driver === 'apcu' && $this->apcuAvailable) {
            apcu_delete($key);
        }
        $cacheFile = $this->getCacheFile($key);
        
        if (file_exists($cacheFile)) {
            return unlink($cacheFile);
        }
        
        return true;
    }
    
    /**
     * Clear all cache
     * 
     * @return bool Success status
     */
    public function clear() {
        if ($this->driver === 'apcu' && $this->apcuAvailable) {
            apcu_clear_cache();
        }
        $files = glob($this->cacheDir . '*.cache');
        
        foreach ($files as $file) {
            unlink($file);
        }
        
        return true;
    }
    
    /**
     * Check if cache exists and is valid
     * 
     * @param string $key Cache key
     * @return bool Whether cache exists and is valid
     */
    public function exists($key) {
        if ($this->driver === 'apcu' && $this->apcuAvailable) {
            return apcu_exists($key);
        }
        return $this->get($key) !== false;
    }
}
?> 