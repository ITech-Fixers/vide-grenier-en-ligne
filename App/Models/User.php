<?php

namespace App\Models;

use Core\Model;
use Exception;
use PDO;

/**
 * User Model:
 */
class User extends Model {

    /**
     * Crée un utilisateur
     */
    public static function createUser($data): false|string
    {
        $db = static::getDB();

        $stmt = $db->prepare('INSERT INTO users(username, email, password, salt) VALUES (:username, :email, :password,:salt)');

        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':salt', $data['salt']);

        $stmt->execute();

        return $db->lastInsertId();
    }

    /**
     * Récupère un utilisateur par son email
     * @access public
     * @param string $login
     * @return array|false
     */
    public static function getByLogin(string $login): false|array
    {
        $db = static::getDB();

        $stmt = $db->prepare("
            SELECT * FROM users WHERE ( users.email = :email) LIMIT 1
        ");

        $stmt->bindParam(':email', $login);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }


    /**
     * Récupère un utilisateur par son id
     * @access public
     * @return array|false
     * @throws Exception
     */
    public static function login(): false|array
    {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT * FROM articles WHERE articles.id = ? LIMIT 1');

        $stmt->execute([$id]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Stocke un token de "se souvenir de moi" dans la base de données
     * @access public
     * @param int $userId
     * @param string $token
     * @param string $expiresAt
     * @return void
     */
    public static function storeRememberMeToken(int $userId, string $token, string $expiresAt): void
    {
        $db = static::getDB();
        $stmt = $db->prepare('INSERT INTO user_tokens (user_id, token, expires_at) VALUES (?, ?, ?)');
        $stmt->execute([$userId, $token, $expiresAt]);
    }

    /**
     * Récupère un utilisateur par son token de "se souvenir de moi"
     * @access public
     * @param string $token
     * @return array|false
     */
    public static function getUserByRememberMeToken(string $token): false|array
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT u.* FROM users u INNER JOIN user_tokens ut ON u.id = ut.user_id WHERE ut.token = ? AND ut.expires_at > NOW()');
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Supprime un token de "se souvenir de moi" de la base de données
     * @access public
     * @param string $token
     * @return void
     */
    public static function deleteRememberMeToken(string $token): void
    {
        $db = static::getDB();
        $stmt = $db->prepare('DELETE FROM user_tokens WHERE token = ?');
        $stmt->execute([$token]);
    }
}
