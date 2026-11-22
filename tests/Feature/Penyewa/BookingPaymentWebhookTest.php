<?php

namespace Tests\Feature\Penyewa;

use App\Models\BookingDetail;
use App\Models\BookingHeader;
use App\Models\Field;
use App\Models\MasterCity;
use App\Models\MasterSport;
use App\Models\Payment;
use App\Models\PaymentWebhookEvent;
use App\Models\Refund;
use App\Models\User;
use App\Models\Venue;
use App\Services\MidtransService;
use App\Support\BookingCode;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BookingPaymentWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected Field $field;
    protected Venue $venue;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create([
            'role' => 'customer',
            'status_akun' => 'aktif',
        ]);

        $city = MasterCity::create(['nama_kota' => 'Denpasar', 'provinsi' => 'Bali']);
        $sport = MasterSport::create([
            'nama_olahraga' => 'Futsal',
            'slug' => 'futsal',
            'icon_url' => 'images/sports/futsal.svg',
            'status_olahraga' => 'aktif',
        ]);

        $mitraUser = User::factory()->create([
            'role' => 'mitra',
            'status_akun' => 'aktif',
        ]);

        $mitraProfile = \App\Models\MitraProfile::create([
            'user_id' => $mitraUser->id,
            'no_ktp'  => '1234567890123456',
            'npwp'    => '123456789012345',
            'status'  => 'approved',
        ]);

        $this->venue = Venue::create([
            'mitra_id' => $mitraProfile->id,
            'city_id' => $city->id,
            'nama_venue' => 'Arena Sport Futsal',
            'alamat_lengkap' => 'Jl. Bypass Ngurah Rai',
            'jam_buka' => '08:00:00',
            'jam_tutup' => '22:00:00',
            'status_vanue' => 'aktif',
        ]);

        $this->field = Field::create([
            'venue_id' => $this->venue->id,
            'sport_id' => $sport->id,
            'nama_lapangan' => 'Lapangan 1 Vinyl',
            'tipe_lapangan' => 'Vinyl',
            'harga_perjam' => 150000,
            'status_lapangan' => 'aktif',
        ]);

        Config::set('midtrans.server_key', 'test-server-key');
        Config::set('midtrans.client_key', 'test-client-key');
        Config::set('booking.payment_window_minutes', 15);
        Config::set('booking.cancellation_min_hours', 24);
        Config::set('booking.refund_percent', 100);
    }

    public function test_booking_creation_assigns_unique_booking_code_and_expires_at_from_config(): void
    {
        Config::set('booking.payment_window_minutes', 20);

        $now = Carbon::parse('2026-11-20 10:00:00', 'Asia/Makassar');
        Carbon::setTestNow($now);

        $response = $this->actingAs($this->customer)->postJson('/penyewa/bayar', [
            'field_id'     => $this->field->id,
            'tanggal_sewa' => '2026-11-25',
            'slots'        => [
                ['start' => '10:00', 'end' => '11:00', 'price' => 150000],
            ],
            'total_bayar'  => 152500,
        ]);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertTrue($data['success']);

        $booking = BookingHeader::find($data['booking_id']);
        $this->assertNotNull($booking);
        $this->assertNotNull($booking->kode_booking);
        $this->assertMatchesRegularExpression('/^SP-[A-Z0-9]{6}$/', $booking->kode_booking);

        $expectedExpiresAt = $now->copy()->addMinutes(20);
        $this->assertEquals($expectedExpiresAt->timestamp, $booking->expires_at->timestamp);

        Carbon::setTestNow();
    }

    public function test_cancellation_rejected_if_slot_starts_within_cancellation_min_hours(): void
    {
        Config::set('booking.cancellation_min_hours', 24);

        $now = Carbon::parse('2026-11-20 10:00:00', 'Asia/Makassar');
        Carbon::setTestNow($now);

        // Slot mulai 5 jam lagi (kurang dari 24 jam)
        $booking = BookingHeader::create([
            'penyewa_id'    => $this->customer->id,
            'kode_booking'  => BookingCode::unique(),
            'total_tagihan' => 152500,
            'status'        => 'payment',
            'created_at'    => $now->copy()->subHours(1),
        ]);

        BookingDetail::create([
            'header_id'    => $booking->id,
            'field_id'     => $this->field->id,
            'tanggal_sewa' => '2026-11-20',
            'jam_mulai'    => '15:00:00',
            'jam_selesai'  => '16:00:00',
            'harga_slot'   => 150000,
            'status_item'  => 'ongoing',
            'created_at'   => $now->copy()->subHours(1),
        ]);

        $response = $this->actingAs($this->customer)->post("/penyewa/booking/{$booking->id}/cancel");
        $response->assertSessionHas('error');

        $booking->refresh();
        $this->assertEquals('payment', $booking->status);

        Carbon::setTestNow();
    }

    public function test_cancellation_allowed_if_slot_starts_after_cancellation_min_hours_and_creates_refund_if_paid(): void
    {
        Config::set('booking.cancellation_min_hours', 24);
        Config::set('booking.refund_percent', 80);

        $now = Carbon::parse('2026-11-20 10:00:00', 'Asia/Makassar');
        Carbon::setTestNow($now);

        // Slot mulai 48 jam lagi (lebih dari 24 jam)
        $booking = BookingHeader::create([
            'penyewa_id'    => $this->customer->id,
            'kode_booking'  => BookingCode::unique(),
            'total_tagihan' => 200000,
            'status'        => 'payment',
            'created_at'    => $now->copy()->subHours(1),
        ]);

        $detail = BookingDetail::create([
            'header_id'    => $booking->id,
            'field_id'     => $this->field->id,
            'tanggal_sewa' => '2026-11-22',
            'jam_mulai'    => '14:00:00',
            'jam_selesai'  => '15:00:00',
            'harga_slot'   => 200000,
            'status_item'  => 'ongoing',
            'created_at'   => $now->copy()->subHours(1),
        ]);

        $payment = Payment::create([
            'header_id'         => $booking->id,
            'pg_transaction_id' => 'SP-TEST-TRX-1',
            'metode_pembayaran' => 'Midtrans (qris)',
            'nominal_bayar'     => 200000,
            'status_pembayaran' => 'succes',
            'paid_at'           => $now->copy()->subHours(1),
            'created_at'        => $now->copy()->subHours(1),
        ]);

        $response = $this->actingAs($this->customer)->post("/penyewa/booking/{$booking->id}/cancel");
        $response->assertRedirect("/penyewa/booking/{$booking->id}");
        $response->assertSessionHas('success');

        $booking->refresh();
        $this->assertEquals('canceled', $booking->status);

        // Pastikan record Refund dibuat dengan persentase 80% (160000)
        $refund = Refund::where('header_id', $booking->id)->first();
        $this->assertNotNull($refund);
        $this->assertEquals(160000, (float) $refund->nominal_refund);
        $this->assertEquals('pending', $refund->status_refund);
        $this->assertEquals($payment->id, $refund->payment_id);
        $this->assertEquals($detail->id, $refund->detail_id);

        Carbon::setTestNow();
    }

    public function test_webhook_saves_raw_payload_and_ignores_duplicate_event(): void
    {
        $serverKey = 'test-server-key';
        Config::set('midtrans.server_key', $serverKey);

        $booking = BookingHeader::create([
            'penyewa_id'    => $this->customer->id,
            'kode_booking'  => BookingCode::unique(),
            'total_tagihan' => 150000,
            'status'        => 'pending',
            'expires_at'    => now()->addMinutes(15),
            'created_at'    => now(),
        ]);

        $orderId = 'SP-' . $booking->id . '-12345';
        $payment = Payment::create([
            'header_id'         => $booking->id,
            'pg_transaction_id' => $orderId,
            'metode_pembayaran' => 'Midtrans',
            'nominal_bayar'     => 150000,
            'status_pembayaran' => 'pending',
            'created_at'        => now(),
        ]);

        $statusCode = '200';
        $grossAmount = '150000.00';
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $payload = [
            'event_id'           => 'evt_test_unique_1',
            'order_id'           => $orderId,
            'status_code'        => $statusCode,
            'gross_amount'       => $grossAmount,
            'signature_key'      => $signatureKey,
            'transaction_status' => 'settlement',
            'payment_type'       => 'gopay',
        ];

        // Mock gateway API status refetch
        Http::fake([
            '*/v2/' . $orderId . '/status' => Http::response([
                'transaction_status' => 'settlement',
                'status_code'        => '200',
                'gross_amount'       => '150000.00',
                'payment_type'       => 'gopay',
            ], 200),
        ]);

        // Kiriman pertama
        $res1 = $this->postJson('/api/midtrans/notification', $payload);
        $res1->assertStatus(200);

        // Verifikasi payment_webhook_events tersimpan
        $event = PaymentWebhookEvent::where('provider', 'midtrans')->where('event_id', 'evt_test_unique_1')->first();
        $this->assertNotNull($event);
        $this->assertTrue($event->signature_valid);
        $this->assertEquals('processed', $event->process_status);
        $this->assertEquals($orderId, $event->pg_transaction_id);

        $booking->refresh();
        $this->assertEquals('payment', $booking->status);

        // Kiriman kedua (duplikat event_id)
        $res2 = $this->postJson('/api/midtrans/notification', $payload);
        $res2->assertStatus(200);
        $res2->assertJson(['status' => 'ignored']);

        // Pastikan jumlah event tetap 1
        $this->assertEquals(1, PaymentWebhookEvent::where('provider', 'midtrans')->where('event_id', 'evt_test_unique_1')->count());
    }

    public function test_webhook_resurrects_expired_booking_if_slot_still_available(): void
    {
        $serverKey = 'test-server-key';
        Config::set('midtrans.server_key', $serverKey);

        $pastTime = now()->subMinutes(30);

        // Booking kedaluwarsa
        $booking = BookingHeader::create([
            'penyewa_id'    => $this->customer->id,
            'kode_booking'  => BookingCode::unique(),
            'total_tagihan' => 150000,
            'status'        => 'expired',
            'expires_at'    => now()->subMinutes(15),
            'created_at'    => $pastTime,
        ]);

        BookingDetail::create([
            'header_id'    => $booking->id,
            'field_id'     => $this->field->id,
            'tanggal_sewa' => now()->addDays(2)->format('Y-m-d'),
            'jam_mulai'    => '14:00:00',
            'jam_selesai'  => '15:00:00',
            'harga_slot'   => 150000,
            'status_item'  => 'ongoing',
            'created_at'   => $pastTime,
        ]);

        $orderId = 'SP-' . $booking->id . '-expired-slot-empty';
        $payment = Payment::create([
            'header_id'         => $booking->id,
            'pg_transaction_id' => $orderId,
            'metode_pembayaran' => 'Midtrans',
            'nominal_bayar'     => 150000,
            'status_pembayaran' => 'pending',
            'created_at'        => $pastTime,
        ]);

        $statusCode = '200';
        $grossAmount = '150000.00';
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        Http::fake([
            '*/v2/' . $orderId . '/status' => Http::response([
                'transaction_status' => 'settlement',
                'status_code'        => '200',
                'gross_amount'       => '150000.00',
                'payment_type'       => 'bca_va',
            ], 200),
        ]);

        $payload = [
            'event_id'           => 'evt_resurrect_' . time(),
            'order_id'           => $orderId,
            'status_code'        => $statusCode,
            'gross_amount'       => $grossAmount,
            'signature_key'      => $signatureKey,
            'transaction_status' => 'settlement',
            'payment_type'       => 'bca_va',
        ];

        $res = $this->postJson('/api/midtrans/notification', $payload);
        $res->assertStatus(200);

        $booking->refresh();
        $this->assertEquals('payment', $booking->status, 'Booking yang kedaluwarsa harus dihidupkan kembali jika slot masih kosong');

        $payment->refresh();
        $this->assertEquals('succes', $payment->status_pembayaran);
    }

    public function test_webhook_creates_full_refund_if_expired_booking_slot_taken(): void
    {
        $serverKey = 'test-server-key';
        Config::set('midtrans.server_key', $serverKey);

        $pastTime = now()->subMinutes(30);
        $targetDate = now()->addDays(3)->format('Y-m-d');

        // Booking 1 kedaluwarsa milik customer A
        $expiredBooking = BookingHeader::create([
            'penyewa_id'    => $this->customer->id,
            'kode_booking'  => BookingCode::unique(),
            'total_tagihan' => 150000,
            'status'        => 'expired',
            'expires_at'    => now()->subMinutes(15),
            'created_at'    => $pastTime,
        ]);

        $expiredDetail = BookingDetail::create([
            'header_id'    => $expiredBooking->id,
            'field_id'     => $this->field->id,
            'tanggal_sewa' => $targetDate,
            'jam_mulai'    => '16:00:00',
            'jam_selesai'  => '17:00:00',
            'harga_slot'   => 150000,
            'status_item'  => 'ongoing',
            'created_at'   => $pastTime,
        ]);

        $orderId = 'SP-' . $expiredBooking->id . '-expired-slot-taken';
        $payment = Payment::create([
            'header_id'         => $expiredBooking->id,
            'pg_transaction_id' => $orderId,
            'metode_pembayaran' => 'Midtrans',
            'nominal_bayar'     => 150000,
            'status_pembayaran' => 'pending',
            'created_at'        => $pastTime,
        ]);

        // Slot tersebut sudah diambil orang lain (Customer B) dengan status payment
        $otherCustomer = User::factory()->create(['role' => 'customer']);
        $newBooking = BookingHeader::create([
            'penyewa_id'    => $otherCustomer->id,
            'kode_booking'  => BookingCode::unique(),
            'total_tagihan' => 150000,
            'status'        => 'payment',
            'created_at'    => now()->subMinutes(5),
        ]);

        BookingDetail::create([
            'header_id'    => $newBooking->id,
            'field_id'     => $this->field->id,
            'tanggal_sewa' => $targetDate,
            'jam_mulai'    => '16:00:00',
            'jam_selesai'  => '17:00:00',
            'harga_slot'   => 150000,
            'status_item'  => 'ongoing',
            'created_at'   => now()->subMinutes(5),
        ]);

        $statusCode = '200';
        $grossAmount = '150000.00';
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        Http::fake([
            '*/v2/' . $orderId . '/status' => Http::response([
                'transaction_status' => 'settlement',
                'status_code'        => '200',
                'gross_amount'       => '150000.00',
                'payment_type'       => 'echannel',
            ], 200),
        ]);

        $payload = [
            'event_id'           => 'evt_slot_taken_' . time(),
            'order_id'           => $orderId,
            'status_code'        => $statusCode,
            'gross_amount'       => $grossAmount,
            'signature_key'      => $signatureKey,
            'transaction_status' => 'settlement',
            'payment_type'       => 'echannel',
        ];

        $res = $this->postJson('/api/midtrans/notification', $payload);
        $res->assertStatus(200);

        $expiredBooking->refresh();
        $this->assertEquals('expired', $expiredBooking->status, 'Booking tetap kedaluwarsa karena slot sudah diambil orang lain');

        // Pastikan Full Refund (100%) dibuat
        $refund = Refund::where('header_id', $expiredBooking->id)->first();
        $this->assertNotNull($refund, 'Harus membuat entri refund baru');
        $this->assertEquals(150000, (float) $refund->nominal_refund, 'Refund harus 100% penuh');
        $this->assertEquals('pending', $refund->status_refund);
        $this->assertStringContainsString('Full Refund', $refund->alasan_refund);
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        $serverKey = 'test-server-key';
        Config::set('midtrans.server_key', $serverKey);

        $orderId = 'SP-INVALID-SIG-' . time();
        $payload = [
            'event_id'           => 'evt_invalid_sig_' . time(),
            'order_id'           => $orderId,
            'status_code'        => '200',
            'gross_amount'       => '100000.00',
            'signature_key'      => 'wrong-signature-key-12345',
            'transaction_status' => 'settlement',
        ];

        $res = $this->postJson('/api/midtrans/notification', $payload);
        $res->assertStatus(403);
        $res->assertJson(['message' => 'Invalid signature']);

        $event = PaymentWebhookEvent::where('event_id', $payload['event_id'])->first();
        $this->assertNotNull($event);
        $this->assertFalse($event->signature_valid);
        $this->assertEquals('failed', $event->process_status);
        $this->assertEquals('Invalid signature', $event->error);
    }
}
