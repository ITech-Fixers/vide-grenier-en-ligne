<?php

namespace App\Utility;

class Flash
{
    public static function danger($message): void
    {
        self::addMessage('danger', $message);
    }

    public static function info($message): void
    {
        self::addMessage('info', $message);
    }

    public static function success($message): void
    {
        self::addMessage('success', $message);
    }

    public static function warning($message): void
    {
        self::addMessage('warning', $message);
    }

    public static function addMessage($type, $message): void
    {
        if (!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }

        $_SESSION['flash'][$type] = [
            'message' => $message
        ];
    }
}