<?php

namespace app\core;

use app\core\db\Database;
use app\core\db\DbModel;
use app\models\User;

class Application
{
    public static Application $app;
    public static string $ROOT_DIR;

    public string $layout = 'main';
    public User $userInstance;
    public ?Controller $controller = null;
    public Database $db;
    public ?UserModel $user;
    public View $view;
    public Session $session;
    public Router $router;
    public Request $request;
    public Response $response;

    public function __construct(string $rootPath, array $config)
    {
        $this->userInstance = $config['userInstance'];
        self::$app = $this;
        self::$ROOT_DIR = $rootPath;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View();

        $this->db = new Database($config['db']);

        $primaryValue = $this->session->get('user');
        if ($primaryValue) {
            $primaryKey = $this->userInstance->primaryKey();
            $this->user = $this->userInstance->findOne([$primaryKey => $primaryValue]);
        } else {
            $this->user = null;
        }
    }

    public function getController(): Controller
    {
        return $this->controller;
    }

    public function setController(Controller $controller): void
    {
        $this->controller = $controller;
    }

    public function run(): void
    {
        try {
            echo $this->router->resolve();
        } catch (\Exception $ex) {
            $this->response->setStatusCode($ex->getCode());
            echo $this->view->renderView('_error', [
                'exception' => $ex
            ]);
        }
    }

    public function login(UserModel $user)
    {
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user', $primaryValue);
        return true;
    }

    public function logout(): void
    {
        $this->user = null;
        $this->session->remove('user');
    }

    public static function isGuest(): bool
    {
        return !self::$app->user;
    }
}