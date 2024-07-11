<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */

use Core\View;

session_start();

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('login', ['controller' => 'User', 'action' => 'login']);
$router->add('register', ['controller' => 'User', 'action' => 'register']);
$router->add('logout', ['controller' => 'User', 'action' => 'logout', 'private' => true]);
$router->add('account', ['controller' => 'User', 'action' => 'account', 'private' => true]);
$router->add('product', ['controller' => 'Product', 'action' => 'index', 'private' => true]);
$router->add('product/edit/{id:\d+}', ['controller' => 'Product', 'action' => 'showEdit', 'private' => true]);
$router->add('product/update/{id:\d+}', ['controller' => 'Product', 'action' => 'update', 'private' => true]);
$router->add('product/activate/{id:\d+}', ['controller' => 'Product', 'action' => 'activate', 'private' => true]);
$router->add('product/deactivate/{id:\d+}', ['controller' => 'Product', 'action' => 'deactivate', 'private' => true]);
$router->add('product/give/{id:\d+}', ['controller' => 'Product', 'action' => 'give', 'private' => true]);
$router->add('product/{id:\d+}', ['controller' => 'Product', 'action' => 'show']);
$router->add('product/contact/{id:\d+}', ['controller' => 'Product', 'action' => 'contact', 'private' => true]);
$router->add('product/contact/send/{id:\d+}', ['controller' => 'Product', 'action' => 'sendMessage', 'private' => true]);
$router->add('admin/statistics', ['controller' => 'Statistics', 'action' => 'index', 'private' => true]);
$router->add('{controller}/{action}');

/*
 * Gestion des erreurs dans le routing
 */
try {
    $router->dispatch($_SERVER['QUERY_STRING']);
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
