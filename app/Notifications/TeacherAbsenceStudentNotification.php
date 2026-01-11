<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TeacherAbsenceStudentNotification extends Notification
{
    use Queueable;

    protected $izin;
    protected $jadwal;

    public function __construct($izin, $jadwal)
    {
        $this->izin = $izin;
        $this->jadwal = $jadwal;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $teacherName = $this->izin->guru->nama_lengkap;
        $className = $this->jadwal->rombel->kelas->nama_kelas;
        $subjectName = $this->jadwal->mataPelajaran->nama_mapel;
        $jamKe = $this->jadwal->jam_ke;

        $hasMaterial = $this->jadwal->pivot->lms_material_id ? true : false;
        $hasAssignment = $this->jadwal->pivot->lms_assignment_id ? true : false;

        $message = "Bapak/Ibu $teacherName tidak dapat mengajar di kelas $className pada Jam ke-$jamKe ($subjectName). ";
        
        if ($hasMaterial && $hasAssignment) {
            $message .= "Silakan pelajari Materi dan kerjakan Tugas yang telah dilampirkan.";
        } elseif ($hasMaterial) {
            $message .= "Silakan pelajari Materi yang telah dilampirkan.";
        } elseif ($hasAssignment) {
            $message .= "Silakan kerjakan Tugas yang telah dilampirkan.";
        }

        return [
            'izin_id' => $this->izin->id,
            'type' => 'teacher_absence',
            'guru_nama' => $teacherName,
            'message' => $message,
            'url' => route('shared.notifications.index'),
            'material_id' => $this->jadwal->pivot->lms_material_id,
            'assignment_id' => $this->jadwal->pivot->lms_assignment_id,
        ];
    }
}
