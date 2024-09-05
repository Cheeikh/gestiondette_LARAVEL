<?php

namespace App\Exceptions;

use Exception;

class ClientCreationException extends Exception
{
    public function __construct($message = "Erreur lors de la création du client", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        return response()->json([
            'error' => 'Client Creation Error',
            'message' => $this->getMessage(),
        ], 422);
    }
}