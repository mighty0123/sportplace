<?php

namespace Tests\Feature\Commission;

use App\Livewire\Admin\Komisi\CommissionHistory;
use App\Livewire\Admin\Komisi\CommissionPreview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\Concerns\CreatesFixtures;
use Tests\TestCase;

class CommissionAdminTest extends TestCase
{
    use RefreshDatabase, CreatesFixtures;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function when(string $modifier = '+2 days'): string
    {
        return now()->modify($modifier)->format('Y-m-d\TH:i');
    }

    private function submit(array $data)
    {
        return $this->actingAs($this->makeUser('admin'))->post(route('admin.komisi.store'), $data);
    }

    public function test_only_active_admins_can_open_the_page(): void
    {
        $this->get(route('admin.komisi.index'))->assertRedirect(route('login'));
        $this->actingAs($this->makeUser('mitra'))->get(route('admin.komisi.index'))->assertForbidden();
        $this->actingAs($this->makeUser('customer'))->post(route('admin.komisi.store'), [])->assertForbidden();
        $this->actingAs($this->makeUser('admin'))->get(route('admin.komisi.index'))->assertOk()->assertSee('Pengaturan Komisi')->assertSee('20%');
    }

    public function test_admin_can_schedule_a_global_rate_and_it_shows_in_the_history(): void
    {
        $this->submit(['scope' => 'global', 'persen' => '25', 'berlaku_mulai' => $this->when(), 'catatan' => 'Kenaikan tarif sesuai rapat tim'])
            ->assertRedirect(route('admin.komisi.index'))->assertSessionHas('success');

        $this->assertDatabaseHas('commission_rates', ['scope_key' => 'g', 'persen' => '25.00']);
        $this->assertDatabaseHas('audit_logs', ['entitas_type' => 'CommissionRate', 'aksi' => 'tambah_tarif']);

        Livewire::actingAs($this->makeUser('admin'))->test(CommissionHistory::class)->assertSee('Terjadwal')->assertSee('Berlaku')->assertSee('25%');
    }

    public function test_a_comma_decimal_is_accepted(): void
    {
        $this->submit(['scope' => 'global', 'persen' => '12,5', 'berlaku_mulai' => $this->when(), 'catatan' => 'Uji pemisah desimal koma'])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('commission_rates', ['scope_key' => 'g', 'persen' => '12.50']);
    }

    public function test_reason_percent_and_dates_are_validated(): void
    {
        $this->submit(['scope' => 'global', 'persen' => '25', 'catatan' => 'pendek'])->assertSessionHasErrors('catatan');
        $this->submit(['scope' => 'global', 'persen' => 'abc', 'catatan' => 'Alasan yang cukup panjang'])->assertSessionHasErrors('persen');
        $this->submit(['scope' => 'global', 'persen' => '90', 'catatan' => 'Alasan yang cukup panjang'])->assertSessionHasErrors('persen');
        $this->submit(['scope' => 'global', 'catatan' => 'Alasan yang cukup panjang'])->assertSessionHasErrors('persen');
        $this->submit(['scope' => 'global', 'persen' => '25', 'berlaku_mulai' => $this->when('-2 days'), 'catatan' => 'Alasan yang cukup panjang'])->assertSessionHasErrors('berlaku_mulai');
        $this->submit(['scope' => 'ngawur', 'persen' => '25', 'catatan' => 'Alasan yang cukup panjang'])->assertSessionHasErrors('scope');

        $this->assertSame(1, DB::table('commission_rates')->count(), 'Hanya tarif awal yang ada.');
    }

    public function test_repeating_the_current_rate_is_refused(): void
    {
        $this->submit(['scope' => 'global', 'persen' => '20', 'catatan' => 'Mengulang tarif yang sudah berlaku'])->assertSessionHasErrors('persen');
    }

    public function test_mitra_rates_need_an_approved_mitra_and_an_empty_percent_means_follow_global(): void
    {
        $approved = $this->mitraProfile($this->makeUser('mitra'), ['status' => 'approved']);
        $pending = $this->mitraProfile($this->makeUser('mitra'), ['status' => 'pending']);

        $this->submit(['scope' => 'mitra', 'mitra_id' => $pending, 'persen' => '10', 'catatan' => 'Mitra belum disetujui'])->assertSessionHasErrors('mitra_id');
        $this->submit(['scope' => 'mitra', 'mitra_id' => $approved, 'persen' => '10', 'catatan' => 'Tarif khusus kemitraan awal'])->assertSessionHasNoErrors();
        $this->submit(['scope' => 'mitra', 'mitra_id' => $approved, 'persen' => '', 'berlaku_mulai' => $this->when('+1 day'), 'catatan' => 'Kembali mengikuti tarif global'])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('commission_rates', ['scope_key' => "m{$approved}", 'persen' => '10.00']);
        $this->assertDatabaseHas('commission_rates', ['scope_key' => "m{$approved}", 'persen' => null]);
    }

    public function test_the_mitra_form_can_be_prefilled_from_the_overrides_list(): void
    {
        $mitra = $this->mitraProfile($this->makeUser('mitra'), ['status' => 'approved', 'nama_usaha' => 'Arena Uji']);

        $this->actingAs($this->makeUser('admin'))->get(route('admin.komisi.index', ['mitra' => $mitra]))
            ->assertOk()->assertSee('Arena Uji');
    }

    public function test_rates_can_never_be_edited_or_deleted_because_no_such_route_exists(): void
    {
        $admin = $this->makeUser('admin');
        $id = DB::table('commission_rates')->value('id');

        foreach (['put', 'patch', 'delete'] as $verb) {
            $status = $this->actingAs($admin)->{$verb}("/admin/komisi/tarif/{$id}")->getStatusCode();
            $this->assertContains($status, [404, 405], "{$verb} tidak boleh berhasil.");
        }

        $this->assertDatabaseHas('commission_rates', ['id' => $id, 'persen' => '20.00']);
    }

    public function test_the_preview_shows_the_agreed_example(): void
    {
        Livewire::actingAs($this->makeUser('admin'))->test(CommissionPreview::class)
            ->set('price', '150.000')->set('slots', '1')
            ->assertSee('30.000')->assertSee('120.000')->assertSee('152.500');
    }

    public function test_the_preview_rejects_nonsense_without_an_error_page(): void
    {
        Livewire::actingAs($this->makeUser('admin'))->test(CommissionPreview::class)
            ->set('price', 'abc')->assertSee('harga slot yang valid');
    }

    public function test_the_components_authorize_every_request_themselves(): void
    {
        $customer = $this->makeUser('customer');

        Livewire::actingAs($customer)->test(CommissionPreview::class)->assertForbidden();
        Livewire::actingAs($customer)->test(CommissionHistory::class)->assertForbidden();
    }
}