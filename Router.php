<?php

namespace app\core;

use app\core\exception\NotFoundException;

class Router
{
    public Request $request;
    public Response $response;

    protected array $routes = [];

    /**
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get(string $path, mixed $callback): void
    {
        $this->routes['get'][$path] = $callback;
    }

    public function post(string $path, mixed $callback): void
    {
        $this->routes['post'][$path] = $callback;
    }

    /**
     * @throws NotFoundException
     */
    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;
        if ($callback === false) {
            throw new NotFoundException();
        }
        /** @var Controller $controller */
        $controller = new $callback[0]();
        Application::$app->controller = $controller;
        $controller->action = $callback[1];
        $callback[0] = $controller;

        foreach ($controller->getMiddlewares() as $middleware) {
            $middleware->execute();
        }
        return call_user_func($callback, $this->request, $this->response);
    }

}