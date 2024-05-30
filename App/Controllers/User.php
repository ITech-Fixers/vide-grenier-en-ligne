<?php

namespace App\Controllers;

use App\Models\UserRegister;
use App\Models\Articles;
use App\Utility\Flash;
use App\Utility\Hash;
use Core\Controller;
use Core\View;
use ErrorException;
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
        if(isset($_POST['submit'])){
            $request = $_POST;

            if (!isset($request['email']) || !isset($request['password'])){
                Flash::danger('Veuillez renseigner un email et un mot de passe');
                View::renderTemplate('User/login.html');
                return;
            }

            $login = $this->login($request);

            if ($login) {
                View::renderTemplate('User/account.html');
                return;
            }
        }

        View::renderTemplate('User/login.html');
    }

    /**
     * Page de création de compte
     * @throws ErrorException
     * @throws Exception
     */
    public function registerAction(): void
    {
        if(isset($_POST['submit'])){
            $request = $_POST;
            $errors = UserRegister::validate($request);

            if (!empty($errors)){
                Flash::danger(implode('<br>', $errors));
                View::renderTemplate('User/register.html');
                return;
            }

            $register = $this->register($request);

            if ($register) {
                $this->login($request);
                View::renderTemplate('User/account.html');
                return;
            }
        }

        View::renderTemplate('User/register.html');
    }

    /**
     * Affiche la page du compte
     * @throws Exception
     */
    public function accountAction(): void
    {
        $articles = Articles::getByUser($_SESSION['user']['id']);

        View::renderTemplate('User/account.html', [
            'articles' => $articles
        ]);
    }

    /*
     * Fonction privée pour enregistrer un utilisateur
     */
    private function register($data): bool
    {
        try {
            // Generate a salt, which will be applied to the during the password
            // hashing process.
            $salt = Hash::generateSalt(32);

            \App\Models\User::createUser([
                "email" => $data['email'],
                "username" => $data['username'],
                "password" => Hash::generate($data['password'], $salt),
                "salt" => $salt
            ]);

            return true;

        } catch (Exception $ex) {
            Flash::danger($ex->getMessage());
            return false;
        }
    }

    private function login($data): bool
    {
        try {
            if(!isset($data['email'])){
                throw new Exception('Veuillez renseigner un email');
            }

            $user = \App\Models\User::getByLogin($data['email']);

            if (!$user) {
                throw new Exception('Utilisateur non trouvé');
            }

            if (Hash::generate($data['password'], $user['salt']) !== $user['password']) {
                throw new Exception('Mot de passe incorrect');
            }

            $_SESSION['user'] = array(
                'id' => $user['id'],
                'username' => $user['username'],
            );

            if (isset($data['remember-me'])) {
                $this->createRememberMeToken($user['id']);
            }

            return true;

        } catch (Exception $ex) {
            Flash::danger($ex->getMessage());
            return false;
        }
    }


    /**
     * Logout: Delete cookie and session. Returns true if everything is okay,
     * otherwise turns false.
     * @access public
     * @return boolean
     * @since 1.0.2
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
     * @throws RandomException
     */
    private function createRememberMeToken(int $userId): void
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

        // Insérer le token dans la base de données
        \App\Models\User::storeRememberMeToken($userId, $token, $expiresAt);

        // Créer le cookie avec une durée de vie de 30 jours
        setcookie('remember_me', $token, time() + (86400 * 30), "/", "", false, true);
    }
}
