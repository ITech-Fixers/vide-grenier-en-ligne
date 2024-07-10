<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Models\Cities;
use Core\Controller;
use Exception;


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
     *     path="/products",
     *     summary="Affiche la liste des articles / produits pour la page d'accueil",
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
        $query = $_GET['sort'];

        $articles = Articles::getAll($query);

        header('Content-Type: application/json');
        echo json_encode($articles);
    }

    /**
     * @OA\Get(
     *     path="/cities",
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
     *     response=200,
     *     description="Fetches statistics",
     *     @OA\JsonContent(
     *     type="array",
     *     @OA\Items(
     *     type="object",
     *     @OA\Property(property="name", type="string"),
     *     @OA\Property(property="views", type="integer")
     *    )
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
     *     response=200,
     *     description="Fetches statistics",
     *     @OA\JsonContent(
     *     type="array",
     *     @OA\Items(
     *     type="object",
     *     @OA\Property(property="name", type="string"),
     *     @OA\Property(property="contacts", type="integer")
     *    )
     */
}
