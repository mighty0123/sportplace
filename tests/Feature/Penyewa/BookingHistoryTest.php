<?php

namespace Tests\Feature\Penyewa;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/penyewa/riwayat');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_customer_can_view_history(): void
    {
        $customer = User::factory()->create([
            'name' => 'Dimas Pratama',
            'email' => 'dimas@sportplace.com',
            'role' => 'customer',
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($customer)->get('/penyewa/riwayat');

        $response->assertStatus(200);
        $response->assertSee('Riwayat Booking');
        $response->assertSee('Semua (12)');
        $response->assertSee('Pending (1)');
        $response->assertSee('Confirmed (2)');
        $response->assertSee('Completed (8)');
        $response->assertSee('Cancelled (1)');
        $response->assertSee('Champion Tennis Club - Court 2');
        $response->assertSee('Viva Arena Futsal - Lapangan Vinyl A');
        $response->assertSee('Smash Center Arena - Lapangan 1 & 2');
        $response->assertSee('Hoops Hall Arena - Full Court Timber');
        $response->assertSee('Bali Futsal Center - Court B');
        $response->assertSee('Sanur Grand Tennis - Outdoor Court');
        $response->assertSee('Batas bayar: 45 mnt');
        $response->assertSee('Dana dikembalikan 100%');
        $response->assertSee('SportPlace');
    }

    public function test_filter_by_status_shows_matching_bookings(): void
    {
        $customer = User::factory()->create([
            'name' => 'Dimas Pratama',
            'email' => 'dimas@sportplace.com',
            'role' => 'customer',
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($customer)->get('/penyewa/riwayat?status=pending');

        $response->assertStatus(200);
        $response->assertSee('Champion Tennis Club - Court 2');
        $response->assertDontSee('Viva Arena Futsal - Lapangan Vinyl A');
    }
}
