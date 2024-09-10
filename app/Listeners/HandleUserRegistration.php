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
        $photo = $event->photo;  // Utilisez $photo comme défini dans l'événement

        if ($photo) {
            // Upload de la photo
            $photoCloudUrl = $this->uploadService->upload($photo);
            $photoLocalPath = $photo->store('uploads', 'public');
            $photoLocalUrl = Storage::url($photoLocalPath);

            $user->photo_cloud = $photoCloudUrl;
            $user->photo_local = $photoLocalUrl;
        } else {
            // Photo par défaut si aucune n'est fournie
            $user->photo_cloud = 'https://res.cloudinary.com/dvy0saazc/image/upload/v1725507238/uploads/ytk2cqqcoxvgcqap7lm5.jpg';
            $user->photo_local = null;
        }

        // Hachage du mot de passe
        $user->password = Hash::make($user->password);

        // Sauvegarder l'utilisateur avec les informations de photo et mot de passe
        $user->save();
    }
}
