<?php

namespace App\Controllers;

use Core\View;

class Politics extends \Core\Controller
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