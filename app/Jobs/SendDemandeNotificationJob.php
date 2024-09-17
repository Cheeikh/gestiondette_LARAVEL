<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Demande;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\DemandeCreatedMailable;
use App\Notifications\DemandeCreatedNotification;

class SendDemandeNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $demande;

    public function __construct(Demande $demande)
    {
        $this->demande = $demande;
    }

    public function handle()
    {
        $users = User::where('role_id', 2)->get();

        foreach ($users as $user) {
            try {
                // Send email
                Mail::to($user->email)->send(new DemandeCreatedMailable($this->demande));
                Log::info("Email sent to {$user->email}");

                // Send notification
                $notification = new DemandeCreatedNotification($this->demande);
                $user->notify($notification);
                Log::info("Notification sent to {$user->email}");
            } catch (\Exception $e) {
                Log::error("Failed to send notification or email to {$user->email}: " . $e->getMessage());
            }
        }
    }
}
