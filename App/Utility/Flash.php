<?php

namespace App\Utility;

class Flash
{
    /**
     * Ajoute un message flash de type danger
     *
     * @param string $message
     *
     * @return void
     */
    public static function danger(string $message): void
    {
        self::addMessage('danger', $message);
    }

    /**
     * Ajoute un message flash de type info
     *
     * @param string $message
     *
     * @return void
     */
    public static function info(string $message): void
    {
        self::addMessage('info', $message);
    }

    /**
     * Ajoute un message flash de type success
     *
     * @param string $message
     *
     * @return void
     */
    public static function success(string $message): void
    {
        self::addMessage('success', $message);
    }

    /**
     * Ajoute un message flash de type warning
     *
     * @param string $message
     *
     * @return void
     */
    public static function warning(string $message): void
    {
        self::addMessage('warning', $message);
    }

    /**
     * Ajoute un message flash
     *
     * @param string $type
     * @param string $message
     *
     * @return void
     */
    private static function addMessage(string $type, string $message): void
    {
        if (!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }

        $_SESSION['flash'][$type] = [
            'message' => $message
        ];
    }
}