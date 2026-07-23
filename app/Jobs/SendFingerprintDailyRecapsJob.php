<?php

namespace App\Jobs;

use App\Services\FingerprintWhatsappNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendFingerprintDailyRecapsJob implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        $this->onQueue('fingerprint');
    }

    public int $timeout = 900;

    public int $tries = 2;

    public function handle(FingerprintWhatsappNotificationService $service): void
    {
        Log::info('Pengiriman rekap fingerprint WhatsApp selesai.', $service->sendToday());
    }
}
