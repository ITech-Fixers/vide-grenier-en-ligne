<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\View;
use Exception;

/**
 * Home controller
 */
class Home extends \Core\Controller
{

    /**
     * Affiche la page d'accueil
     *
     * @return void
     * @throws Exception
     */
    public function indexAction(): void
    {
        View::renderTemplate('Home/index.html');
    }
}
