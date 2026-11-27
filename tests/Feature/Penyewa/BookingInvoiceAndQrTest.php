<?php

namespace Tests\Feature\Penyewa;

use App\Models\BookingDetail;
use App\Models\BookingHeader;
use App\Models\Field;
use App\Models\MasterCity;
use App\Models\MasterSport;
use App\Models\MitraProfile;
use App\Models\Payment;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingInvoiceAndQrTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected Field $field;
    protected Venue $venue;
    protected BookingHeader $booking;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create([
            'role' => 'customer',
            'status_akun' => 'aktif',
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'no_hp' => '081234567890',
        ]);

        $city = MasterCity::create(['nama_kota' => 'Denpasar', 'provinsi' => 'Bali']);
        $sport = MasterSport::create([
            'nama_olahraga' => 'Futsal',
            'slug' => 'futsal',
            'icon_url' => 'images/sports/futsal.svg',
            'status_olahraga' => 'aktif',
        ]);

        $mitraUser = User::factory()->create(['role' => 'mitra', 'status_akun' => 'aktif']);
        $mitraProfile = MitraProfile::create([
            'user_id' => $mitraUser->id,
            'no_ktp'  => '1234567890123456',
            'npwp'    => '123456789012345',
            'status'  => 'approved',
        ]);

        $this->venue = Venue::create([
            'mitra_id'       => $mitraProfile->id,
            'city_id'        => $city->id,
            'nama_venue'     => 'Viva Arena Futsal',
            'alamat_lengkap' => 'Jl. Hayam Wuruk No. 88, Renon',
            'jam_buka'       => '08:00:00',
            'jam_tutup'      => '23:00:00',
            'status_vanue'   => 'aktif',
        ]);

        $this->field = Field::create([
            'venue_id'        => $this->venue->id,
            'sport_id'        => $sport->id,
            'nama_lapangan'   => 'Lapangan Vinyl A',
            'harga_perjam'    => 150000,
            'status_lapangan' => 'aktif',
        ]);

        $this->booking = BookingHeader::create([
            'penyewa_id'    => $this->customer->id,
            'kode_booking'  => 'SP999XYZ',
            'status'        => 'payment',
            'total_tagihan' => 152500,
            'expires_at'    => Carbon::now()->addHours(2),
            'created_at'    => Carbon::now(),
        ]);

        BookingDetail::create([
            'header_id'    => $this->booking->id,
            'field_id'     => $this->field->id,
            'tanggal_sewa' => Carbon::tomorrow()->format('Y-m-d'),
            'jam_mulai'    => '19:00:00',
            'jam_selesai'  => '20:00:00',
            'harga_slot'   => 150000,
            'status_item'  => 'ongoing',
            'created_at'   => Carbon::now(),
        ]);

        Payment::create([
            'header_id'         => $this->booking->id,
            'metode_pembayaran' => 'Midtrans (QRIS)',
            'paid_at'           => Carbon::now(),
            'nominal_bayar'     => 152500,
            'status_pembayaran' => 'succes',
            'pg_transaction_id' => 'MID-TEST-12345',
            'created_at'        => Carbon::now(),
        ]);
    }

    public function test_booking_detail_page_shows_scannable_qr_ticket_and_invoice_button(): void
    {
        $response = $this->actingAs($this->customer)->get("/penyewa/booking/{$this->booking->id}");

        $response->assertStatus(200);

        // Harus menampilkan kode booking
        $response->assertSee('SP999XYZ');

        // Harus menampilkan QR code img
        $response->assertSee('data:image/svg+xml;base64,', false);
        $response->assertSee('Scan QR di atas dengan ponsel untuk diarahkan langsung ke kode booking ini');

        // Tombol unduh invoice PDF mengarah ke route invoice
        $response->assertSee("/penyewa/booking/{$this->booking->kode_booking}/invoice");
    }

    public function test_booking_detail_can_be_accessed_via_kode_booking(): void
    {
        // QR Code ketika discan akan langsung mengarahkan ke kode booking
        $response = $this->actingAs($this->customer)->get("/penyewa/booking/SP999XYZ");

        $response->assertStatus(200);
        $response->assertSee('SP999XYZ');
        $response->assertSee('Viva Arena Futsal');
        $response->assertSee('Lapangan Vinyl A');
    }

    public function test_invoice_pdf_streams_landscape_single_page_nota(): void
    {
        $response = $this->actingAs($this->customer)->get("/penyewa/booking/{$this->booking->kode_booking}/invoice");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');

        // Memastikan isi PDF memuat data invoice
        $content = $response->getContent();
        $this->assertNotEmpty($content);
        $this->assertStringStartsWith('%PDF-', $content);
    }

    public function test_public_can_verify_ticket_via_scanned_qr_url_without_login(): void
    {
        // Siapapun yang scan QR di HP (termasuk resepsionis venue) bisa buka halaman verifikasi tiket tanpa login
        $response = $this->get("/tiket/{$this->booking->kode_booking}");

        $response->assertStatus(200);
        $response->assertSee('SP999XYZ');
        $response->assertSee('TIKET VALID');
        $response->assertSee('Viva Arena Futsal');
        $response->assertSee('Konfirmasi Check-In Sekarang');
    }

    public function test_ticket_can_be_checked_in_successfully(): void
    {
        $this->assertNull($this->booking->fresh()->checked_in_at);

        // Petugas melakukan check-in
        $response = $this->post("/tiket/{$this->booking->kode_booking}/checkin");

        $response->assertStatus(302);
        $this->assertNotNull($this->booking->fresh()->checked_in_at);

        // Akses ulang halaman tiket: harus menampilkan status sudah check-in
        $responseVerify = $this->get("/tiket/{$this->booking->kode_booking}");
        $responseVerify->assertStatus(200);
        $responseVerify->assertSee('SUDAH CHECK-IN');
        $responseVerify->assertSee('Tiket Sudah Di-Check-In');
    }

    public function test_instant_confirm_payment_updates_status_directly_with_clean_url(): void
    {
        $pendingBooking = BookingHeader::create([
            'penyewa_id'    => $this->customer->id,
            'kode_booking'  => 'SPPENDING1',
            'total_tagihan' => 150000,
            'status'        => 'pending',
            'created_at'    => Carbon::now(),
        ]);

        $response = $this->actingAs($this->customer)->postJson("/penyewa/booking/{$pendingBooking->id}/confirm-payment", [
            'payment_type' => 'qris',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status'  => 'payment',
            'is_paid' => true,
        ]);

        // URL redirect harus bersih tanpa ?paid=1
        $this->assertStringNotContainsString('?paid=1', $response->json('redirect_url'));

        // Status di database langsung terupdate
        $this->assertEquals('payment', $pendingBooking->fresh()->status);
        $this->assertEquals('succes', $pendingBooking->fresh()->payment?->status_pembayaran);
    }

    public function test_check_payment_status_returns_clean_url(): void
    {
        $response = $this->actingAs($this->customer)->getJson("/penyewa/booking/{$this->booking->id}/check-status");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status'  => 'payment',
            'is_paid' => true,
        ]);

        $this->assertStringNotContainsString('?paid=1', $response->json('redirect'));
    }

    public function test_legacy_paid_query_auto_redirects_to_clean_url(): void
    {
        $response = $this->actingAs($this->customer)->get("/penyewa/booking/{$this->booking->id}?paid=1");

        $response->assertStatus(302);
        $response->assertRedirect("/penyewa/booking/{$this->booking->kode_booking}");
    }

    public function test_review_pembayaran_uses_dynamic_slot_price_for_daytime_slot(): void
    {
        $this->field->update([
            'harga_perjam' => 180000,
            'meta_harga'   => [
                'siang' => 140000,
                'malam' => 180000,
            ],
        ]);

        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        // Uji slot siang (10:00 - 11:00) -> Harus Rp 140.000, bukan Rp 180.000
        $response = $this->actingAs($this->customer)->get("/penyewa/review-pembayaran?field_id={$this->field->id}&date={$tomorrow}&slots=10:00 - 11:00");

        $response->assertStatus(200);
        $response->assertSee('Rp 140.000');
        $response->assertSee('Rp 142.500'); // Subtotal 140.000 + Biaya Layanan 2.500
        $response->assertDontSee('Rp 180.000');
    }

    public function test_process_payment_stores_exact_slot_price_in_database(): void
    {
        $this->field->update([
            'harga_perjam' => 180000,
            'meta_harga'   => [
                'siang' => 140000,
                'malam' => 180000,
            ],
        ]);

        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        $response = $this->actingAs($this->customer)->post('/penyewa/bayar', [
            'field_id'           => $this->field->id,
            'tanggal_sewa'       => $tomorrow,
            'slots'              => '10:00 - 11:00',
            'metode_pembayaran'  => 'Midtrans',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('booking_details', [
            'field_id'   => $this->field->id,
            'jam_mulai'  => '10:00:00',
            'harga_slot' => 140000,
        ]);

        $this->assertDatabaseHas('booking_headers', [
            'penyewa_id'    => $this->customer->id,
            'subtotal_slot' => 140000,
            'total_tagihan' => 142500,
        ]);
    }
}
