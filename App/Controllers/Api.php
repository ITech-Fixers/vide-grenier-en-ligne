<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Models\Cities;
use Core\Controller;
use Exception;
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
}
