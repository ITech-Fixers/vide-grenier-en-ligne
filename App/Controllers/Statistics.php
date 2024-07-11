<?php

namespace App\Controllers;

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
        if (!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']) {
            header("Location: /");
            return;
        }

        try {
            View::renderTemplate('Admin/Statistics.html');
        } catch (Exception $e) {
            Flash::danger('Une erreur est survenue, veuillez réessayer');
            header("Location: /");
            return;
        }
    }
}