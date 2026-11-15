<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CreatesFixtures;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase, CreatesFixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function validPassword(): array
    {
        return ['current_password' => 'Password123', 'password' => 'SandiBaruKuat9', 'password_confirmation' => 'SandiBaruKuat9'];
    }

    public function test_name_and_phone_can_be_updated_and_phone_verification_resets(): void
    {
        $admin = $this->makeUser('admin', ['phone_verified_at' => now()]);

        $this->actingAs($admin)->put(route('admin.profile.update'), ['name' => 'Admin Baru Nama', 'no_hp' => '0812-3456-7890'])
            ->assertRedirect(route('admin.profile'));

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'name' => 'Admin Baru Nama', 'no_hp' => '081234567890', 'phone_verified_at' => null]);
        $this->assertDatabaseHas('audit_logs', ['entitas_id' => $admin->id, 'aksi' => 'ubah_profil']);
    }

    public function test_email_cannot_be_changed_from_the_profile_page(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)->put(route('admin.profile.update'), ['name' => 'Admin Baru Nama', 'no_hp' => '081234567890', 'email' => 'peretas@example.test']);

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'email' => $admin->email]);
    }

    public function test_invalid_or_already_used_phone_numbers_are_rejected(): void
    {
        $admin = $this->makeUser('admin');
        $other = $this->makeUser('customer');

        $this->actingAs($admin)->put(route('admin.profile.update'), ['name' => 'Admin Uji', 'no_hp' => '12345'])->assertSessionHasErrors('no_hp');
        $this->actingAs($admin)->put(route('admin.profile.update'), ['name' => 'Admin Uji', 'no_hp' => $other->no_hp])->assertSessionHasErrors('no_hp');
    }

    public function test_password_change_requires_the_current_password(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)->put(route('admin.profile.password'), ['current_password' => 'salah'] + $this->validPassword())
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('Password123', DB::table('users')->where('id', $admin->id)->value('password')));
    }

    public function test_weak_or_unchanged_passwords_are_rejected(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)->put(route('admin.profile.password'), ['current_password' => 'Password123', 'password' => 'abc', 'password_confirmation' => 'abc'])
            ->assertSessionHasErrors('password');
        $this->actingAs($admin)->put(route('admin.profile.password'), ['current_password' => 'Password123', 'password' => 'Password123', 'password_confirmation' => 'Password123'])
            ->assertSessionHasErrors('password');
    }

    public function test_password_change_succeeds_logs_out_other_devices_and_never_logs_the_password(): void
    {
        config(['session.driver' => 'database']);
        $admin = $this->makeUser('admin');
        DB::table('sessions')->insert(['id' => 'perangkat-lain', 'user_id' => $admin->id, 'ip_address' => '10.0.0.9', 'user_agent' => 'uji', 'payload' => 'x', 'last_activity' => time()]);

        $this->actingAs($admin)->put(route('admin.profile.password'), $this->validPassword())->assertRedirect(route('admin.profile'));

        $this->assertTrue(Hash::check('SandiBaruKuat9', DB::table('users')->where('id', $admin->id)->value('password')));
        $this->assertDatabaseMissing('sessions', ['id' => 'perangkat-lain']);

        $note = (string) DB::table('audit_logs')->where('aksi', 'ganti_password')->value('catatan');
        $this->assertNotSame('', $note);
        $this->assertStringNotContainsString('SandiBaruKuat9', $note);
    }

    public function test_logging_out_other_devices_requires_the_password(): void
    {
        config(['session.driver' => 'database']);
        $admin = $this->makeUser('admin');
        DB::table('sessions')->insert(['id' => 'perangkat-lain', 'user_id' => $admin->id, 'ip_address' => '10.0.0.9', 'user_agent' => 'uji', 'payload' => 'x', 'last_activity' => time()]);

        $this->actingAs($admin)->post(route('admin.profile.sessions.logout'), ['logout_password' => 'salah'])->assertSessionHasErrors('logout_password');
        $this->assertDatabaseHas('sessions', ['id' => 'perangkat-lain']);

        $this->actingAs($admin)->post(route('admin.profile.sessions.logout'), ['logout_password' => 'Password123'])->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('sessions', ['id' => 'perangkat-lain']);
    }

    public function test_the_profile_page_is_not_cacheable(): void
    {
        $this->actingAs($this->makeUser('admin'))->get(route('admin.profile'))->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private');
    }
}