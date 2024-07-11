<?php

namespace App\Service\Validation;

class ContactMessage
{

    /**
     * Valide les données du formulaire de contact
     *
     * @param array $request
     *
     * @return array un tableau contenant les erreurs
     */
    public static function validate(array $request): array
    {
        $errors = [];

        if (empty($request['message'])) {
            $errors[] = 'Le message est obligatoire';
        }

        if (empty($request['owner_id'])) {
            $errors[] = 'L\'id du propriétaire est obligatoire';
        }

        return $errors;
    }
}