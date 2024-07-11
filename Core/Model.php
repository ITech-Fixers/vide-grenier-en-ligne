<?php

namespace Core;

use PDO;
use App\Config;

/**
 * Base model
 *
 * PHP version 7.0
 */
abstract class Model
{

    /**
     * Get the PDO database connection
     *
     * @return PDO|null
     */
    protected static function getDB(): ?PDO
    {
        static $db = null;

        if ($db === null) {
            $host = $_ENV['MYSQL_HOST'];
            $dbname = $_ENV['DB_NAME'];
            $username = $_ENV['DB_USER'];
            $password = $_ENV['DB_PASSWORD'];

            $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8';

            $db = new PDO($dsn, $username, $password);

            // Throw an Exception when an error occurs
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $db;
    }
}
