<?php

namespace App\Controllers;

use App\Exception\UserNotFoundException;
use App\Exception\ValidationException;
use App\Service\Validation\UserRegister;
use App\Utility\Flash;
use App\Utility\Hash;
use Core\Controller;
use Core\View;
use Exception;
use Random\RandomException;

/**
 * User controller
 */
class User extends Controller
{

    public function __construct($route_params)
    {
        parent::__construct($route_params);
    }


    /**
     * Affiche la page de login
     * @throws Exception
     */
    public function loginAction(): void
    {
        try {
            if (isset($_POST['submit'])){
                $request = $_POST;

                if (!isset($request['email']) || !isset($request['password'])){
                    Flash::danger('Veuillez renseigner un email et un mot de passe');
                    View::renderTemplate('User/login.html');
                    return;
                }

                $this->login($request);

                header('Location: /account');
            }

            View::renderTemplate('User/login.html');
        } catch (ValidationException|UserNotFoundException $e) {
            Flash::danger($e->getMessage());
            View::renderTemplate('User/login.html');
        } catch (Exception) {
            Flash::danger('Une erreur est survenue, veuillez réessayer');
            View::renderTemplate('User/login.html');
        }
    }

    /**
     * Page de création de compte
     * @throws Exception
     */
    public function registerAction(): void
    {
        try {
            if (isset($_POST['submit'])){
                $request = $_POST;
                $errors = UserRegister::validate($request);

                if (!empty($errors)){
                    throw new ValidationException(implode('<br>', $errors));
                }

                $this->register($request);
                $this->login($request);

                View::renderTemplate('User/account.html');
                return;
            }

            View::renderTemplate('User/register.html');
        } catch (ValidationException $e) {
            Flash::danger($e->getMessage());
            View::renderTemplate('User/register.html');
        } catch (UserNotFoundException|Exception) {
            Flash::danger('Une erreur est survenue, veuillez réessayer');
            View::renderTemplate('User/register.html');
        }
    }

    /**
     * Affiche la page du compte
     * @throws Exception
     */
    public function accountAction(): void
    {
        View::renderTemplate('User/account.html');
    }

    /**
     * Enregistre un utilisateur
     * @throws Exception
     */
    private function register($data): void
    {
        $salt = Hash::generateSalt(32);

        \App\Models\User::createUser([
            "email" => $data['email'],
            "username" => $data['username'],
            "password" => Hash::generate($data['password'], $salt),
            "salt" => $salt
        ]);
    }

    /**
     * Connexion d'un utilisateur
     * @throws ValidationException
     * @throws UserNotFoundException
     * @throws RandomException
     */
    private function login($data): void
    {
        if (!isset($data['email'])){
            throw new ValidationException('Veuillez renseigner un email');
        }

        $user = \App\Models\User::getByLogin($data['email']);

        if (!$user) {
            throw new UserNotFoundException('Utilisateur non trouvé');
        }

        if (Hash::generate($data['password'], $user['salt']) !== $user['password']) {
            throw new ValidationException('Mot de passe incorrect');
        }

        $_SESSION['user'] = array(
            'id' => $user['id'],
            'username' => $user['username'],
            'is_admin' => $user['is_admin'] == 1
        );


        if (isset($data['remember-me'])) {
            $this->createRememberMeToken($user['id']);
        }
    }


    /**
     * Déconnexion d'un utilisateur
     * @access public
     * @return boolean
     */
    public function logoutAction(): bool
    {

        if (isset($_COOKIE['remember_me'])) {
            $token = $_COOKIE['remember_me'];
            \App\Models\User::deleteRememberMeToken($token);

            setcookie('remember_me', '', time() - 3600, "/");
        }

        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
        header ("Location: /");

        return true;
    }

    /**
     * Crée un token de connexion
     * @throws RandomException
     */
    private function createRememberMeToken(int $userId): void
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

        \App\Models\User::storeRememberMeToken($userId, $token, $expiresAt);

        // Cookie avec une durée de vie de 30 jours
        setcookie('remember_me', $token, time() + (86400 * 30), "/", "", false, true);
    }
}
