<?php

namespace Tests\Unit;

use App\Models\BookingDetail;
use App\Models\Field;
use App\Models\User;
use App\Models\Venue;
use App\Notifications\ListingModerationStatusUpdated;
use App\Notifications\MitraVerificationStatusUpdated;
use App\Notifications\NewBookingReceived;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class MitraNotificationTest extends TestCase
{
    public function test_mitra_verification_approved_notification_structure(): void
    {
        $notifiable = new User();
        $notifiable->name = 'Bambang Mitra';

        $notification = new MitraVerificationStatusUpdated('approved');

        $this->assertEquals(['database', 'mail'], $notification->via($notifiable));

        $data = $notification->toArray($notifiable);
        $this->assertEquals('mitra_verification', $data['type']);
        $this->assertEquals('approved', $data['status']);
        $this->assertStringContainsString('Disetujui', $data['title']);
        $this->assertStringContainsString('/mitra/dashboard', $data['url']);

        $mail = $notification->toMail($notifiable);
        $this->assertStringContainsString('Disetujui', $mail->subject);
        $this->assertEquals('Buka Dashboard Mitra', $mail->actionText);
    }

    public function test_mitra_verification_rejected_notification_structure(): void
    {
        $notifiable = new User();
        $notifiable->name = 'Bambang Mitra';

        $notification = new MitraVerificationStatusUpdated('rejected', 'Foto KTP buram dan tidak terbaca');

        $data = $notification->toArray($notifiable);
        $this->assertEquals('rejected', $data['status']);
        $this->assertStringContainsString('Ditolak', $data['title']);
        $this->assertEquals('Foto KTP buram dan tidak terbaca', $data['alasan']);
        $this->assertStringContainsString('/mitra/profil', $data['url']);

        $mail = $notification->toMail($notifiable);
        $this->assertStringContainsString('Ditolak', $mail->subject);
        $this->assertEquals('Tinjau & Perbaiki Dokumen', $mail->actionText);
    }

    public function test_listing_moderation_approved_notification_structure(): void
    {
        $notifiable = new User();
        $notifiable->name = 'Mitra Juara';

        $notification = new ListingModerationStatusUpdated('lapangan', 'Lapangan Futsal A', 'aktif');

        $this->assertEquals(['database', 'mail'], $notification->via($notifiable));

        $data = $notification->toArray($notifiable);
        $this->assertEquals('listing_moderation', $data['type']);
        $this->assertEquals('lapangan', $data['jenis']);
        $this->assertEquals('aktif', $data['status']);
        $this->assertStringContainsString('Disetujui', $data['title']);
        $this->assertStringContainsString('/mitra/venues', $data['url']);

        $mail = $notification->toMail($notifiable);
        $this->assertStringContainsString('Disetujui', $mail->subject);
        $this->assertStringContainsString('Lapangan Futsal A', $mail->subject);
    }

    public function test_listing_moderation_rejected_notification_structure(): void
    {
        $notifiable = new User();
        $notifiable->name = 'Mitra Juara';

        $notification = new ListingModerationStatusUpdated('venue', 'GOR Bahagia', 'rejected', 'Alamat tidak lengkap');

        $data = $notification->toArray($notifiable);
        $this->assertEquals('rejected', $data['status']);
        $this->assertStringContainsString('Ditolak', $data['title']);
        $this->assertEquals('Alamat tidak lengkap', $data['alasan']);

        $mail = $notification->toMail($notifiable);
        $this->assertStringContainsString('Ditolak', $mail->subject);
    }

    public function test_new_booking_notification_structure(): void
    {
        $notifiable = new User();
        $notifiable->name = 'Pemilik Lapangan';

        $venue = new Venue();
        $venue->setRawAttributes(['nama_venue' => 'Sport Center Juara']);

        $field = new Field();
        $field->setRawAttributes(['nama_lapangan' => 'Lapangan Bulutangkis 1']);
        $field->setRelation('venue', $venue);

        $detail = new BookingDetail();
        $detail->setRawAttributes([
            'id' => 99,
            'header_id' => 10,
            'field_id' => 1,
            'tanggal_sewa' => '2026-12-01',
            'jam_mulai' => '10:00:00',
            'jam_selesai' => '11:00:00',
            'harga_slot' => 150000,
        ]);
        $detail->setRelation('field', $field);

        $notification = new NewBookingReceived($detail);

        $this->assertEquals(['database', 'mail'], $notification->via($notifiable));

        $data = $notification->toArray($notifiable);
        $this->assertEquals('new_booking', $data['type']);
        $this->assertEquals(99, $data['booking_detail_id']);
        $this->assertEquals(10, $data['header_id']);
        $this->assertStringContainsString('/mitra/pesanan/99', $data['url']);

        $mail = $notification->toMail($notifiable);
        $this->assertStringContainsString('Lapangan Bulutangkis 1', $mail->subject);
        $this->assertEquals('Lihat Detail Pesanan', $mail->actionText);
    }
}
