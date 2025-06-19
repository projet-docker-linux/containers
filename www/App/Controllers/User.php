<?php

namespace App\Controllers;

use App\Config;
use App\Model\UserRegister;
use App\Models\Articles;
use App\Utility\Hash;
use App\Utility\Session;
use \Core\View;
use Exception;
use http\Env\Request;
use http\Exception\InvalidArgumentException;

/**
 * User controller
 */
class User extends \Core\Controller
{

    /**
     * Affiche la page de login
     */
    public function loginAction()
    {
        if(isset($_POST['submit'])){
            $f = $_POST;

            // TODO: Validation

            $this->login($f);

            // Si login OK, redirige vers le compte
            header('Location: /account');
        }

        View::renderTemplate('User/login.html');
    }

    /**
     * Page de création de compte
     */
    public function registerAction()
    {
        if(isset($_POST['submit'])){
            $f = $_POST;

            if($f['password'] !== $f['password-check']){
                // TODO: Gestion d'erreur côté utilisateur
            }

            // validation

            $userID = $this->register($f);
            
            $user = \App\Models\User::getByLogin($f['email']);
            $_SESSION['user'] = array(
                'id' => $user['id'],
                'username' => $user['username'],
            );
            
            header('Location: /');
            exit;
        }

        View::renderTemplate('User/register.html');
    }

    /**
     * Affiche la page du compte
     */
    public function accountAction()
    {
        $articles = Articles::getByUser($_SESSION['user']['id']);

        View::renderTemplate('User/account.html', [
            'articles' => $articles
        ]);
    }

    /*
     * Fonction privée pour enregister un utilisateur
     */
    private function register($data)
    {
        try {
            // Generate a salt, which will be applied to the during the password
            // hashing process.
            $salt = Hash::generateSalt(32);

            $userID = \App\Models\User::createUser([
                "email" => $data['email'],
                "username" => $data['username'],
                "password" => Hash::generate($data['password'], $salt),
                "salt" => $salt
            ]);

            return $userID;

        } catch (Exception $ex) {
            // TODO : Set flash if error : utiliser la fonction en dessous
            /* Utility\Flash::danger($ex->getMessage());*/
        }
    }

    private function login($data){
        try {
            if(!isset($data['email'])){
                throw new Exception('TODO');
            }

            $user = \App\Models\User::getByLogin($data['email']);

            if (Hash::generate($data['password'], $user['salt']) !== $user['password']) {
                return false;
            }

            // Créer la session utilisateur
            $_SESSION['user'] = array(
                'id' => $user['id'],
                'username' => $user['username'],
            );

            if (isset($data['remember_me']) && $data['remember_me'] == '1') {
                $token = bin2hex(random_bytes(32)); 
                $expiry = time() + (30 * 24 * 60 * 60); 
                
                \App\Models\User::storeRememberToken($user['id'], $token, $expiry);
                
                setcookie(
                    'remember_token',
                    $token,
                    $expiry,
                    '/',
                    '',
                    true, 
                    true  
                );
            }

            return true;

        } catch (Exception $ex) {
            // TODO : Set flash if error
            /* Utility\Flash::danger($ex->getMessage());*/
        }
    }

    public function checkRememberMe()
    {
        if (!isset($_SESSION['user']) && isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];
            $user = \App\Models\User::getByRememberToken($token);
            
            if ($user && $user['remember_expiry'] > time()) {
                $_SESSION['user'] = array(
                    'id' => $user['id'],
                    'username' => $user['username'],
                );
            } else {
                // Token invalide ou expiré, supprimer le cookie
                setcookie('remember_token', '', time() - 3600, '/');
            }
        }
    }

    /**
     * Logout: Delete cookie and session. Returns true if everything is okay,
     * otherwise turns false.
     */
    public function logoutAction() {
        // Supprimer le cookie remember me s'il existe
        if (isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];
            \App\Models\User::deleteRememberToken($token);
            setcookie('remember_token', '', time() - 3600, '/');
        }

        // Destroy all data registered to the session
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        header("Location: /");
        return true;
    }

}
