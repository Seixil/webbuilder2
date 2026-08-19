<?php
/**
 * Základná Trieda Pre Kontroléry
 */

namespace App\Core;

class BaseController {
    
    /**
     * Vyrenderuj HTML šablónu
     */
    public function render($view, $data = []) {
        extract($data);
        
        $view_path = PUBLIC_PATH . '/views/' . $view . '.php';
        
        if (!file_exists($view_path)) {
            die("❌ View not found: {$view_path}");
        }
        
        ob_start();
        include $view_path;
        return ob_get_clean();
    }
    
    /**
     * Vráť JSON odpoveď
     */
    public function json($data, $status_code = 200) {
        http_response_code($status_code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Presmeruj na inú stránku
     */
    public function redirect($url, $status_code = 302) {
        http_response_code($status_code);
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Vráť chybu JSON
     */
    public function error($message, $status_code = 400) {
        $this->json([
            'success' => false,
            'error' => $message,
            'status' => $status_code
        ], $status_code);
    }
    
    /**
     * Vráť úspešnú odpoveď
     */
    public function success($data = [], $message = 'Success', $status_code = 200) {
        $this->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'status' => $status_code
        ], $status_code);
    }
}
