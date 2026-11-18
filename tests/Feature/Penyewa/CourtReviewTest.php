<?php

namespace Tests\Feature\Penyewa;

use App\Models\Field;
use App\Models\MasterCity;
use App\Models\MasterSport;
use App\Models\MitraProfile;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourtReviewTest extends TestCase
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
            'name' => 'Dimas Pratama',
            'email' => 'dimas@example.com',
        ]);

        $city = MasterCity::create(['nama_kota' => 'Jakarta Selatan', 'provinsi' => 'DKI Jakarta']);
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
            'status_verifikasi' => 'verified',
        ]);

        $this->venue = Venue::create([
            'mitra_id' => $mitraProfile->id,
            'city_id' => $city->id,
            'nama_venue' => 'Viva Arena Futsal',
            'alamat_lengkap' => 'Jl. Kemang Raya No. 12',
            'jam_buka' => '08:00:00',
            'jam_tutup' => '23:00:00',
            'status_vanue' => 'aktif',
            'status_operasional' => 'buka',
        ]);

        $this->field = Field::create([
            'venue_id' => $this->venue->id,
            'sport_id' => $sport->id,
            'nama_lapangan' => 'Lapangan Futsal Vinyl A',
            'tipe_lantai' => 'vinyl',
            'ukuran' => '25x15m',
            'harga_perjam' => 200000,
            'status_lapangan' => 'aktif',
            'status_operasional' => 'tersedia',
        ]);
    }

    public function test_court_detail_displays_review_button_and_section(): void
    {
        $response = $this->get('/penyewa/lapangan/' . $this->field->id);

        $response->assertStatus(200);
        $response->assertSee('Lapangan Futsal Vinyl A');
        $response->assertSee('Beri Ulasan');
        $response->assertSee('Review Lapangan Ini');
        $response->assertSee('Tulis Review Lapangan');
        $response->assertSee('Ulasan', false);
        $response->assertSee('Beri Review Lapangan');
        $response->assertSee('review-modal');
    }

    public function test_user_can_submit_review_via_ajax(): void
    {
        $payload = [
            'rating'   => 5,
            'nama'     => 'Dimas Pratama',
            'komentar' => 'Lapangan sangat mantap dan bersih! Lampunya terang sekali.',
            'tags'     => 'Lapangan Bersih,Lampu Terang',
        ];

        $response = $this->actingAs($this->customer)
            ->postJson(route('penyewa.lapangan.review', $this->field->id), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status'  => 'success',
            'review'  => [
                'name'    => 'Dimas Pratama',
                'rating'  => 5,
                'comment' => 'Lapangan sangat mantap dan bersih! Lampunya terang sekali.',
            ],
        ]);
    }

    public function test_user_can_submit_review_and_see_it_on_court_detail(): void
    {
        $payload = [
            'rating'   => 5,
            'nama'     => 'Budi Santoso',
            'komentar' => 'Main badminton di sini sangat menyenangkan, sirkulasi angin oke.',
            'tags'     => 'Fasilitas Lengkap',
        ];

        $submitResponse = $this->actingAs($this->customer)
            ->post(route('penyewa.lapangan.review', $this->field->id), $payload);

        $submitResponse->assertRedirect();

        // Refresh detail page with same session
        $pageResponse = $this->actingAs($this->customer)->get('/penyewa/lapangan/' . $this->field->id);
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Budi Santoso');
        $pageResponse->assertSee('Main badminton di sini sangat menyenangkan, sirkulasi angin oke.');
    }
}
