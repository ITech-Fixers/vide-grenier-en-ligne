<?php
declare(strict_types=1);

namespace Core;

use Exception;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\Filesystemloader;

/**
 * View
 *
 * PHP version 7.0
 */
class View
{

    /**
     * Render a view file
     *
     * @param string $view The view file
     * @param array $args Associative array of data to display in the view (optional)
     *
     * @return void
     * @throws Exception
     */
    public static function render(string $view, array $args = []): void
    {
        extract($args, EXTR_SKIP);

        $file = dirname(__DIR__) . "/App/Views/$view";  // relative to Core directory

        if (is_readable($file)) {
            require $file;
        } else {
            throw new Exception("$file not found");
        }
    }

    /**
     * Render a view template using Twig
     *
     * @param string $template The template file
     * @param array $args Associative array of data to display in the view (optional)
     *
     * @return void
     * @throws Exception
     */
    public static function renderTemplate(string $template, array $args = []): void
    {
        static $twig = null;

        if ($twig === null) {
            $loader = new Filesystemloader(dirname(__DIR__) . '/App/Views');
            $twig = new Environment($loader, ['debug' => true,]);
            $twig->addExtension(new DebugExtension());
        }

        try {
            echo $twig->render($template, View::setDefaultVariables($args));
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Ajoute les données à fournir à toutes les pages
     * @param array $args
     * @return array
     */
    public static function setDefaultVariables(array $args = []): array
    {
        $args["user"] = $_SESSION['user'] ?? null;
        $args["flash"] = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $args;
    }
}
