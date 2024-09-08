<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeService
{
    public function generate($data)
    {
        return QrCode::format('png')->size(200)->generate($data);
    }
}
