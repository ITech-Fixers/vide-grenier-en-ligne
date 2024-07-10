<?php

namespace App\Models;

use Core\Model;
use DateTime;
use Exception;

/**
 * Articles Model
 */
class Articles extends Model {

    /**
     * Récupère tous les articles
     *
     * @access public
     * @param $filter
     * @return array|false
     */
    public static function getAll($filter): false|array
    {
        $db = static::getDB();

        $query = 'SELECT * FROM articles ';

        switch ($filter){
            case 'views':
                $query .= ' ORDER BY articles.views DESC';
                break;
            case 'date':
                $query .= ' ORDER BY articles.published_date DESC';
                break;
            case '':
                break;
        }

        $stmt = $db->query($query);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un article par son id
     *
     * @access public
     * @param $id
     * @return array|false
     */
    public static function getOne($id): array|false
    {
        $db = static::getDB();

        $stmt = $db->prepare('
            SELECT articles.*, users.*, villes_france.ville_nom_reel, villes_france.ville_code_postal
            FROM articles
            INNER JOIN users ON articles.user_id = users.id
            INNER JOIN villes_france ON articles.ville_id = villes_france.ville_id
            WHERE articles.id = ? 
            LIMIT 1');

        $stmt->execute([$id]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Ajoute une vue à un article
     *
     * @access public
     * @param $id
     * @return void
     */
    public static function addOneView($id): void
    {
        $db = static::getDB();

        $stmt = $db->prepare('
            UPDATE articles 
            SET articles.views = articles.views + 1
            WHERE articles.id = ?');

        $stmt->execute([$id]);
    }

    /**
     * Récupère les articles d'un utilisateur
     *
     * @access public
     * @param $id
     * @return array|false
     */
    public static function getByUser($id): false|array
    {
        $db = static::getDB();

        $stmt = $db->prepare('
            SELECT *, articles.id as id FROM articles
            LEFT JOIN users ON articles.user_id = users.id
            WHERE articles.user_id = ?');

        $stmt->execute([$id]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les articles suggérés
     *
     * @access public
     * @return array|false
     * @throws Exception
     */
    public static function getSuggest(): false|array
    {
        $db = static::getDB();

        $stmt = $db->prepare('
            SELECT *, articles.id as id FROM articles
            INNER JOIN users ON articles.user_id = users.id
            ORDER BY published_date DESC LIMIT 10');

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    /**
     * Sauvegarde un article
     *
     * @access public
     * @param $data
     * @return string|boolean
     */
    public static function save($data): bool|string
    {
        $db = static::getDB();

        $stmt = $db->prepare('INSERT INTO articles(name, description, user_id, published_date, ville_id) VALUES (:name, :description, :user_id, :published_date, :ville_id)');

        $published_date =  new DateTime();
        $published_date = $published_date->format('Y-m-d');
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':published_date', $published_date);
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':ville_id', $data['city_id']);

        $stmt->execute();

        return $db->lastInsertId();
    }

    /**
     * Attache une image à un article
     *
     * @access public
     * @param $articleId
     * @param $pictureName
     * @return void
     */
    public static function attachPicture($articleId, $pictureName): void
    {
        $db = static::getDB();

        $stmt = $db->prepare('UPDATE articles SET picture = :picture WHERE articles.id = :articleid');

        $stmt->bindParam(':picture', $pictureName);
        $stmt->bindParam(':articleid', $articleId);


        $stmt->execute();
    }

    public static function donatePerUser()
    {

        $db = static::getDB();

        $stmt = $db->prepare('
            SELECT u.username, COUNT(a.id) AS nombre_d_articles
            FROM articles a
            JOIN users u ON a.user_id = u.id
            GROUP BY u.username 
            ORDER BY nombre_d_articles DESC
            LIMIT 5');

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function donatePerCity()
    {
        $db = static::getDB();

        $stmt = $db->prepare('
            SELECT v.ville_nom_reel, COUNT(a.id) AS nombre_d_articles
            FROM articles a 
            JOIN villes_france v on a.ville_id = v.ville_id 
            GROUP BY v.ville_nom_reel 
            ORDER BY nombre_d_articles DESC
            LIMIT 5');

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    public static function mostViewed()
    {
        $db = static::getDB();

        $stmt = $db->prepare('
            SELECT a.name, a.views 
            FROM articles a 
            ORDER BY a.views DESC
            LIMIT 5');

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
