<?php

declare(strict_types=1);

namespace App\Utility;

/**
 * Hash:
 */
class Hash {

    /**
     * Génère et retourne un hash
     *
     * @param string $string
     * @param string $salt
     *
     * @return string
     */
    public static function generate(string $string, string $salt = ""): string
    {
        return(hash("sha256", $string . $salt));
    }

    /**
     * Génère et retourne un salt
     *
     * @param int $length
     *
     * @return string
     */
    public static function generateSalt(int $length): string
    {
        $salt = "";
        $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789/\\][{}\'\";:?.>,<!@#$%^&*()-_=+|";
        for ($i = 0; $i < $length; $i++) {
            $salt .= $charset[mt_rand(0, strlen($charset) - 1)];
        }
        return $salt;
    }

    /**
     * Génère et retourne un UID
     *
     * @return string
     */
    public static function generateUnique(): string
    {
        return(self::generate(uniqid()));
    }

}
