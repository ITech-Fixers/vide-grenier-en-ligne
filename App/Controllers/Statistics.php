<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Utility\Flash;
use Core\Controller;
use Core\View;
use Exception;

class Statistics extends Controller
{


    /**
     * Affiche la page d'un produit
     * @return void
     * @throws Exception
     */
    public function index(): void
    {

        try {
            View::renderTemplate('Admin/Statistics.html');
        } catch (Exception) {
            Flash::danger('Une erreur est survenue, veuillez réessayer');
            header ("Location: /");
        }
    }
}