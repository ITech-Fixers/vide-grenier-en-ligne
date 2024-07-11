<?php

namespace App\Service\Validation;

class ArticleAdd
{
    /**
     * Valide les données du formulaire d'ajout d'article
     *
     * @param array $request
     *
     * @return array un tableau contenant les erreurs
     */
    public static function validate(array $request): array
    {
        $errors = [];

        if (empty($request['name'])) {
            $errors[] = 'Le nom est obligatoire';
        }

        if (empty($request['description'])) {
            $errors[] = 'La description est obligatoire';
        }

        if (empty($request['city_id'])) {
            $errors[] = 'La ville est obligatoire';
        }

        return $errors;
    }
}