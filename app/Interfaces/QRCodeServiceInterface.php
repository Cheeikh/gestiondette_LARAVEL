<?php

namespace App\Interfaces;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

interface QRCodeServiceInterface
{
    public function generate($data);

}
