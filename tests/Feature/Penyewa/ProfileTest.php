<?php

namespace Tests\Feature\Penyewa;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/penyewa/profil');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_customer_can_view_profile(): void
    {
        $customer = User::factory()->create([
            'name' => 'Dimas Pratama',
            'email' => 'dimas.pratama@email.com',
            'no_hp' => '081234567890',
            'role' => 'customer',
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($customer)->get('/penyewa/profil');

        $response->assertStatus(200);
        $response->assertSee('Dimas Pratama');
        $response->assertSee('dimas.pratama@email.com');
        $response->assertSee('Member sejak');
        $response->assertSee('Penyewa Aktif');
        $response->assertSee('Informasi Pribadi');
        $response->assertSee('NAMA LENGKAP', false);
        $response->assertSee('Preferensi Olahraga Favorit');
        $response->assertSee('Futsal');
        $response->assertSee('Badminton');
        $response->assertSee('Rumah Senopati Residence');
        $response->assertSee('Aktivitas Booking Terkini');
        $response->assertSee('Lihat Semua Riwayat');
    }

    public function test_customer_can_update_profile(): void
    {
        $customer = User::factory()->create([
            'name' => 'Dimas Pratama',
            'email' => 'dimas.pratama@email.com',
            'no_hp' => '081234567890',
            'role' => 'customer',
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($customer)->put('/penyewa/profil', [
            'name' => 'Dimas Pratama Wijaya',
            'no_hp' => '081299998888',
        ]);

        $response->assertRedirect('/penyewa/profil');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
            'name' => 'Dimas Pratama Wijaya',
            'no_hp' => '081299998888',
        ]);
    }
}
