<?php

namespace App\Listeners;

use App\Events\ClientCreated;
use App\Jobs\SendClientWelcomeEmailJob;
use App\Services\PDFService;
use App\Services\QRCodeService;

class SendClientWelcomeEmail
{
    protected $pdfService;
    protected $qrCodeService;

    public function __construct(PDFService $pdfService, QRCodeService $qrCodeService)
    {
        $this->pdfService = $pdfService;
        $this->qrCodeService = $qrCodeService;
    }

    public function handle(ClientCreated $event)
    {
        dispatch(new SendClientWelcomeEmailJob($event->client, $this->pdfService, $this->qrCodeService));
    }
}
