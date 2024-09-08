<?php

namespace App\Jobs;

use App\Models\Client;
use App\Services\PDFService;
use App\Services\QRCodeService;
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
        $qrCode = $this->qrCodeService->generate($this->client->telephone);
        $photoPath = $this->client->user && $this->client->user->photo_local ?
            public_path($this->client->user->photo_local) :
            public_path('images/Profile-Avatar-PNG.png');

        $photoBase64 = base64_encode(file_get_contents($photoPath));

        $pdf = $this->pdfService->createPDF('fidelite.carte', [
            'client' => $this->client,
            'qrCode' => $qrCode,
            'photo' => $photoBase64
        ]);

        Mail::send('emails.client_fidelite', ['client' => $this->client], function ($message) use ($pdf) {
            $message->to($this->client->email)
                ->subject('Votre carte de fidélité')
                ->attachData($pdf->output(), 'carte_fidelite.pdf');
        });
    }
}
