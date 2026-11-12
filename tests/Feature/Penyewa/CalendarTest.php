<?php

namespace Tests\Feature\Penyewa;

use App\Models\BookingDetail;
use App\Models\BookingHeader;
use App\Models\Field;
use App\Models\MasterCity;
use App\Models\MasterSport;
use App\Models\MitraProfile;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarTest extends TestCase
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
            'alamat_lengkap' => 'Renon, Denpasar, Bali',
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
    }

    public function test_calendar_page_displays_only_three_legend_indicators(): void
    {
        $response = $this->actingAs($this->customer)->get("/penyewa/kalender/{$this->field->id}");

        $response->assertStatus(200);

        // Legend strip must contain Available, Dipilih, and Booked
        $response->assertSee('Available');
        $response->assertSee('Dipilih');
        $response->assertSee('Booked');

        // Legend strip must NOT contain Ditahan or Lewat
        $response->assertDontSee('Ditahan</span>', false);
        $response->assertDontSee('Lewat</span>', false);
    }

    public function test_confirmed_booking_displays_as_booked(): void
    {
        $targetDate = Carbon::tomorrow()->format('Y-m-d');

        $header = BookingHeader::create([
            'penyewa_id'    => $this->customer->id,
            'kode_booking'  => 'BK-CONFIRMED-01',
            'status'        => 'payment',
            'total_tagihan' => 150000,
            'expires_at'    => Carbon::now()->addHours(2),
            'created_at'    => Carbon::now(),
        ]);

        BookingDetail::create([
            'header_id'    => $header->id,
            'field_id'     => $this->field->id,
            'tanggal_sewa' => $targetDate,
            'jam_mulai'    => '10:00:00',
            'jam_selesai'  => '11:00:00',
            'harga_slot'   => 150000,
            'status_item'  => 'ongoing',
            'created_at'   => Carbon::now(),
        ]);

        $response = $this->actingAs($this->customer)->get("/penyewa/kalender/{$this->field->id}?date={$targetDate}");

        $response->assertStatus(200);
        $response->assertSee('data-time="10:00 - 11:00"', false);
        // Slot must be marked as booked
        $response->assertSee('data-status="booked"', false);
    }

    public function test_pending_booking_within_15_minutes_displays_as_booked(): void
    {
        // "Lalu untuk yg sudah ke booking org juga berubah jadi booked"
        $targetDate = Carbon::tomorrow()->format('Y-m-d');

        $otherUser = User::factory()->create(['role' => 'customer']);

        $header = BookingHeader::create([
            'penyewa_id'    => $otherUser->id,
            'kode_booking'  => 'BK-PENDING-02',
            'status'        => 'pending',
            'total_tagihan' => 150000,
            'expires_at'    => Carbon::now()->addMinutes(12),
            'created_at'    => Carbon::now(),
        ]);

        BookingDetail::create([
            'header_id'    => $header->id,
            'field_id'     => $this->field->id,
            'tanggal_sewa' => $targetDate,
            'jam_mulai'    => '14:00:00',
            'jam_selesai'  => '15:00:00',
            'harga_slot'   => 150000,
            'status_item'  => 'ongoing',
            'created_at'   => Carbon::now(),
        ]);

        $response = $this->actingAs($this->customer)->get("/penyewa/kalender/{$this->field->id}?date={$targetDate}");

        $response->assertStatus(200);
        // Slot that is pending within 15 minutes MUST be shown as 'booked'
        $response->assertSee('data-time="14:00 - 15:00"', false);
        $response->assertSee('data-status="booked"', false);
    }

    public function test_pending_booking_that_expired_reverts_to_available(): void
    {
        // "kalau lewat dia akan balik ke available."
        $targetDate = Carbon::tomorrow()->format('Y-m-d');

        $otherUser = User::factory()->create(['role' => 'customer']);

        $header = BookingHeader::create([
            'penyewa_id'    => $otherUser->id,
            'kode_booking'  => 'BK-EXPIRED-03',
            'status'        => 'pending',
            'total_tagihan' => 150000,
            'expires_at'    => Carbon::now()->subMinutes(5), // Sudah lewat batas waktu
            'created_at'    => Carbon::now()->subMinutes(20),
        ]);

        BookingDetail::create([
            'header_id'    => $header->id,
            'field_id'     => $this->field->id,
            'tanggal_sewa' => $targetDate,
            'jam_mulai'    => '16:00:00',
            'jam_selesai'  => '17:00:00',
            'harga_slot'   => 150000,
            'status_item'  => 'ongoing',
            'created_at'   => Carbon::now()->subMinutes(20),
        ]);

        $response = $this->actingAs($this->customer)->get("/penyewa/kalender/{$this->field->id}?date={$targetDate}");

        $response->assertStatus(200);

        // Header status di database otomatis diubah menjadi expired
        $this->assertEquals('expired', $header->fresh()->status);

        // Slot harus kembali berstatus Available
        $response->assertSee('data-time="16:00 - 17:00"', false);
        $response->assertSee('Available');
    }

    public function test_calendar_json_endpoint_returns_slots_and_statuses(): void
    {
        $targetDate = Carbon::tomorrow()->format('Y-m-d');

        $response = $this->actingAs($this->customer)->getJson("/penyewa/kalender/{$this->field->id}?date={$targetDate}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'timeSlots' => [
                '*' => [
                    'id',
                    'date_short',
                    'full_date',
                    'time_start',
                    'time_end',
                    'time_label',
                    'price',
                    'status',
                    'label',
                    'court_name',
                ]
            ],
            'serviceFee',
        ]);
    }
}
