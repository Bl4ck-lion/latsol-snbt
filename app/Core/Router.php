<?php
namespace app\Core;

class Router {
    private $routes = [];

    public function add($route, $controller, $action, $method = 'GET') {
        $this->routes[] = [
            'route' => $route,
            'controller' => $controller,
            'action' => $action,
            'method' => $method
        ];
    }

    public function dispatch($uri) {
        $uri = parse_url($uri, PHP_URL_PATH);

        // Handle static assets. This is a simple implementation for development.
        // In a production environment, the web server (e.g., Nginx) should be configured to handle this.
        if (preg_match('/^\/assets\//', $uri)) {
            $filePath = __DIR__ . '/../../public' . $uri;
            if (file_exists($filePath)) {
                $mimeTypes = [
                    'css' => 'text/css',
                    'js'  => 'application/javascript',
                    'png' => 'image/png',
                    'jpg' => 'image/jpeg',
                    'jpeg'=> 'image/jpeg',
                    'gif' => 'image/gif',
                    'svg' => 'image/svg+xml',
                ];
                $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
                $mimeType = $mimeTypes[$fileExtension] ?? 'application/octet-stream';

                header('Content-Type: ' . $mimeType);
                readfile($filePath);
                return;
            }
        }

        foreach ($this->routes as $route) {
            $pattern = '#^' . preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_]+)', $route['route']) . '$#';
            if (preg_match($pattern, $uri, $matches) && $_SERVER['REQUEST_METHOD'] === $route['method']) {
                array_shift($matches);
                $controllerName = 'app\\Controllers\\' . $route['controller'];
                $controller = new $controllerName();
                call_user_func_array([$controller, $route['action']], $matches);
                return;
            }
        }

        // Handle 404
        header("HTTP/1.0 404 Not Found");
        echo '404 Not Found';
    }
}