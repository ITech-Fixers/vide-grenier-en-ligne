<?php

namespace App\Exception;

use Exception;

class PermissionException extends Exception
{
    protected mixed $errors;

    public function __construct($message = "Permission denied", $errors = [], $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}