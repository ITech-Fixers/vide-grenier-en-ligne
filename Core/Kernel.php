<?php

namespace Core;

use Dotenv\Dotenv;
use Exception;

final readonly class Kernel
{
    private Router $router;
    private Dotenv $dotenv;

    public function __construct()
    {
        $this->router = new Router();
        $this->dotenv = Dotenv::createImmutable(dirname(__DIR__));
        $this->setUp();
    }

    /**
     * Lance l'application
     *
     * @throws Exception
     */
    public function run(): void
    {
        try {
            $this->router->dispatch($_SERVER['QUERY_STRING']);
        } catch (Exception $e) {

            if ($e->getMessage() == 'You must be logged in') {
                header('Location: /login');
                exit();
            }

            switch ($e->getCode()) {
                case 500:
                    View::renderTemplate('500.html');
                    break;
                case 404:
                    View::renderTemplate('404.html');
                    break;
                default:
                    throw $e;
            }
        }
    }

    /**
     * Initialise l'application
     */
    private function setUp(): void
    {
        $this->setSession();
        $this->loadEnv();
        $this->setConfig();
        $this->addRoutes();
    }

    /**
     * Initialise la session
     */
    private function setSession(): void
    {
        session_start();
    }

    /**
     * Charge les variables d'environnement
     */
    private function loadEnv(): void
    {
        $this->dotenv->load();
    }

    /**
     * Configure l'application
     */
    private function setConfig(): void
    {
        error_reporting(E_ALL);
        set_error_handler('Core\Error::errorHandler');
        set_exception_handler('Core\Error::exceptionHandler');
    }

    /**
     * Ajoute les routes
     */
    public function addRoutes(): void
    {
        $this->router->add('', ['controller' => 'Home', 'action' => 'index']);
        $this->router->add('privacy', ['controller' => 'Politics', 'action' => 'privacy']);
        $this->router->add('cookies', ['controller' => 'Politics', 'action' => 'cookies']);
        $this->router->add('login', ['controller' => 'User', 'action' => 'login']);
        $this->router->add('register', ['controller' => 'User', 'action' => 'register']);
        $this->router->add('logout', ['controller' => 'User', 'action' => 'logout', 'private' => true]);
        $this->router->add('account', ['controller' => 'User', 'action' => 'account', 'private' => true]);
        $this->router->add('product', ['controller' => 'Product', 'action' => 'index', 'private' => true]);
        $this->router->add('product/user/{id:\d+}', ['controller' => 'User', 'action' => 'otherAccount', 'private' => true]);
        $this->router->add('product/edit/{id:\d+}', ['controller' => 'Product', 'action' => 'showEdit', 'private' => true]);
        $this->router->add('product/update/{id:\d+}', ['controller' => 'Product', 'action' => 'update', 'private' => true]);
        $this->router->add('product/activate/{id:\d+}', ['controller' => 'Product', 'action' => 'activate', 'private' => true]);
        $this->router->add('product/deactivate/{id:\d+}', ['controller' => 'Product', 'action' => 'deactivate', 'private' => true]);
        $this->router->add('product/give/{id:\d+}', ['controller' => 'Product', 'action' => 'give', 'private' => true]);
        $this->router->add('product/{id:\d+}', ['controller' => 'Product', 'action' => 'show']);
        $this->router->add('product/contact/{id:\d+}', ['controller' => 'Product', 'action' => 'contact', 'private' => true]);
        $this->router->add('product/contact/send/{id:\d+}', ['controller' => 'Product', 'action' => 'sendMessage', 'private' => true]);
        $this->router->add('admin/statistics', ['controller' => 'Statistics', 'action' => 'index', 'private' => true]);
        $this->router->add('{controller}/{action}');
    }
}