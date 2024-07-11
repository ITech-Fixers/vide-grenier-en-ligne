<?php

namespace Core;

use App\Models\User;
use Exception;

/**
 * Base controller
 *
 * PHP version 7.0
 */
abstract class Controller
{

    /**
     * Parameters from the matched route
     * @var array
     */
    protected $route_params = [];

    /**
     * Class constructor
     *
     * @param array $route_params  Parameters from the route
     *
     * @return void
     */
    public function __construct(array $route_params)
    {
        $this->route_params = $route_params;
    }

    /**
     * Magic method called when a non-existent or inaccessible method is
     * called on an object of this class. Used to execute before and after
     * filter methods on action methods. Action methods need to be named
     * with an "Action" suffix, e.g. indexAction, showAction etc.
     *
     * @param string $name Method name
     * @param array $args Arguments passed to the method
     *
     * @return void
     * @throws Exception
     */
    public function __call($name, $args)
    {
        $method = $name . 'Action';

        if (method_exists($this, $method)) {
            if ($this->before() !== false) {
                call_user_func_array([$this, $method], $args);
                $this->after();
            }
        } else {
            throw new Exception("Method $method not found in controller " . get_class($this));
        }
    }

    /**
     * Before filter - called before an action method.
     *
     * @return void
     */
    protected function before(): void
    {
        $this->checkRememberMe();
    }

    /**
     * After filter - called after an action method.
     *
     * @return void
     */
    protected function after()
    {
    }

    protected function checkRememberMe(): void
    {
        if (isset($_COOKIE['remember_me']) && !isset($_SESSION['user'])) {
            $user = User::getUserByRememberMeToken($_COOKIE['remember_me']);
            if ($user) {
                $_SESSION['user'] = array(
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'is_admin' => $user['is_admin'] === '1'
                );
            }
        }
    }
}
