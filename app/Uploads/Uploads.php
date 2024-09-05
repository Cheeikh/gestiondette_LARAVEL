<?php

namespace App\Uploads;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class Uploads implements UploadInterface
{
    public function upload($file): string
    {
        $result = Cloudinary::upload($file->getRealPath(), [
            'folder' => 'uploads',
            'transformation' => [
                'width' => 300,
                'height' => 300,
                'crop' => 'fit'
            ],
        ]);

        return $result->getSecurePath(); // Retourne le lien sécurisé de l'image
    }
}
