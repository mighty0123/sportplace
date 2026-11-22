<?php

namespace App\Notifications;

use App\Models\BookingDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBookingReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public BookingDetail $bookingDetail
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
        $fieldName = $this->bookingDetail->field?->nama_lapangan ?? 'Lapangan';
        $venueName = ($this->bookingDetail->field && $this->bookingDetail->field->relationLoaded('venue'))
            ? $this->bookingDetail->field->venue?->nama_venue
            : null;
        $tanggal = $this->bookingDetail->tanggal_sewa?->format('d/m/Y') ?? '-';
        $jamMulai = substr((string) $this->bookingDetail->jam_mulai, 0, 5);
        $jamSelesai = substr((string) $this->bookingDetail->jam_selesai, 0, 5);
        $penyewa = ($this->bookingDetail->relationLoaded('header') && $this->bookingDetail->header && $this->bookingDetail->header->relationLoaded('user'))
            ? $this->bookingDetail->header->user?->name ?? 'Penyewa'
            : 'Penyewa';
        $harga = 'Rp ' . number_format($this->bookingDetail->harga_slot ?? 0, 0, ',', '.');
        $targetUrl = $this->resolveUrl('/mitra/pesanan/' . $this->bookingDetail->id);

        return (new MailMessage)
            ->subject("[SportPlace] Pesanan Baru Masuk: {$fieldName} ({$tanggal})")
            ->greeting('Halo, ' . ($notifiable->name ?? 'Mitra SportPlace') . '!')
            ->line("Ada reservasi baru yang masuk untuk fasilitas Anda!")
            ->line("**Fasilitas:** {$fieldName}" . ($venueName ? " ({$venueName})" : ''))
            ->line("**Jadwal Sesi:** {$tanggal}, {$jamMulai} - {$jamSelesai} WIB")
            ->line("**Penyewa:** {$penyewa}")
            ->line("**Tarif Sesi:** {$harga}")
            ->action('Lihat Detail Pesanan', $targetUrl)
            ->line('Mohon persiapkan fasilitas terbaik untuk menyambut kedatangan penyewa.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $fieldName = $this->bookingDetail->field?->nama_lapangan ?? 'Lapangan';
        $tanggal = $this->bookingDetail->tanggal_sewa?->format('d/m/Y') ?? '';
        $jamMulai = substr((string) $this->bookingDetail->jam_mulai, 0, 5);
        $jamSelesai = substr((string) $this->bookingDetail->jam_selesai, 0, 5);

        return [
            'type' => 'new_booking',
            'title' => 'Pesanan Baru Masuk',
            'message' => "Reservasi baru untuk {$fieldName} pada {$tanggal} ({$jamMulai} - {$jamSelesai}).",
            'booking_detail_id' => $this->bookingDetail->id,
            'header_id' => $this->bookingDetail->header_id,
            'url' => $this->resolveUrl('/mitra/pesanan/' . $this->bookingDetail->id),
        ];
    }

    private function resolveUrl(string $path): string
    {
        return function_exists('app') && app()->bound('url') ? url($path) : $path;
    }
}
