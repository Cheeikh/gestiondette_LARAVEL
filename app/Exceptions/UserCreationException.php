<?php

namespace App\Exceptions;

use Exception;

class UserCreationException extends Exception
{
    public function __construct($message = "Erreur lors de la création de l'utilisateur", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        return response()->json([
            'error' => 'User Creation Error',
            'message' => $this->getMessage(),
        ], 422);
    }
}