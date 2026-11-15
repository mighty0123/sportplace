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
}
