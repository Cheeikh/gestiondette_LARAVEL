<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class PDFService
{

    public function createPDF($view, $data)
    {
        // Generate the PDF from the view with the provided data.
        return Pdf::loadView($view, $data);
    }
}
