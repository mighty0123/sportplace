<?php

namespace Tests\Feature\Penyewa;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/penyewa/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_customer_can_view_dashboard(): void
    {
        $customer = User::factory()->create([
            'name' => 'Dimas Pratama',
            'email' => 'dimas@sportplace.com',
            'role' => 'customer',
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($customer)->get('/penyewa/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Halo, Dimas Pratama 👋');
        $response->assertSee('Booking Lapangan Baru');
        $response->assertSee('Booking Aktif');
        $response->assertSee('Booking Mendatang');
        $response->assertSee('Total Booking Bulan Ini');
        $response->assertSee('Booking Aktif & Mendatang', false);
        $response->assertSee('Viva Arena Futsal');
        $response->assertSee('Smash Center Arena');
        $response->assertSee('Champion Tennis Club');
        $response->assertSee('Hoops Hall Jakarta');
        $response->assertSee('Aksi Cepat');
        $response->assertSee('Cari & Sewa Lapangan');
        $response->assertSee('Riwayat Pemesanan');
        $response->assertSee('Beri Ulasan Lapangan');
        $response->assertSee('SportPlace');
    }
}
