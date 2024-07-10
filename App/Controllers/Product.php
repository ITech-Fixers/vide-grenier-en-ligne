<?php

namespace App\Controllers;

use App\Exception\ArticleNotFoundException;
use App\Exception\CityNotFoundException;
use App\Exception\MailerException;
use App\Exception\UserNotFoundException;
use App\Exception\ValidationException;
use App\Models\Articles;
use App\Models\Cities;
use App\Models\User;
use App\Service\Validation\ArticleAdd;
use App\Service\Validation\ContactMessage;
use App\Utility\Flash;
use App\Utility\Mailer;
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

            if (empty($article)) {
                throw new ArticleNotFoundException('L\'article n\'existe pas');
            }

            View::renderTemplate('Product/Show.html', [
                'article' => $article[0],
                'suggestions' => $suggestions
            ]);
        } catch (ArticleNotFoundException $e) {
            Flash::danger($e->getMessage());
            header ("Location: /");
        } catch (Exception) {
            Flash::danger('Une erreur est survenue, veuillez réessayer');
            header ("Location: /");
        }
    }

    /**
     * Affiche la page de contact
     * @return void
     * @throws Exception
     */
    public function contactAction(): void
    {
        $articleId = $this->route_params['id'];
        $article = Articles::getOne($articleId);

        try {
            if (empty($article)) {
                throw new ArticleNotFoundException('L\'article n\'existe pas');
            }

            $owner = User::getByArticle($articleId);

            if (empty($owner)) {
                throw new UserNotFoundException('Le détenteur de l\'article n\'existe pas');
            }

            View::renderTemplate('Product/Contact.html', [
                'owner' => $owner,
                'article' => $article[0]
            ]);
        } catch (ArticleNotFoundException|UserNotFoundException $e) {
            Flash::danger($e->getMessage());
            header ("Location: /");
        } catch (Exception $e) {
            Flash::danger('Une erreur est survenue, veuillez réessayer');
            header ("Location: /");
        }
    }

    /**
     * Envoie un message à l'utilisateur
     * @return void
     * @throws Exception
     */
    public function sendMessageAction(): void
    {
        $articleId = $this->route_params['id'];

        try {
            $article = Articles::getOne($articleId);

            if (empty($article)) {
                throw new ArticleNotFoundException('L\'article n\'existe pas');
            }

            $owner = User::getByArticle($articleId);
            $user = User::getById($_SESSION['user']['id']);

            if (empty($owner) || empty($user)) {
                empty($owner) ? $message = 'Le détenteur de l\'article n\'existe pas' : $message = 'L\'utilisateur n\'existe pas';
                throw new UserNotFoundException($message);
            }

            $request = $_POST;
            $errors = ContactMessage::validate($request);

            if (!empty($errors)) {
                throw new ValidationException(implode('<br>', $errors));
            }

            Mailer::send(
                $user['username'],
                $user['email'],
                $owner['username'],
                $owner['email'],
                $request['libellé'],
                htmlspecialchars($request['message'])
            );

            Articles::addOneContact($articleId);

            Flash::success('Message envoyé avec succès');
            header ("Location: /product/$articleId");
        } catch (ArticleNotFoundException|ValidationException|UserNotFoundException|MailerException $e) {
            Flash::danger($e->getMessage());
            header ("Location: /product/$articleId");
        } catch (Exception) {
            Flash::danger('Une erreur est survenue, veuillez réessayer');
            header ("Location: /product/$articleId");
        }
    }
}
