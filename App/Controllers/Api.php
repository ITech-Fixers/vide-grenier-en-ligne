<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Models\Cities;
use Core\Controller;
use Exception;
use App\Models\User;
use OpenApi\Annotations as OA;


/**
 * @OA\Info(
 *     title="Vide grenier en ligne API",
 *     version="1.0.0",
 *     description="Une API pour gérer les articles et les villes du vide grenier en ligne"
 * )
 */
class Api extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Affiche la liste des articles / produits pour la page d'accueil",
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         required=false,
     *         description="Critère de tri des produits (views ou date)",
     *         @OA\Schema(
     *             type="string",
     *             enum={"views", "date"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="latitude",
     *         in="query",
     *         required=false,
     *         description="Latitude pour la géolocalisation",
     *         @OA\Schema(
     *             type="number",
     *             format="float"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="longitude",
     *         in="query",
     *         required=false,
     *         description="Longitude pour la géolocalisation",
     *         @OA\Schema(
     *             type="number",
     *             format="float"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="radius",
     *         in="query",
     *         required=false,
     *         description="Rayon en kilomètres pour la géolocalisation",
     *         @OA\Schema(
     *             type="number",
     *             format="float"
     *         )
     *     ),
     *     @OA\Parameter(
     *          name="search",
     *          in="query",
     *          required=false,
     *          description="Recherche dans les articles",
     *          @OA\Schema(
     *              type="string"
     *         )
     *    ),
     *     @OA\Response(
     *         response=200,
     *         description="Fetches products",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="published_date", type="string"),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="views", type="integer"),
     *                 @OA\Property(property="picture", type="string"),
     *                 @OA\Property(property="ville_id", type="integer")
     *             )
     *         )
     *     )
     * )
     *
     * @throws Exception
     */
    public function ProductsAction(): void
    {
        $sort = $_GET['sort'] ?? null;
        $latitude = $_GET['latitude'] ?? null;
        $longitude = $_GET['longitude'] ?? null;
        $radius = $_GET['radius'] ?? null;
        $search = $_GET['search'] ?? null;

        if ($latitude && $longitude && $radius) {
            $articles = Articles::getNearby(floatval($latitude), floatval($longitude), floatval($radius));
        } else if ($sort) {
            $articles = Articles::getAll($sort);
        } else if ($search) {
            $articles = Articles::search($search);
        } else {
            $articles = Articles::getAll('');
        }

        header('Content-Type: application/json');
        echo json_encode($articles);
    }


    /**
     * @OA\Get(
     *     path="/api/userproducts",
     *     summary="Affiche la liste des articles / produits pour un utilisateur",
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         required=false,
     *         description="Critère de tri des produits (views ou date)",
     *         @OA\Schema(
     *             type="string",
     *             enum={"views", "date"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Fetches user products",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="published_date", type="string"),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="views", type="integer"),
     *                 @OA\Property(property="picture", type="string"),
     *                 @OA\Property(property="ville_id", type="integer")
     *             )
     *         )
     *     )
     * )
     *
     * @throws Exception
     */
    public function UserProductsAction(): void
    {
        $query = $_GET['sort'];
        $user_id = $_SESSION['user']['id'];

        $articles = Articles::getAllByUser($query, $user_id);

        header('Content-Type: application/json');
        echo json_encode($articles);
    }

    /**
     * @OA\Get(
     *     path="/api/cities",
     *     summary="Recherche dans la liste des villes",
     *     @OA\Response(
     *         response=200,
     *         description="Search cities",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string")
     *             )
     *         )
     *     )
     * )
     *
     * @throws Exception
     */
    public function CitiesAction(): void
    {
        $cities = Cities::search($_GET['query']);

        header('Content-Type: application/json');
        echo json_encode($cities);
    }

    /**
     * @OA\Get(
     *     path="/donatePerUser",
     *     summary="Affiche le nombre d'articles donnés par utilisateur",
     *     @OA\Response(
     *         response=200,
     *         description="Fetches statistics",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="username", type="string"),
     *                 @OA\Property(property="nombre_d_articles", type="integer")
     *             )
     *         )
     *     )
     * )
     *
     * @throws Exception
     */
    public function DonatePerUserAction(): void
    {
        $statistics = Articles::donatePerUser();

        header('Content-Type: application/json');
        echo json_encode($statistics);
    }

    /**
     * @OA\Get(
     *     path="/donatePerCity",
     *     summary="Affiche le nombre d'articles donnés par ville",
     *     @OA\Response(
     *         response=200,
     *         description="Fetches statistics",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="city", type="string"),
     *                 @OA\Property(property="nombre_d_articles", type="integer")
     *             )
     *         )
     *     )
     * )
     *
     * @throws Exception
     */
    public function DonatePerCityAction(): void
    {
        $statistics = Articles::donatePerCity();

        header('Content-Type: application/json');
        echo json_encode($statistics);
    }


    /**
     * @OA\Get(
     *     path="/mostViewed",
     *     summary="Affiche les articles les plus vus",
     *     @OA\Response(
     *         response=200,
     *         description="Fetches statistics",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="views", type="integer")
     *             )
     *         )
     *     )
     * )
     */
    public function MostViewedAction(): void
    {
        $statistics = Articles::mostViewed();

        header('Content-Type: application/json');
        echo json_encode($statistics);
    }


    /**
     * @OA\Get(
     *     path="/mostContacted",
     *     summary="Affiche les articles qui découle sur un contact",
     *     @OA\Response(
     *         response=200,
     *         description="Fetches statistics",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="contact_count", type="integer")
     *             )
     *         )
     *     )
     * )
     */
    public function MostContactedAction(): void
    {
        $statistics = Articles::mostContacted();

        header('Content-Type: application/json');
        echo json_encode($statistics);
    }

    /**
     * @OA\Get(
     *     path="/statistics",
     *     summary="Affiche le nombre d'utilisateurs",
     *     @OA\Response(
     *         response=200,
     *         description="Fetches statistics",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="user_count", type="integer"),
 *                     @OA\Property(property="online_article_count", type="integer"),
     *                 @OA\Property(property="gived_article_count", type="integer"),
     *                 @OA\Property(property="total_article_count", type="integer")
     *             )
     *         )
     *     )
     * )
     */
    public function StatisticsAction(): void
    {
        $usersCount = User::getUserCount();
        $onlineArticles = Articles::getOnlineArticleCount();
        $givenArticles = Articles::getDonatedArticleCount();
        $totalArticles = Articles::getTotalArticleCount();

        $statistics = [
            'user_count' => $usersCount,
            'online_article_count' => $onlineArticles,
            'given_article_count' => $givenArticles,
            'total_article_count' => $totalArticles
        ];

        header('Content-Type: application/json');
        echo json_encode($statistics);
    }
}
