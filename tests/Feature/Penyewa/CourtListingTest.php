<?php

namespace Tests\Feature\Penyewa;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourtListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/penyewa/lapangan');
        $response->assertRedirect('/login');

        $responseNew = $this->get('/penyewa/booking/baru');
        $responseNew->assertRedirect('/login');
    }

    public function test_authenticated_customer_can_view_court_listing(): void
    {
        $customer = User::factory()->create([
            'name' => 'Dimas Pratama',
            'email' => 'dimas@sportplace.com',
            'role' => 'customer',
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($customer)->get('/penyewa/lapangan');

        $response->assertStatus(200);
        $response->assertSee('Cari & Booking Lapangan');
        $response->assertSee('Semua Olahraga');
        $response->assertSee('Futsal');
        $response->assertSee('Badminton');
        $response->assertSee('Tenis');
        $response->assertSee('Basket');
        $response->assertSee('Viva Arena Futsal');
        $response->assertSee('Smash Center Arena');
        $response->assertSee('Champion Tennis Club');
        $response->assertSee('Booking');
    }

    public function test_filter_by_sport_shows_only_matching_courts(): void
    {
        $customer = User::factory()->create([
            'name' => 'Dimas Pratama',
            'email' => 'dimas@sportplace.com',
            'role' => 'customer',
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($customer)->get('/penyewa/lapangan?sport=futsal');

        $response->assertStatus(200);
        $response->assertSee('Viva Arena Futsal');
        $response->assertDontSee('Champion Tennis Club');
    }

    public function test_dashboard_booking_button_links_to_court_listing(): void
    {
        $customer = User::factory()->create([
            'name' => 'Dimas Pratama',
            'email' => 'dimas@sportplace.com',
            'role' => 'customer',
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($customer)->get('/penyewa/dashboard');

        $response->assertStatus(200);
        $response->assertSee('/penyewa/lapangan');
    }
}
