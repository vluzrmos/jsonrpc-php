<?php

// Bootstrap for tests
// Sets up the autoloader and default environment variables

// Autoloader do Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Define timezone padrão
date_default_timezone_set('America/Bahia');

if (!getenv('RUN_INTEGRATION_TESTS')) {
    putenv('RUN_INTEGRATION_TESTS=0');
}

// PHP settings for tests
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Helper function for debugging during test development
if (!function_exists('test_debug')) {
    function test_debug($message, $data = null) {
        if (getenv('TEST_DEBUG') === '1') {
            echo "[DEBUG] " . $message;
            if ($data !== null) {
                echo ": " . print_r($data, true);
            }
            echo "\n";
        }
    }
}

echo "Bootstrap completed.\n";
echo "RUN_INTEGRATION_TESTS: " . getenv('RUN_INTEGRATION_TESTS') . "\n";
