<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ListingModerationStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $jenis,
        public string $name,
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
        $isApproved = $this->status === 'aktif';
        $label = ucfirst($this->jenis);
        $targetUrl = $this->resolveUrl('/mitra/venues');

        $mail = (new MailMessage)
            ->subject("[SportPlace] Moderasi {$label} \"{$this->name}\": " . ($isApproved ? 'Disetujui' : 'Ditolak'))
            ->greeting('Halo, ' . ($notifiable->name ?? 'Mitra SportPlace') . '!')
            ->line($isApproved
                ? "Selamat! {$label} \"{$this->name}\" telah diverifikasi dan disetujui untuk ditayangkan secara publik."
                : "Mohon maaf, pengajuan {$label} \"{$this->name}\" ditolak pada proses moderasi admin.");

        if (! $isApproved && $this->alasan) {
            $mail->line('Alasan Penolakan: ' . $this->alasan)
                ->line('Silakan periksa detail listing Anda di dashboard Mitra, perbaiki catatan yang diberikan, dan ajukan kembali.');
        }

        return $mail
            ->action('Kelola ' . $label, $targetUrl)
            ->line('Terima kasih atas kerja sama Anda.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $isApproved = $this->status === 'aktif';
        $label = ucfirst($this->jenis);

        return [
            'type' => 'listing_moderation',
            'jenis' => $this->jenis,
            'name' => $this->name,
            'status' => $this->status,
            'title' => "Moderasi {$label} " . ($isApproved ? 'Disetujui' : 'Ditolak'),
            'message' => $isApproved
                ? "{$label} \"{$this->name}\" telah disetujui dan kini berstatus aktif untuk menerima reservasi."
                : "{$label} \"{$this->name}\" ditolak: " . ($this->alasan ?? 'Perlu perbaikan informasi listing.'),
            'alasan' => $this->alasan,
            'url' => $this->resolveUrl('/mitra/venues'),
        ];
    }

    private function resolveUrl(string $path): string
    {
        return function_exists('app') && app()->bound('url') ? url($path) : $path;
    }
}
