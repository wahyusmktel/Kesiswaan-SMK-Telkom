<?php

namespace App\Jobs;

use App\Services\FingerprintWhatsappNotificationService;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendFingerprintDailyRecapsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public ?string $notificationDate = null, public bool $manual = false)
    {
        $this->onQueue('fingerprint');
    }

    public int $timeout = 900;

    public int $tries = 2;

    public function handle(FingerprintWhatsappNotificationService $service): void
    {
        $date = $this->notificationDate ? Carbon::parse($this->notificationDate) : today();
        Log::info('Pengiriman notifikasi fingerprint WhatsApp selesai.', [
            'date' => $date->toDateString(),
            'manual' => $this->manual,
            'recaps' => $service->sendToday($date, $this->manual),
            'reminders' => $service->sendRemindersToday($date, $this->manual),
        ]);
    }
}
