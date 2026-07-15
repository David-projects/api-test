<?

declare(strict_types=1);

namespace Framework;

class Router
{
    private array $routes = [];
    private array $middlewares = [];
    private array $errorHandler;

    /**
     * adds a route to the router
     * 
     * @param string $method: Method to be added GET, POST, PUT, DELETE
     * @param string $path: Path to be added to the router
     * @param array $controller: controller to  be added to the router
     */
    public function add(string $method, string $path, array $controller)
    {
        $path = $this->normalizePath($path);
        $regexPath = preg_replace("#{[^/]+}#", "([^/]+)", $path);

        $this->routes[] = [
            "path" => $path,
            "method" => strtoupper($method),
            "controller" => $controller,
            "middlewares" => [],
            "regexPath" => $regexPath
        ];
    }

    /**
     * normalize the path for the route removes the forward slashes to ensure there is only one at the start and end
     * @param string $path: Path to be normalized
     * 
     * @return string : returns a normalized path as a string
     */
    private function normalizePath(string $path): string
    {
        $regex = "#[/]{2,}#";
        $path = trim($path, "/");
        $path = "/{$path}/";
        return preg_replace($regex, "/", $path);
    }

    /**
     * dispatches the pages to be displayed
     * 
     * @param string $path: Path to route too
     * @param string $method: Method to be added GET, POST, PUT, DELETE
     * @param Container $container: Container to be returned,
     */
    public function dispatch(string $path, string $method, Container $container = null)
    {
        $path = $this->normalizePath($path);
        $method = strtoupper($_POST['_METHOD'] ?? $method);

        /**
         * Looping thought all the routes to find the right one
         * 
         */
        foreach ($this->routes as $route) {

            if (
                !preg_match("#^{$route['regexPath']}$#", $path, $paramValues) ||
                $route['method'] !== $method
            ) {
                continue;
            }

            array_shift($paramValues);
            preg_match_all('#{([^/]+)}#', $route['path'], $paramKeys);
            $paramKeys = $paramKeys[1];
            $params = array_combine($paramKeys, $paramValues);

            /**
             * Gets the class and function call in the request.
             * Then loads the controller and calls the function.
             */
            [$class, $function] = $route['controller'];

            $controllerInstance = $container ? $container->resolve($class) : new $class;

            $allMiddleware = [...$route['middlewares'], ...$this->middlewares];


            $action = fn () => $controllerInstance->{$function}($params);

            foreach ($allMiddleware as $middleware) {
                $middlewareInstance = $container ? $container->resolve($middleware) : new $middleware();
                $action = fn () => $middlewareInstance->process($action);
            }

            $action();

            return;
        }

        $this->dispatchNotFound($container);
    }

    public function addMiddleware(string $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    public function addRouteMiddleware(string $middleware)
    {
        $lastRoutekey = array_key_last($this->routes);
        $this->routes[$lastRoutekey]['middlewares'][] = $middleware;
    }

    public function setErrorHandler(array $controller)
    {
        $this->errorHandler = $controller;
    }

    public function dispatchNotFound(?Container $container)
    {
        [$class, $function] = $this->errorHandler;
        $controllerInstance = $container ? $container->resolve($class) : new $class;

        $action = fn () => $controllerInstance->$function();

        foreach ($this->middlewares as $middleware) {
            $middlewareInstance = $container ? $container->resolve($middleware) : new $class;
            $action = fn () => $middlewareInstance->process($action);
        }

        $action();
    }
}