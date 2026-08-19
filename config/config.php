<?php
/**
 * Globálne Konfigurácia Webbuildera
 */

// ============================================
// DATABÁZA
// ============================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'webbuilder_db');
define('DB_CHARSET', 'utf8mb4');

// ============================================
// APLIKÁCIA
// ============================================
define('APP_NAME', 'WebBuilder Pro');
define('APP_ENV', 'development'); // development, production
define('APP_DEBUG', true);

// Wildcard doména
define('MAIN_DOMAIN', 'example.com'); // Zmeň na svoju doménu
define('WILDCARD_DOMAIN', '*.example.com');

// ============================================
// PRIEČINKY (PATHS)
// ============================================
define('ROOT_PATH', dirname(dirname(__FILE__)));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('SRC_PATH', ROOT_PATH . '/src');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');
define('PUBLIC_PAGES_PATH', ROOT_PATH . '/public_pages');
define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');

// ============================================
// WEB CESTY (URLS)
// ============================================
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST']);
define('ASSETS_URL', BASE_URL . '/assets');
define('API_URL', BASE_URL . '/api.php');

// ============================================
// EDITOR KONFIGURÁCIA
// ============================================
define('MAX_ELEMENTS_PER_PAGE', 100);
define('MAX_FILE_UPLOAD_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_UPLOAD_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'svg']);

// ============================================
// SUBDOMAIN VALIDÁCIA
// ============================================
define('SUBDOMAIN_MIN_LENGTH', 3);
define('SUBDOMAIN_MAX_LENGTH', 50);
define('SUBDOMAIN_PATTERN', '/^[a-z0-9-]+$/i'); // Alfanumerické + pomlčka

// ============================================
// AUTOLOAD TRIED
// ============================================
spl_autoload_register(function ($class) {
    // Namespace: App\Models\Page -> src/Models/Page.php
    $prefix = 'App\\';
    $base_dir = SRC_PATH . '/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// ============================================
// ERROR HANDLING
// ============================================
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
}

// ============================================
// TIMEZONE
// ============================================
date_default_timezone_set('Europe/Bratislava');
