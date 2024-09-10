<?php

namespace App\Jobs;

use App\Models\Client;
use App\Interfaces\PDFServiceInterface as PDFService;
use App\Interfaces\QRCodeServiceInterface as QRCodeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendClientWelcomeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $client;
    protected $pdfService;
    protected $qrCodeService;

    public function __construct(Client $client, PDFService $pdfService, QRCodeService $qrCodeService)
    {
        $this->client = $client;
        $this->pdfService = $pdfService;
        $this->qrCodeService = $qrCodeService;
    }

    public function handle()
    {
        // Générer le QR code pour le client
        $qrCode = $this->qrCodeService->generate($this->client->telephone);

        // Déterminer le chemin de la photo
        $photoPath = $this->client->user && $this->client->user->photo_local ?
            public_path($this->client->user->photo_local) :
            public_path('images/Profile-Avatar-PNG.png');

        // Encodage de la photo en base64
        $photoBase64 = base64_encode(file_get_contents($photoPath));

        // Générer le PDF en passant les données à la vue
        $pdf = $this->pdfService->createPDF('fidelite.carte', [
            'client' => $this->client,
            'qrCode' => $qrCode,
            'photo' => $photoBase64
        ]);

        // Envoyer l'e-mail avec le PDF en pièce jointe
        Mail::send('emails.client_fidelite', ['client' => $this->client], function ($message) use ($pdf) {
            $message->to($this->client->email)
                ->subject('Votre carte de fidélité')
                ->attachData($pdf->output(), 'carte_fidelite.pdf');
        });
    }
}
