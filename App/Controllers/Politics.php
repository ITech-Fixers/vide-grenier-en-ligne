<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\View;
use Exception;

class Politics extends Controller
{

    /**
     * Affiche la page de la politique de confidentialité
     *
     * @return void
     * @throws Exception
     */
    public function privacyAction(): void
    {
        View::renderTemplate('Politics/privacy.html');
    }

    /**
     * Affiche la page de la politique relative aux cookies
     *
     * @return void
     * @throws Exception
     */
    public function cookiesAction(): void
    {
        View::renderTemplate('Politics/cookies.html');
    }

}