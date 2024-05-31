<?php

namespace App\Controllers;

use App\Exception\CityNotFoundException;
use App\Exception\ValidationException;
use App\Models\ArticleAdd;
use App\Models\Articles;
use App\Models\Cities;
use App\Utility\Flash;
use App\Utility\Upload;
use Core\Controller;
use Core\View;
use Exception;

/**
 * Product controller
 */
class Product extends Controller
{
    /**
     * Affiche la page d'ajout d'un produit
     * @return void
     * @throws Exception
     */
    public function indexAction(): void
    {
        if(isset($_POST['submit'])) {
            try {
                $request = $_POST;
                $errors = ArticleAdd::validate($request);

                if (!empty($errors)) {
                    throw new ValidationException(implode('<br>', $errors));
                }

                $city = Cities::getById($request['city_id']);

                if (empty($city)) {
                    throw new CityNotFoundException('La ville sélectionnée n\'existe pas');
                }

                $request['user_id'] = $_SESSION['user']['id'];
                $id = Articles::save($request);

                $pictureName = Upload::uploadFile($_FILES['picture'], $id);

                Articles::attachPicture($id, $pictureName);

                Flash::success('Produit ajouté avec succès');
                $this->route_params['id'] = $id;
                $this->showAction();
            } catch (ValidationException|CityNotFoundException $e) {
                Flash::danger($e->getMessage());
                View::renderTemplate('Product/Add.html');
            } catch (Exception) {
                Flash::danger('Une erreur est survenue, veuillez réessayer');
                View::renderTemplate('Product/Add.html');
            }
        } else {
            View::renderTemplate('Product/Add.html');
        }
    }

    /**
     * Affiche la page d'un produit
     * @return void
     * @throws Exception
     */
    public function showAction(): void
    {
        $id = $this->route_params['id'];

        try {
            Articles::addOneView($id);
            $suggestions = Articles::getSuggest();
            $article = Articles::getOne($id);

            View::renderTemplate('Product/Show.html', [
                'article' => $article[0],
                'suggestions' => $suggestions
            ]);
        } catch (Exception) {
            Flash::danger('Une erreur est survenue, veuillez réessayer');
            header ("Location: /");
        }
    }
}
