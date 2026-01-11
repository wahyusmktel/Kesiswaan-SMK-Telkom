<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PengajuanIzinGuruNotification extends Notification
{
    use Queueable;

    public $izin;
    public $type;
    public $message;
    public $url;

    /**
     * Create a new notification instance.
     *
     * @param  mixed  $izin
     * @param  string  $type
     * @param  string  $message
     * @param  string  $url
     * @return void
     */
    public function __construct($izin, $type, $message, $url)
    {
        $this->izin = $izin;
        $this->type = $type;
        $this->message = $message;
        $this->url = $url;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'izin_id' => $this->izin->id,
            'type' => $this->type,
            'guru_nama' => $this->izin->guru->nama_lengkap,
            'message' => $this->message,
            'url' => $this->url,
        ];
    }
}
