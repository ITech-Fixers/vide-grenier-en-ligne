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

        //if user not null
        if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
            if ($_SESSION['user']['is_admin']) {
                try {
                    View::renderTemplate('Admin/Statistics.html');
                } catch (Exception $e) {
                    Flash::danger('Une erreur est survenue, veuillez réessayer');
                    header("Location: /");
                    exit;
                }
            } else {
                header("Location: /");
                exit;
            }
        } else {
            header("Location: /");
            exit;
        }
    }
}