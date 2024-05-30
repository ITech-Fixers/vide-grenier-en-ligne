<?php

namespace App\Models;

class UserRegister
{
    public static function validate($data): array
    {
        $errors = [];

        if (empty($data['password']) || empty($data['password-check'])) {
            $errors[] = 'Le mot de passe ne peut pas être vide';
        }

        if (empty($data['username'])) {
            $errors[] = 'Le nom d\'utilisateur ne peut pas être vide';
        }

        if (empty($data['email'])) {
            $errors[] = 'L\'email ne peut pas être vide';
        }

        if ($data['password'] !== $data['password-check']) {
            $errors[] = 'Les mots de passe ne correspondent pas';
        }

        if (strlen($data['password']) < 6) {
            $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'L\'email n\'est pas valide';
        }

        return $errors;
    }
}