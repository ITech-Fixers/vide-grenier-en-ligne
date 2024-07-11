<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

class CityNotFoundException extends Exception
{
    protected mixed $errors;

    public function __construct($message = "City not found", $errors = [], $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}