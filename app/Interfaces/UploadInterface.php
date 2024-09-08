<?php

namespace App\Interfaces;

interface UploadInterface
{
    public function upload($file): string;
}
