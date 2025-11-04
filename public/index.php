<?php

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Autoload classes
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Load configuration
$config = require __DIR__ . '/../app/Config/config.php';

// Start the session
session_start();

// Route the request
$router = new app\Core\Router();
require __DIR__ . '/../app/routes.php';

$router->dispatch($_SERVER['REQUEST_URI']);