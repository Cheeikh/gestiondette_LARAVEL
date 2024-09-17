<?php

namespace App\Jobs;

use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use App\Interfaces\UploadInterface;

class UploadPhotoToCloudJob implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    public $user;
    public $photoPath;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param string $photoPath
     */
    public function __construct(User $user, string $photoPath)
    {
        $this->user = $user;
        $this->photoPath = $photoPath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(UploadInterface $uploadService)
    {
        try {
            // Récupérer le fichier depuis le chemin local
            $photo = Storage::disk('public')->get($this->photoPath);

            // Upload la photo sur le cloud
            $photoCloudUrl = $uploadService->upload($photo);

            // Mettre à jour l'utilisateur avec le lien cloud
            $this->user->update([
                'photo_cloud' => $photoCloudUrl,
            ]);

            // Supprimer le fichier local après l'upload
            Storage::disk('public')->delete($this->photoPath);

            Log::info("Photo uploaded successfully for user ID {$this->user->id}");
        } catch (Exception $e) {
            Log::error("Photo upload failed for user ID {$this->user->id}: {$e->getMessage()}");
            // Relancer le job après 5 minutes si l'upload échoue
            $this->release(300); // 300 secondes = 5 minutes
        }
    }
}
