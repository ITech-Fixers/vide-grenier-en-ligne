<?php

namespace App\Models;

use App\Utility\Hash;
use Core\Model;
use App\Core;
use Exception;
use App\Utility;

/**
 * City Model:
 */
class Cities extends Model {

    public static function search($str) {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT ville_id, ville_nom_reel FROM villes_france WHERE ville_nom_reel LIKE :query');

        $query = $str . '%';

        $stmt->bindParam(':query', $query);

        $stmt->execute();

        $results = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

        return $results;
    }
}
