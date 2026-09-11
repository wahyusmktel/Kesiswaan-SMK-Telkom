<?php

namespace App\Jobs;

use App\Services\FingerprintWhatsappNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendFingerprintCheckinRemindersJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 300;

    public int $tries = 2;

    public function __construct()
    {
        $this->onQueue('fingerprint');
    }

    public function handle(FingerprintWhatsappNotificationService $service): void
    {
        Log::info('Pemeriksaan pengingat check-in fingerprint selesai.', $service->sendCheckinRemindersNow());
    }
}
