<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicDashboardTest extends TestCase
{
    public function test_public_dashboard_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('SportPlace');
        $response->assertSee('Cari Lapangan.');
        $response->assertSee('Main Tanpa Ribet.');
    }

    public function test_public_dashboard_contains_sport_categories(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Main Olahraga Favoritmu');
        $response->assertSee('Futsal');
        $response->assertSee('Badminton');
        $response->assertSee('Basket');
        $response->assertSee('Tenis');
        $response->assertSee('Voli');
        $response->assertSee('Tenis Meja');
    }

    public function test_public_dashboard_contains_popular_fields(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Lapangan Populer');
        $response->assertSee('Arena Sport Center');
        $response->assertSee('Green Court Arena');
        $response->assertSee('Bali Basket Hub');
        $response->assertSee('Sunrise Tennis Club');
        $response->assertSee('Rp 80.000');
        $response->assertSee('Rp 120.000');
    }

    public function test_public_dashboard_contains_features_and_steps(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Booking Lapangan Lebih Mudah');
        $response->assertSee('Banyak Pilihan');
        $response->assertSee('Harga Transparan');
        $response->assertSee('Jadwal Jelas');
        $response->assertSee('Booking Lebih Praktis');

        $response->assertSee('Cara Booking');
        $response->assertSee('Cari');
        $response->assertSee('Pilih Lapangan');
        $response->assertSee('Pilih Jadwal');
        $response->assertSee('Booking');

        $response->assertSee('Lapangan Terpilih untuk Kamu');
        $response->assertSee('Siap Mulai Main?');
    }
}
