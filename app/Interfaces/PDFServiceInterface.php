<?php

namespace App\Interfaces;

interface PDFServiceInterface
{
    public function createPDF($view, $data);

}
