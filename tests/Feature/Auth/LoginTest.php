<?php

namespace Tests\Feature\Auth;

use App\Models\MitraProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * FR-AUTH-003 / PUB-04. AC-FR-AUTH-003-01..03.
 */
class LoginTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ], $overrides));
    }

    public function test_customer_login_berhasil_dan_diarahkan_ke_beranda(): void
    {
        // AC-FR-AUTH-003-01
        $user = $this->makeUser(['role' => 'customer']);

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password123']);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_mitra_pending_diarahkan_ke_halaman_status_verifikasi(): void
    {
        // AC-FR-AUTH-003-02
        $user = $this->makeUser(['role' => 'mitra']);
        MitraProfile::create([
            'user_id' => $user->id,
            'no_ktp' => '1234567890123456',
            'npwp' => '12.345.678.9-012.345',
            'status' => 'pending',
        ]);

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password123']);

        $response->assertRedirect(route('mitra.status'));
    }

    public function test_mitra_rejected_diarahkan_ke_halaman_status_verifikasi(): void
    {
        $user = $this->makeUser(['role' => 'mitra']);
        MitraProfile::create([
            'user_id' => $user->id,
            'no_ktp' => '1234567890123456',
            'npwp' => '12.345.678.9-012.345',
            'status' => 'rejected',
            'alasan_penolakan' => 'Dokumen tidak jelas',
        ]);

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password123']);

        $response->assertRedirect(route('mitra.status'));
    }

    public function test_mitra_approved_diarahkan_ke_dashboard(): void
    {
        $user = $this->makeUser(['role' => 'mitra']);
        MitraProfile::create([
            'user_id' => $user->id,
            'no_ktp' => '1234567890123456',
            'npwp' => '12.345.678.9-012.345',
            'status' => 'approved',
        ]);

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password123']);

        $response->assertRedirect(route('mitra.dashboard'));
    }

    public function test_mitra_tanpa_profil_diarahkan_ke_pendaftaran(): void
    {
        $user = $this->makeUser(['role' => 'mitra']);

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password123']);

        $response->assertRedirect(route('mitra.onboarding.create'));
    }

    public function test_password_salah_tidak_membuat_sesi(): void
    {
        // AC-FR-AUTH-003-03
        $user = $this->makeUser();

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'salahsekali']);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_email_tidak_terdaftar_pesan_generik(): void
    {
        $response = $this->post('/login', ['email' => 'tidakada@example.com', 'password' => 'password123']);

        $response->assertSessionHasErrors('email');
        $response->assertSessionHasErrors(['email' => 'Email atau password salah.']);
        $this->assertGuest();
    }

    public function test_akun_suspend_ditolak(): void
    {
        $user = $this->makeUser(['status_akun' => 'suspend']);

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password123']);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_akun_banned_ditolak(): void
    {
        $user = $this->makeUser(['status_akun' => 'banned']);

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password123']);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_akun_belum_verifikasi_otp_diarahkan_ke_layar_otp(): void
    {
        $user = $this->makeUser(['email_verified_at' => null]);

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password123']);

        $response->assertRedirect(route('otp.verify.show'));
        $this->assertGuest();
    }

    public function test_setelah_login_dari_halaman_terproteksi_kembali_ke_halaman_asal(): void
    {
        $user = $this->makeUser(['role' => 'mitra']);
        MitraProfile::create([
            'user_id' => $user->id,
            'no_ktp' => '1234567890123456',
            'npwp' => '12.345.678.9-012.345',
            'status' => 'approved',
        ]);

        // Akses halaman terproteksi tanpa login → disimpan sebagai intended URL.
        $this->get('/mitra/dashboard')->assertRedirect(route('login'));

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password123']);

        $response->assertRedirect(route('mitra.dashboard'));
    }

    public function test_halaman_login_tidak_dapat_diakses_saat_sudah_login(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect();
    }

    public function test_login_dibatasi_rate_limit_setelah_banyak_percobaan_gagal(): void
    {
        $user = $this->makeUser();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'salah']);
        }

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password123']);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
