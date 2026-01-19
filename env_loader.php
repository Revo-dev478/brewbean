<?php
/**
 * Environment Variable Loader
 * 
 * Loads environment variables from .env file
 * Usage: require_once 'env_loader.php'; at the top of your PHP files
 */

function loadEnv($path = null) {
    if ($path === null) {
        $path = __DIR__ . '/.env';
    }
    
    if (!file_exists($path)) {
        // Fallback: check if we're in a subdirectory
        $parentPath = dirname(__DIR__) . '/.env';
        if (file_exists($parentPath)) {
            $path = $parentPath;
        } else {
            return false;
        }
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                $value = substr($value, 1, -1);
            }
            
            // Set environment variable
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
    
    return true;
}

/**
 * Get environment variable with optional default value
 * 
 * @param string $key The environment variable name
 * @param mixed $default Default value if not found
 * @return mixed
 */
function env($key, $default = null) {
    $value = getenv($key);
    
    if ($value === false) {
        $value = isset($_ENV[$key]) ? $_ENV[$key] : null;
    }
    
    if ($value === null || $value === false) {
        return $default;
    }
    
    // Convert string booleans
    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'null':
        case '(null)':
            return null;
        case 'empty':
        case '(empty)':
            return '';
    }
    
    return $value;
}

// Auto-load .env file when this script is included
loadEnv();
