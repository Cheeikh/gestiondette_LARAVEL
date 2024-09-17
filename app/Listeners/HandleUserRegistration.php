<?php

namespace App\Listeners;

use App\Events\UserRegistering;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Interfaces\UploadInterface;

class HandleUserRegistration
{
    protected $uploadService;

    public function __construct(UploadInterface $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function handle(UserRegistering $event)
    {
        $user = $event->user;
        $photo = $event->photo;

        if ($photo) {
            $photoCloudUrl = $this->uploadService->upload($photo);
            $photoLocalPath = $photo->store('uploads', 'public');
            $photoLocalUrl = Storage::url($photoLocalPath);

            $user->photo_cloud = $photoCloudUrl;
            $user->photo_local = $photoLocalUrl;
        } else {
            $user->photo_cloud = 'https://res.cloudinary.com/dvy0saazc/image/upload/v1725507238/uploads/ytk2cqqcoxvgcqap7lm5.jpg';
            $user->photo_local = null;
        }

        $user->password = Hash::make($user->password);

        $user->save();
    }
}
