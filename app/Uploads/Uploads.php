<?php

namespace App\Uploads;

use App\Interfaces\UploadInterface;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class Uploads implements UploadInterface
{
    public function upload($file): string
    {
        $result = Cloudinary::upload($file->getRealPath(), [
            'folder' => 'uploads'
        ]);

        return $result->getSecurePath();
    }
}
