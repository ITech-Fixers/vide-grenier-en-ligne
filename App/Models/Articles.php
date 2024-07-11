<?php

declare(strict_types=1);

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
     * @param string $filter
     *
     * @return array|false
     */
    public static function getAll(string $filter): false|array
    {
        $db = static::getDB();

        $query = 'SELECT * FROM articles WHERE articles.is_activated = 1 AND articles.is_donated = 0';

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
     * Récupère les articles à proximité
     * Note : La formule de calcul de distance est basée sur la formule de Haversine
     *
     * @access public
     * @param float $latitude
     * @param float $longitude
     * @param float $radius
     *
     * @return array|false
     */
    public static function getNearby(float $latitude, float $longitude, float $radius): false|array
    {
        $db = static::getDB();

        $query = 'SELECT articles.*, villes_france.ville_latitude_deg, villes_france.ville_longitude_deg,
              (6371 * acos(
                cos(radians(:latitude)) * 
                cos(radians(villes_france.ville_latitude_deg)) * 
                cos(radians(villes_france.ville_longitude_deg) - radians(:longitude)) + 
                sin(radians(:latitude)) * 
                sin(radians(villes_france.ville_latitude_deg))
              )) AS distance 
              FROM articles 
              JOIN villes_france ON articles.ville_id = villes_france.ville_id 
              WHERE articles.is_activated = 1 AND articles.is_donated = 0
              HAVING distance < :radius 
              ORDER BY distance ASC';

        $stmt = $db->prepare($query);
        $stmt->bindValue(':latitude', $latitude, \PDO::PARAM_STR);
        $stmt->bindValue(':longitude', $longitude, \PDO::PARAM_STR);
        $stmt->bindValue(':radius', $radius, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Recherche des articles
     *
     * @access public
     * @param string $search
     *
     * @return array|false
     */
    public static function search(string $search): false|array
    {
        $db = static::getDB();

        $stmt = $db->prepare('
            SELECT articles.*, villes_france.ville_nom_reel, villes_france.ville_code_postal
            FROM articles
            INNER JOIN villes_france ON articles.ville_id = villes_france.ville_id
            WHERE (articles.name LIKE :search OR articles.description LIKE :search)
              AND articles.is_activated = 1 AND articles.is_donated = 0');

        $stmt->bindValue(':search', "%$search%", \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Récupère tous les articles d'un utilisateur
     *
     * @access public
     * @param string $filter
     * @param int $user_id
     *
     * @return array|false
     */
    public static function getAllByUser(string $filter, int $user_id): array|false
    {
        $db = static::getDB();

        $query = 'SELECT * FROM articles WHERE user_id = ?';

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

        $stmt = $db->prepare($query);
        $stmt->execute([$user_id]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Récupère tous les articles d'un utilisateur activés
     *
     * @access public
     * @param string $filter
     * @param int $user_id
     *
     * @return array|false
     */
    public static function getAllByUserActivated(string $filter, int $user_id): array|false
    {
        $db = static::getDB();

        $query = 'SELECT * FROM articles WHERE user_id = ? AND is_activated = 1 AND is_donated = 0';

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

        $stmt = $db->prepare($query);
        $stmt->execute([$user_id]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un article activé par son id
     *
     * @access public
     * @param int $id
     *
     * @return array|false
     */
    public static function getByIdActivated(int $id): array|false
    {
        $db = static::getDB();

        $stmt = $db->prepare('
            SELECT articles.id AS article_id, articles.*, users.id AS user_id, users.username, users.email, users.is_admin, villes_france.ville_nom_reel, villes_france.ville_code_postal
            FROM articles
            INNER JOIN users ON articles.user_id = users.id
            INNER JOIN villes_france ON articles.ville_id = villes_france.ville_id
            WHERE articles.id = ? 
              AND articles.is_activated = 1 AND articles.is_donated = 0
            LIMIT 1');

        $stmt->execute([$id]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un article par son id
     *
     * @access public
     * @param int $id
     *
     * @return array|false
     */
    public static function getById(int $id): array|false
    {
        $db = static::getDB();

        $stmt = $db->prepare('
            SELECT articles.id AS article_id, articles.*, users.id AS user_id, users.username, users.email, users.is_admin, villes_france.ville_nom_reel, villes_france.ville_code_postal
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
     * @param int $id
     *
     * @return void
     */
    public static function addOneView(int $id): void
    {
        $db = static::getDB();

        $stmt = $db->prepare('
            UPDATE articles 
            SET articles.views = articles.views + 1
            WHERE articles.id = ?');

        $stmt->execute([$id]);
    }

    /**
     * Ajoute un contact à un article
     *
     * @access public
     * @param int $id
     *
     * @return void
     */
    public static function addOneContact(int $id): void
    {
        $db = static::getDB();

        $stmt = $db->prepare('
            UPDATE articles 
            SET articles.contact_count = articles.contact_count + 1
            WHERE articles.id = ?');

        $stmt->execute([$id]);
    }

    /**
     * Récupère les articles suggérés
     *
     * @access public
     *
     * @return array|false
     * @throws Exception
     */
    public static function getSuggest(): false|array
    {
        $db = static::getDB();

        $stmt = $db->prepare('
            SELECT *, articles.id as id FROM articles
            INNER JOIN users ON articles.user_id = users.id
            WHERE articles.is_activated = 1 AND articles.is_donated = 0
            ORDER BY published_date DESC LIMIT 10');

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Sauvegarde un article
     *
     * @access public
     * @param array $data
     *
     * @return string|boolean
     */
    public static function save(array $data): bool|string
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
     * Met à jour un article
     *
     * @access public
     * @param int $id
     * @param array $data
     *
     * @return void
     */
    public static function update(int $id, array $data): void
    {
        $db = static::getDB();

        $stmt = $db->prepare('UPDATE articles SET name = :name, description = :description, ville_id = :ville_id WHERE id = :id');

        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':ville_id', $data['city_id']);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
    }

    /**
     * Marque un article comme donné
     *
     * @access public
     * @param int $id
     *
     * @return void
     */
    public static function give(int $id): void
    {
        $db = static::getDB();

        $stmt = $db->prepare('UPDATE articles SET is_donated = 1 WHERE id = ?');

        $stmt->execute([$id]);
    }

    /**
     * Active un article
     *
     * @access public
     * @param int $id
     *
     * @return void
     */
    public static function activate(int $id): void
    {
        $db = static::getDB();

        $stmt = $db->prepare('UPDATE articles SET is_activated = 1, is_donated = 0 WHERE id = ?');

        $stmt->execute([$id]);
    }

    /**
     * Désactive un article
     *
     * @access public
     * @param int $id
     *
     * @return void
     */
    public static function deactivate(int $id): void
    {
        $db = static::getDB();

        $stmt = $db->prepare('UPDATE articles SET is_activated = 0 WHERE id = ?');

        $stmt->execute([$id]);
    }

    /**
     * Attache une image à un article
     *
     * @access public
     * @param int $articleId
     * @param string $pictureName
     *
     * @return void
     */
    public static function attachPicture(int $articleId, string $pictureName): void
    {
        $db = static::getDB();

        $stmt = $db->prepare('UPDATE articles SET picture = :picture WHERE articles.id = :articleid');

        $stmt->bindParam(':picture', $pictureName);
        $stmt->bindParam(':articleid', $articleId);

        $stmt->execute();
    }

    /**
     * Récupère le nombre d'articles par utilisateur
     *
     * @access public
     *
     * @return array|false
     */
    public static function donatePerUser(): array|false
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

    /**
     * Récupère le nombre d'articles par ville
     *
     * @access public
     *
     * @return array|false
     */
    public static function donatePerCity(): array|false
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

    /**
     * Récupère les articles les plus vus
     *
     * @access public
     *
     * @return array|false
     */
    public static function mostViewed(): array|false
    {
        $db = static::getDB();

        $stmt = $db->prepare('
            SELECT a.name, a.views 
            FROM articles a 
            WHERE a.is_activated = 1 AND a.is_donated = 0
            ORDER BY a.views DESC
            LIMIT 5');

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les articles les plus contactés
     *
     * @access public
     *
     * @return array|false
     */
    public static function mostContacted(): array|false
    {
        $db = static::getDB();

        $stmt = $db->prepare('
            SELECT a.name, a.contact_count 
            FROM articles a 
            WHERE a.is_activated = 1 AND a.is_donated = 0
            ORDER BY a.contact_count DESC
            LIMIT 5');

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les articles en ligne
     *
     * @access public
     *
     * @return int
     */
    public static function getOnlineArticleCount(): int
    {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT COUNT(*) FROM articles WHERE is_activated = 1 AND is_donated = 0');
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Récupère le nombre d'articles donnés
     *
     * @access public
     *
     * @return int
     */
    public static function getDonatedArticleCount(): int
    {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT COUNT(*) FROM articles WHERE is_activated = 0 AND is_donated = 1');
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Récupère le nombre total d'articles
     *
     * @access public
     *
     * @return int
     */
    public static function getTotalArticleCount(): int
    {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT COUNT(*) FROM articles');
        $stmt->execute();

        return $stmt->fetchColumn();
    }
}
