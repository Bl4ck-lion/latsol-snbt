<?php
// This is a placeholder for a real Composer autoloader.
// In a real project, you would run `composer install` to generate this file.

spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});