<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MitraVerificationStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $status,
        public ?string $alasan = null
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $isApproved = $this->status === 'approved';
        $targetUrl = $this->resolveUrl($isApproved ? '/mitra/dashboard' : '/mitra/profil');

        $mail = (new MailMessage)
            ->subject('[SportPlace] Status Verifikasi Mitra: ' . ($isApproved ? 'Disetujui' : 'Ditolak'))
            ->greeting('Halo, ' . ($notifiable->name ?? 'Mitra SportPlace') . '!')
            ->line($isApproved
                ? 'Selamat! Berkas dan data pendaftaran kemitraan Anda telah diperiksa dan disetujui oleh tim verifikator SportPlace.'
                : 'Mohon maaf, pendaftaran kemitraan Anda belum dapat disetujui pada peninjauan kali ini.');

        if (! $isApproved && $this->alasan) {
            $mail->line('Alasan Penolakan: ' . $this->alasan)
                ->line('Silakan periksa kembali data atau dokumen legalitas Anda dan lakukan pengajuan ulang.');
        }

        return $mail
            ->action($isApproved ? 'Buka Dashboard Mitra' : 'Tinjau & Perbaiki Dokumen', $targetUrl)
            ->line('Terima kasih telah bermitra dengan SportPlace.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $isApproved = $this->status === 'approved';

        return [
            'type' => 'mitra_verification',
            'status' => $this->status,
            'title' => $isApproved ? 'Pendaftaran Mitra Disetujui' : 'Pendaftaran Mitra Ditolak',
            'message' => $isApproved
                ? 'Selamat! Akun kemitraan Anda telah disetujui. Anda kini dapat mulai menambahkan venue dan lapangan.'
                : 'Pendaftaran kemitraan Anda ditolak: ' . ($this->alasan ?? 'Perlu perbaikan data/dokumen.'),
            'alasan' => $this->alasan,
            'url' => $this->resolveUrl($isApproved ? '/mitra/dashboard' : '/mitra/profil'),
        ];
    }

    private function resolveUrl(string $path): string
    {
        return function_exists('app') && app()->bound('url') ? url($path) : $path;
    }
}
