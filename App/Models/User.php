<?php

declare(strict_types=1);

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
     *
     * @access public
     * @param array $data
     *
     * @return false|string
     */
    public static function createUser(array $data): false|string
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
     *
     * @access public
     * @param string $login
     *
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
     *
     * @access public
     * @param int $id
     *
     * @return array|false
     */
    public static function getById(int $id): false|array
    {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Stocke un token de "se souvenir de moi" dans la base de données
     *
     * @access public
     * @param int $userId
     * @param string $token
     * @param string $expiresAt
     *
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
     *
     * @access public
     * @param string $token
     *
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
     *
     * @access public
     * @param string $token
     *
     * @return void
     */
    public static function deleteRememberMeToken(string $token): void
    {
        $db = static::getDB();
        $stmt = $db->prepare('DELETE FROM user_tokens WHERE token = ?');
        $stmt->execute([$token]);
    }

    /**
     * Récupère un utilisateur par un id d'article
     *
     * @access public
     * @param int $articleId
     *
     * @return array|false
     */
    public static function getByArticle(int $articleId): false|array
    {
        $db = static::getDB();

        $stmt = $db->prepare('
            SELECT users.id, users.username, users.email FROM users
            INNER JOIN articles ON users.id = articles.user_id
            WHERE articles.id = ?');
        $stmt->execute([$articleId]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Récupère le nombre d'utilisateurs
     *
     * @access public
     *
     * @return int
     */
    public static function getUserCount(): int
    {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT COUNT(*) FROM users');
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Vérifie si un utilisateur a un article
     *
     * @access public
     * @param int $articleId
     * @param int $userId
     *
     * @return bool
     */
    public static function hasArticle(int $articleId, int $userId): bool
    {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT COUNT(*) FROM articles WHERE id = ? AND user_id = ?');
        $stmt->execute([$articleId, $userId]);

        return $stmt->fetchColumn() > 0;
    }
}
