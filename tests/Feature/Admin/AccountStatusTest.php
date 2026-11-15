<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreatesFixtures;
use Tests\TestCase;

class AccountStatusTest extends TestCase
{
    use RefreshDatabase, CreatesFixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_admin_can_suspend_a_customer_and_it_is_audited(): void
    {
        $admin = $this->makeUser('admin');
        $customer = $this->makeUser('customer');

        $this->actingAs($admin)
            ->post(route('admin.pengguna.status', $customer->id), ['status' => 'suspend', 'alasan' => 'Laporan penipuan dari penyewa lain'])
            ->assertRedirect(route('admin.pengguna.show', $customer->id));

        $this->assertDatabaseHas('users', ['id' => $customer->id, 'status_akun' => 'suspend']);
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id, 'entitas_type' => 'User', 'entitas_id' => $customer->id,
            'aksi' => 'suspend', 'status_lama' => 'aktif', 'status_baru' => 'suspend',
        ]);
    }

    public function test_sanction_requires_a_reason_of_at_least_ten_characters(): void
    {
        $admin = $this->makeUser('admin');
        $customer = $this->makeUser('customer');

        $this->actingAs($admin)
            ->post(route('admin.pengguna.status', $customer->id), ['status' => 'banned', 'alasan' => 'pendek'])
            ->assertSessionHasErrors('alasan');

        $this->assertDatabaseHas('users', ['id' => $customer->id, 'status_akun' => 'aktif']);
    }

    public function test_reactivation_needs_no_reason(): void
    {
        $admin = $this->makeUser('admin');
        $customer = $this->makeUser('customer', ['status_akun' => 'suspend']);

        $this->actingAs($admin)->post(route('admin.pengguna.status', $customer->id), ['status' => 'aktif'])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', ['id' => $customer->id, 'status_akun' => 'aktif']);
    }

    public function test_admin_accounts_and_own_account_cannot_be_sanctioned(): void
    {
        $admin = $this->makeUser('admin');
        $other = $this->makeUser('admin');
        $reason = 'Percobaan menyanksi akun admin';

        $this->actingAs($admin)->post(route('admin.pengguna.status', $other->id), ['status' => 'suspend', 'alasan' => $reason])
            ->assertSessionHas('error');
        $this->actingAs($admin)->post(route('admin.pengguna.status', $admin->id), ['status' => 'suspend', 'alasan' => $reason])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $other->id, 'status_akun' => 'aktif']);
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'status_akun' => 'aktif']);
    }

    public function test_repeating_the_same_change_reports_a_conflict(): void
    {
        $admin = $this->makeUser('admin');
        $customer = $this->makeUser('customer', ['status_akun' => 'suspend']);

        $this->actingAs($admin)->post(route('admin.pengguna.status', $customer->id), ['status' => 'suspend', 'alasan' => 'Alasan yang cukup panjang'])
            ->assertSessionHas('error');
    }

    public function test_sanction_deletes_the_users_sessions(): void
    {
        config(['session.driver' => 'database']);
        $admin = $this->makeUser('admin');
        $customer = $this->makeUser('customer');
        DB::table('sessions')->insert(['id' => 'sesi-uji', 'user_id' => $customer->id, 'ip_address' => '127.0.0.1', 'user_agent' => 'uji', 'payload' => 'x', 'last_activity' => time()]);

        $this->actingAs($admin)->post(route('admin.pengguna.status', $customer->id), ['status' => 'suspend', 'alasan' => 'Aktivitas mencurigakan berulang']);

        $this->assertDatabaseMissing('sessions', ['id' => 'sesi-uji']);
    }

    public function test_suspended_user_is_logged_out_on_next_request_with_a_generic_message(): void
    {
        $customer = $this->makeUser('customer', ['status_akun' => 'suspend']);

        $this->actingAs($customer)->get('/penyewa/dashboard')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_non_admin_cannot_change_account_status(): void
    {
        $customer = $this->makeUser('customer');
        $victim = $this->makeUser('customer');

        $this->actingAs($customer)->post(route('admin.pengguna.status', $victim->id), ['status' => 'suspend', 'alasan' => 'Tidak berwenang sama sekali'])
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $victim->id, 'status_akun' => 'aktif']);
    }
}