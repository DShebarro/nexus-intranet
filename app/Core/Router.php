<?php
namespace App\Core;

class Router
{
    private array $routes = [];

    public function add(string $method, string $path, string $controllerAction): void
    {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'pattern' => '#^' . preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $path) . '$#',
            'action'  => $controllerAction,
        ];
    }

    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $uri    = $request->uri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            if (preg_match($route['pattern'], $uri, $matches)) {
                [$ctrlClass, $action] = explode('@', $route['action']);
                $ctrlClass = "App\\Controllers\\{$ctrlClass}";

                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                (new $ctrlClass())->$action($request, $params);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Rota não encontrada.']);
    }
}