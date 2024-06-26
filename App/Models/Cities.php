<?php

namespace App\Models;

use Core\Model;

/**
 * City Model:
 */
class Cities extends Model {

    /**
     * Cherche une ville par son nom
     *
     * @access public
     * @param $str
     * @return false|array
     */
    public static function search($str): false|array
    {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT ville_id, ville_nom_reel FROM villes_france WHERE ville_nom_reel LIKE :query');

        $query = $str . '%';

        $stmt->bindParam(':query', $query);

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    /**
     * Récupère une ville par son id
     *
     * @access public
     * @param int $id
     * @return false|array
     */
    public static function getById(int $id): false|array
    {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT ville_id, ville_nom_reel FROM villes_france WHERE ville_id = :id');

        $stmt->bindParam(':id', $id);

        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
