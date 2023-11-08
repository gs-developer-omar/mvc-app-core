<?php

namespace app\core;

class View
{
    public string $title = '';

    public function renderView(string $view, array $params = []): array|false|string
    {
        $viewContent = $this->getViewContent($view, $params);
        $layoutContent = $this->getLayoutContent();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    public function getViewContent(string $view, array $params = []): false|string
    {
        foreach ($params as $key => $value) {
            ${$key} = $value;
        }
        ob_start();
        include_once Application::$ROOT_DIR . "./views/$view.php";
        return ob_get_clean();
    }

    public function getLayoutContent(): false|string
    {
        $layout = Application::$app->layout;
        if (Application::$app->controller) {
            $layout = Application::$app->getController()->getLayout();
        }
        ob_start();
        include_once Application::$ROOT_DIR . "./views/layouts/$layout.php";
        return ob_get_clean();
    }
}