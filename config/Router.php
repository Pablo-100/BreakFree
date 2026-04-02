<?php
/**
 * BreakFree - Routeur simple
 */

class Router
{
    private array $routes = [];

    /**
     * Enregistrer une route GET
     */
    public function get(string $path, string $controller, string $method): void
    {
        $this->routes['GET'][$path] = ['controller' => $controller, 'method' => $method];
    }

    /**
     * Enregistrer une route POST
     */
    public function post(string $path, string $controller, string $method): void
    {
        $this->routes['POST'][$path] = ['controller' => $controller, 'method' => $method];
    }

    /**
     * Dispatcher la requête
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Supprimer le base path si nécessaire
        $basePath = parse_url(BASE_URL, PHP_URL_PATH) ?: '';
        if ($basePath && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }
        $uri = '/' . trim($uri, '/');
        if ($uri === '/') $uri = '/';

        // Chercher une correspondance exacte
        if (isset($this->routes[$method][$uri])) {
            $route = $this->routes[$method][$uri];
            $this->callAction($route['controller'], $route['method']);
            return;
        }

        // Chercher des routes avec paramètres {id}
        foreach ($this->routes[$method] ?? [] as $routePath => $route) {
            $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $routePath);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $this->callAction($route['controller'], $route['method'], $matches);
                return;
            }
        }

        // 404
        http_response_code(404);
        require_once VIEWS_PATH . '/layouts/404.php';
    }

    /**
     * Appeler le contrôleur
     */
    private function callAction(string $controllerClass, string $method, array $params = []): void
    {
        $controllerFile = CONTROLLERS_PATH . '/' . $controllerClass . '.php';

        if (!file_exists($controllerFile)) {
            throw new RuntimeException("Contrôleur introuvable : $controllerClass");
        }

        require_once $controllerFile;

        if (!class_exists($controllerClass)) {
            throw new RuntimeException("Classe introuvable : $controllerClass");
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $method)) {
            throw new RuntimeException("Méthode introuvable : {$controllerClass}::{$method}");
        }

        call_user_func_array([$controller, $method], $params);
    }
}
