<?php

namespace App\Uploads;

interface UploadInterface
{
    public function upload($file): string;
}
