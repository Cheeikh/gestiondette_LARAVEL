<?php

namespace App\Services;

use App\Interfaces\PDFServiceInterface;
use Barryvdh\DomPDF\Facade\Pdf;

class PDFService implements PDFServiceInterface
{

    public function createPDF($view, $data)
    {
        return Pdf::loadView($view, $data);
    }
}
