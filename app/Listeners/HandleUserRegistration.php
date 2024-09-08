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

        // Gestion de la photo de profil
        if ($photo) {
            // Upload sur le cloud (Cloudinary)
            $photoCloudUrl = $this->uploadService->upload($photo);

            // Stockage local
            $photoLocalPath = $photo->store('uploads', 'public');  // Stockage local dans "storage/app/public/uploads"
            $photoLocalUrl = Storage::url($photoLocalPath);  // Générer l'URL publique pour l'image locale

            // Enregistrer les deux URLs (Cloud et Local)
            $user->photo_cloud = $photoCloudUrl;
            $user->photo_local = $photoLocalUrl;
        } else {
            $user->photo_cloud = 'https://res.cloudinary.com/dvy0saazc/image/upload/v1725507238/uploads/ytk2cqqcoxvgcqap7lm5.jpg';
            $user->photo_local = null;
        }

        // Hachage du mot de passe
        $user->password = Hash::make($user->password);

        // Sauvegarder les modifications
        $user->save();
    }
}
