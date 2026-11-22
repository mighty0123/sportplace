<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\Master\MasterDataManager;
use App\Livewire\Admin\Pengguna\UserTable;
use App\Livewire\Admin\Transaksi\TransactionTable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\CreatesFixtures;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase, CreatesFixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite(); // tidak butuh hasil build aset untuk menguji akses
    }

    public static function adminPages(): array
    {
        return array_map(fn ($u) => [$u], [
            '/admin', '/admin/verifikasi', '/admin/moderasi', '/admin/master-data',
            '/admin/transaksi', '/admin/laporan', '/admin/pengguna', '/admin/profil',
        ]);
    }

    #[DataProvider('adminPages')]
    public function test_guest_is_sent_to_login(string $url): void
    {
        $this->get($url)->assertRedirect(route('login'));
    }

    #[DataProvider('adminPages')]
    public function test_mitra_and_customer_are_forbidden(string $url): void
    {
        $this->actingAs($this->makeUser('mitra'))->get($url)->assertForbidden();
        $this->actingAs($this->makeUser('customer'))->get($url)->assertForbidden();
    }

    #[DataProvider('adminPages')]
    public function test_active_admin_can_open_the_page(string $url): void
    {
        $this->actingAs($this->makeUser('admin'))->get($url)->assertOk();
    }

    public function test_suspended_admin_is_logged_out_on_next_request(): void
    {
        $admin = $this->makeUser('admin', ['status_akun' => 'suspend']);

        $this->actingAs($admin)->get('/admin')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_livewire_components_authorize_every_request_themselves(): void
    {
        $customer = $this->makeUser('customer');

        Livewire::actingAs($customer)->test(UserTable::class)->assertForbidden();
        Livewire::actingAs($customer)->test(MasterDataManager::class)->assertForbidden();
        Livewire::actingAs($customer)->test(TransactionTable::class)->assertForbidden();
    }
}