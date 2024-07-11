<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config;
use App\Exception\ArticleNotFoundException;
use App\Exception\CityNotFoundException;
use App\Exception\FileFormatException;
use App\Exception\MailerException;
use App\Exception\PermissionException;
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
                $file = $_FILES['picture'];

                Upload::validateFileExtension($file);

                if (!empty($errors)) {
                    throw new ValidationException(implode('<br>', $errors));
                }

                $city = Cities::getById((int) $request['city_id']);

                if (empty($city)) {
                    throw new CityNotFoundException('La ville sélectionnée n\'existe pas');
                }

                $request['user_id'] = (int) $_SESSION['user']['id'];
                $id = (int) Articles::save($request);

                $pictureName = Upload::uploadFile($file, $id . '_' . uniqid());
                Articles::attachPicture($id, $pictureName);

                Flash::success('Produit ajouté avec succès');
                $this->route_params['id'] = $id;
                $this->showAction();
            } catch (ValidationException|CityNotFoundException|FileFormatException $e) {
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
     * Affiche la page de modification d'un produit
     * @return void
     * @throws Exception
     */
    public function showEditAction(): void
    {
        $id = (int) $this->route_params['id'];

        try {

            if (!User::hasArticle($id, (int) $_SESSION['user']['id'])) {
                throw new PermissionException("Vous n'êtes pas autorisé à modifier cet article");
            }

            $article = Articles::getById($id);

            if (empty($article)) {
                throw new ArticleNotFoundException('L\'article n\'existe pas');
            }

            View::renderTemplate('Product/Add.html', [
                'article' => $article[0],
                'update' => true,
            ]);
        } catch (ArticleNotFoundException|PermissionException $e) {
            Flash::danger($e->getMessage());
            header ("Location: /");
        } catch (Exception) {
            Flash::danger('Une erreur est survenue, veuillez réessayer');
            header ("Location: /");
        }
    }

    /**
     * Marque un produit comme donné
     * @return void
     * @throws Exception
     */
    public function giveAction(): void
    {
        $id = (int) $this->route_params['id'];

        try {
            $article = Articles::getById($id);

            if (empty($article)) {
                throw new ArticleNotFoundException('L\'article n\'existe pas');
            }

            if (!User::hasArticle($id, (int) $_SESSION['user']['id'])) {
                throw new PermissionException("Vous n'êtes pas autorisé à donner cet article");
            }

            Articles::give($id);
            Articles::deactivate($id);

            Flash::success('Article donné avec succès');
            header ("Location: /");
        } catch (ArticleNotFoundException|PermissionException $e) {
            Flash::danger($e->getMessage());
            header ("Location: /");
        } catch (Exception) {
            Flash::danger('Une erreur est survenue, veuillez réessayer');
            header ("Location: /");
        }
    }

    /**
     * Met à jour un produit
     * @return void
     * @throws Exception
     */
    public function updateAction(): void
    {
        $id = (int) $this->route_params['id'];

        try {
            if (!User::hasArticle($id, (int) $_SESSION['user']['id'])) {
                throw new PermissionException("Vous n'êtes pas autorisé à modifier cet article");
            }

            $article = Articles::getById($id);

            if (empty($article)) {
                throw new ArticleNotFoundException('L\'article n\'existe pas');
            }

            $request = $_POST;
            $errors = ArticleAdd::validate($request);

            if (!empty($errors)) {
                throw new ValidationException(implode('<br>', $errors));
            }

            $city = Cities::getById((int) $request['city_id']);

            if (empty($city)) {
                throw new CityNotFoundException('La ville sélectionnée n\'existe pas');
            }

            $request['user_id'] = (int) $_SESSION['user']['id'];
            Articles::update($id, $request);

            Flash::success('Produit modifié avec succès');
            $this->route_params['id'] = $id;
            $this->showAction();
        } catch (ArticleNotFoundException|PermissionException|ValidationException|CityNotFoundException $e) {
            Flash::danger($e->getMessage());
            header ("Location: /");
        } catch (Exception) {
            Flash::danger('Une erreur est survenue, veuillez réessayer');
            header ("Location: /");
        }
    }

    /**
     * Active un produit
     * @return void
     * @throws Exception
     */
    public function activateAction(): void
    {
        $id = (int) $this->route_params['id'];

        try {
            if (!User::hasArticle($id, (int) $_SESSION['user']['id'])) {
                throw new PermissionException("Vous n'êtes pas autorisé à activer cet article");
            }

            $article = Articles::getById($id);

            if (empty($article)) {
                throw new ArticleNotFoundException('L\'article n\'existe pas');
            }

            Articles::activate($id);

            Flash::success('Article activé avec succès');
            header ("Location: /");
        } catch (ArticleNotFoundException|PermissionException $e) {
            Flash::danger($e->getMessage());
            header ("Location: /");
        } catch (Exception) {
            Flash::danger('Une erreur est survenue, veuillez réessayer');
            header ("Location: /");
        }
    }

    /**
     * Désactive un produit
     * @return void
     * @throws Exception
     */
    public function deactivateAction(): void
    {
        $id = (int) $this->route_params['id'];

        try {
            if (!User::hasArticle($id, (int) $_SESSION['user']['id'])) {
                throw new PermissionException("Vous n'êtes pas autorisé à désactiver cet article");
            }

            $article = Articles::getByIdActivated($id);

            if (empty($article)) {
                throw new ArticleNotFoundException('L\'article n\'existe pas');
            }

            Articles::deactivate($id);

            Flash::success('Article désactivé avec succès');
            header ("Location: /");
        } catch (ArticleNotFoundException|PermissionException $e) {
            Flash::danger($e->getMessage());
            header ("Location: /");
        } catch (Exception) {
            Flash::danger('Une erreur est survenue, veuillez réessayer');
            header ("Location: /");
        }
    }

    /**
     * Affiche la page d'un produit
     * @return void
     * @throws Exception
     */
    public function showAction(): void
    {
        $id = (int) $this->route_params['id'];

        try {
            Articles::addOneView($id);
            $suggestions = Articles::getSuggest();

            if (isset($_SESSION['user']) && User::hasArticle($id, (int) $_SESSION['user']['id'])) {
                $article = Articles::getById($id);
                $isAuthor = true;
            } else {
                $article = Articles::getByIdActivated($id);
                $isAuthor = false;
            }

            if (empty($article)) {
                throw new ArticleNotFoundException('L\'article n\'existe pas');
            }

            View::renderTemplate('Product/Show.html', [
                'article' => $article[0],
                'suggestions' => $suggestions,
                'isAuthor' => $isAuthor
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
        $articleId = (int) $this->route_params['id'];
        $article = Articles::getByIdActivated($articleId);

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
        $articleId = (int) $this->route_params['id'];

        try {

            $article = Articles::getByIdActivated($articleId);

            if (empty($article)) {
                throw new ArticleNotFoundException('L\'article n\'existe pas');
            }

            $owner = User::getByArticle($articleId);
            $user = User::getById((int) $_SESSION['user']['id']);

            if (empty($owner) || empty($user)) {
                empty($owner) ? $message = 'Le détenteur de l\'article n\'existe pas' : $message = 'L\'utilisateur n\'existe pas';
                throw new UserNotFoundException($message);
            }

            $request = $_POST;
            $errors = ContactMessage::validate($request);

            if (!empty($errors)) {
                throw new ValidationException(implode('<br>', $errors));
            }

            $baseURL = $_ENV['BASE_URL'];

            Mailer::send(
                $user['username'],
                $user['email'],
                $owner['username'],
                $owner['email'],
                $article[0]['name'],
                $baseURL . "storage/". $article[0]['picture'],
                htmlspecialchars($request['message']),
                $baseURL . "product/" . $articleId
            );

            Articles::addOneContact($articleId);

            Flash::success('Message envoyé avec succès');
            header ("Location: /product/$articleId");
        } catch (ArticleNotFoundException|ValidationException|UserNotFoundException|MailerException $e) {
            Flash::danger($e->getMessage());
            header ("Location: /product/$articleId");
        } catch (Exception $e) {
            Flash::danger('Une erreur est survenue, veuillez réessayer');
            header ("Location: /product/$articleId");
        }
    }
}
