<?php

namespace Tests\Feature\Penyewa;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/penyewa/kalender');
        $response->assertRedirect('/login');

        $responseDetail = $this->get('/penyewa/booking/1');
        $responseDetail->assertRedirect('/login');
    }

    public function test_authenticated_customer_can_view_court_calendar(): void
    {
        $customer = User::factory()->create([
            'name' => 'Dimas Pratama',
            'email' => 'dimas@sportplace.com',
            'role' => 'customer',
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($customer)->get('/penyewa/kalender');

        $response->assertStatus(200);
        $response->assertSee('Viva Arena Futsal - Lapangan Vinyl A');
        $response->assertSee('Indoor Vinyl Mat');
        $response->assertSee('4.8');
        $response->assertSee('(128 ulasan)');
        $response->assertSee('Renon, Denpasar, Bali');
        $response->assertSee('TARIF SEWA', false);
        $response->assertSee('Mulai', false);
        $response->assertSee('Rp 150.000', false);

        // Date Strip
        $response->assertSee('Kam');
        $response->assertSee('24 Okt');
        $response->assertSee('Sen');
        $response->assertSee('21 Okt');

        // Legend
        $response->assertSee('Tersedia');
        $response->assertSee('Dipilih Anda');
        $response->assertSee('Terisi');
        $response->assertSee('Perawatan');
        $response->assertSee('Tutup');

        // Courts
        $response->assertSee('Court 1');
        $response->assertSee('Vinyl Mat');
        $response->assertSee('Court 2');
        $response->assertSee('Court 3');
        $response->assertSee('Interlock Pro');

        // Times & Slots
        $response->assertSee('08:00 - 09:00');
        $response->assertSee('18:00 - 19:00');
        $response->assertSee('19:00 - 20:00');
        $response->assertSee('Sparring Denpasar');
        $response->assertSee('Perawatan Rutin');
        $response->assertSee('Liga Mahasiswa');

        // Detail Booking Summary
        $response->assertSee('Detail Booking');
        $response->assertSee('Slot Dipilih');
        $response->assertSee('Sewa Lapangan');
        $response->assertSee('Biaya Layanan');
        $response->assertSee('Rp 302.500', false);
        $response->assertSee('Lanjut ke Pembayaran');
        $response->assertSee('Slot ditahan selama 15 menit');
    }

    public function test_booking_detail_route_shows_calendar(): void
    {
        $customer = User::factory()->create([
            'name' => 'Dimas Pratama',
            'email' => 'dimas@sportplace.com',
            'role' => 'customer',
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($customer)->get('/penyewa/booking/1');

        $response->assertStatus(200);
        $response->assertSee('Viva Arena Futsal - Lapangan Vinyl A');
    }
}
