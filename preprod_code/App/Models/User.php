<?php

namespace App\Models;

use App\Utility\Hash;
use Core\Model;
use App\Core;
use Exception;
use App\Utility;

/**
 * User Model:
 */
class User extends Model {

    /**
     * Crée un utilisateur
     */
    public static function createUser($data) {
        $db = static::getDB();

        $stmt = $db->prepare('INSERT INTO users(username, email, password, salt) VALUES (:username, :email, :password,:salt)');

        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':salt', $data['salt']);

        $stmt->execute();

        return $db->lastInsertId();
    }

    public static function getByLogin($login)
    {
        $db = static::getDB();

        $stmt = $db->prepare("
            SELECT * FROM users WHERE ( users.email = :email) LIMIT 1
        ");

        $stmt->bindParam(':email', $login);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

 
    public static function storeRememberToken($userId, $token, $expiry)
    {
        $db = static::getDB();

        $stmt = $db->prepare("DELETE FROM remember_tokens WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        $stmt = $db->prepare("INSERT INTO remember_tokens (user_id, token, expiry) VALUES (:user_id, :token, :expiry)");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expiry', $expiry);
        $stmt->execute();
    }


    public static function getByRememberToken($token)
    {
        $db = static::getDB();

        $stmt = $db->prepare("
            SELECT u.*, rt.expiry as remember_expiry 
            FROM users u 
            JOIN remember_tokens rt ON u.id = rt.user_id 
            WHERE rt.token = :token 
            LIMIT 1
        ");

        $stmt->bindParam(':token', $token);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }


    public static function deleteRememberToken($token)
    {
        $db = static::getDB();

        $stmt = $db->prepare("DELETE FROM remember_tokens WHERE token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
    }

    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function login() {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT * FROM articles WHERE articles.id = ? LIMIT 1');

        $stmt->execute([$id]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


}
