<?php

namespace app\core;

use app\core\middlewares\BaseMiddleware;

abstract class Controller
{
    private string $layout = 'main';
    public string $action = '';
    /**
     * @var BaseMiddleware[]
     */
    protected array $middlewares = [];

    public function render(string $view, array $params = []): false|array|string
    {
        return Application::$app->view->renderView($view, $params);
    }

    public function getLayout(): string
    {
        return $this->layout;
    }

    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    public function registerMiddleware(BaseMiddleware $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

}