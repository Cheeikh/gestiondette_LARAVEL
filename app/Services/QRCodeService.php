<?php

namespace App\Services;

use App\Interfaces\QRCodeServiceInterface;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeService implements QRCodeServiceInterface
{
    public function generate($data)
    {
        return QrCode::format('png')->size(200)->generate($data);
    }
}
