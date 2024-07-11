<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

class FileFormatException extends Exception
{
    protected mixed $errors;

    public function __construct($message = "This file extension is not allowed. Please upload a JPEG or PNG file", $code = 0, $errors = [], Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}