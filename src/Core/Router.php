<?php
/**
 * Jednoduchý Router
 */

namespace App\Core;

class Router {
    private $routes = [];
    private $not_found_callback = null;
    
    /**
     * Zaregistruj routu
     */
    public function add($method, $path, $callback) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'callback' => $callback
        ];
    }
    
    /**
     * Rýchle metódy pre GET, POST, atď.
     */
    public function get($path, $callback) {
        $this->add('GET', $path, $callback);
    }
    
    public function post($path, $callback) {
        $this->add('POST', $path, $callback);
    }
    
    public function put($path, $callback) {
        $this->add('PUT', $path, $callback);
    }
    
    public function delete($path, $callback) {
        $this->add('DELETE', $path, $callback);
    }
    
    /**
     * Handler pre nenajdené routy
     */
    public function notFound($callback) {
        $this->not_found_callback = $callback;
    }
    
    /**
     * Spracuj request
     */
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Odstráň base path z URL
        $base_path = dirname($_SERVER['SCRIPT_NAME']);
        if ($base_path !== '/') {
            $path = str_replace($base_path, '', $path);
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $path)) {
                return call_user_func($route['callback']);
            }
        }
        
        // Nenašli sme routu
        if ($this->not_found_callback) {
            return call_user_func($this->not_found_callback);
        }
        
        http_response_code(404);
        die('❌ 404 - Page not found');
    }
    
    /**
     * Overená zhoda cesty s routou
     */
    private function matchPath($pattern, $path) {
        // Priama zhoda
        if ($pattern === $path) {
            return true;
        }
        
        // Parametrické routy (napr. /page/{id})
        $pattern = preg_replace('/\{[a-zA-Z_][a-zA-Z0-9_]*\}/', '([^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';
        
        return (bool) preg_match($pattern, $path);
    }
}
