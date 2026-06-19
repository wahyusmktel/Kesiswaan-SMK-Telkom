<?php

namespace App\Notifications;

use App\Models\AssessmentPeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssessmentAvailableNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly AssessmentPeriod $period)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Penilaian Baru',
            'message' => 'Penilaian ' . $this->period->title . ' sudah tersedia untuk diisi.',
            'url' => route('penilaian.index'),
        ];
    }
}
