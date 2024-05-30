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

/**
 * User controller
 */
class User extends Controller
{

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

            $this->login($request);

            // Si login OK, redirige vers le compte
            header('Location: /account');
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

            $this->register($request);
            $this->login($request);
            View::renderTemplate('User/account.html');
            return;
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
    private function register($data): void
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

            return;

        } catch (Exception $ex) {
            Flash::danger($ex->getMessage());
        }
    }

    private function login($data): void
    {
        try {
            if(!isset($data['email'])){
                throw new Exception('Veuillez renseigner un email');
            }

            $user = \App\Models\User::getByLogin($data['email']);

            if (Hash::generate($data['password'], $user['salt']) !== $user['password']) {
                return;
            }

            // TODO: Create a remember me cookie if the user has selected the option
            // to remained logged in on the login form.
            // https://github.com/andrewdyer/php-mvc-register-login/blob/development/www/app/Model/UserLogin.php#L86

            $_SESSION['user'] = array(
                'id' => $user['id'],
                'username' => $user['username'],
            );

            return;

        } catch (Exception $ex) {
            Flash::danger($ex->getMessage());
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

        /*
        if (isset($_COOKIE[$cookie])){
            // TODO: Delete the users remember me cookie if one has been stored.
            // https://github.com/andrewdyer/php-mvc-register-login/blob/development/www/app/Model/UserLogin.php#L148
        }*/
        // Destroy all data registered to the session.

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
}
