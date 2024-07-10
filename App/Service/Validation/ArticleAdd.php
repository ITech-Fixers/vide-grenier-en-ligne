<?php

namespace App\Service\Validation;

class ArticleAdd
{
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