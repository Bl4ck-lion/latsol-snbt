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