<?php

namespace App\Exceptions;

use Exception;

class ClientNotFoundException extends Exception
{
    public function __construct($message = "Client non trouvé", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        return response()->json([
            'error' => 'Client Not Found',
            'message' => $this->getMessage(),
        ], 404);
    }
}