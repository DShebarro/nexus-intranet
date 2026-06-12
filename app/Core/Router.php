<?php
namespace App\Core;

class Router
{
    private array $routes = [];
    private array $middlewareAliases = [
        'auth'  => \App\Middleware\AuthMiddleware::class,
        'guest' => \App\Middleware\GuestMiddleware::class,
        'csrf'  => \App\Middleware\CsrfMiddleware::class,
        'admin' => \App\Middleware\AdminMiddleware::class,
    ];
    private array $groupMiddleware = [];
    private ?Container $container = null;

    public function setContainer(Container $container): void
    {
        $this->container = $container;
    }

    public function add(string $method, string $path, string $controllerAction, array $middleware = []): self
    {
        $this->routes[] = [
            'method'     => strtoupper($method),
            'pattern'    => '#^' . preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $path) . '$#',
            'action'     => $controllerAction,
            'middleware' => array_merge($this->groupMiddleware, $middleware),
        ];
        return $this;
    }

    public function group(array $options, callable $callback): void
    {
        $previous = $this->groupMiddleware;
        $middleware = $options['middleware'] ?? [];

        if (is_string($middleware)) {
            $middleware = [$middleware];
        }

        $this->groupMiddleware = array_merge($previous, $middleware);
        $callback($this);
        $this->groupMiddleware = $previous;
    }

    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $uri    = $request->uri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (!preg_match($route['pattern'], $uri, $matches)) {
                continue;
            }

            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            $this->runRoute($route, $request, $params);
            return;
        }

        (new Response())->json(['error' => 'Rota não encontrada.'], 404);
    }

    private function runRoute(array $route, Request $request, array $params): void
    {
        $middleware = $route['middleware'];
        $index = 0;

        $next = function (Request $req) use (&$next, &$index, $middleware, $route, $params) {
            if ($index < count($middleware)) {
                $alias = $middleware[$index++];
                $class = $this->middlewareAliases[$alias] ?? $alias;
                $instance = $this->container
                    ? $this->container->make($class)
                    : new $class();
                $instance->handle($req, fn($r) => $next($r));
                return;
            }

            [$ctrlClass, $action] = explode('@', $route['action']);
            $ctrlClass = "App\\Controllers\\{$ctrlClass}";

            $controller = $this->container
                ? $this->container->make($ctrlClass)
                : new $ctrlClass();

            $controller->$action($req, $params);
        };

        $next($request);
    }
}
