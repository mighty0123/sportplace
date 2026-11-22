<?php

namespace Tests\Feature\Admin;

use App\Services\Admin\MasterDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\Concerns\CreatesFixtures;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase, CreatesFixtures;

    private MasterDataService $svc;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->svc = app(MasterDataService::class);
    }

    private function fails(string $tipe, array $data, ?int $id = null): bool
    {
        $rules = $this->svc->rules($tipe, $id, $data);

        return Validator::make($data, array_intersect_key($rules, $data))->fails();
    }

    private function sport(string $name, string $slug): int
    {
        return DB::table('master_sports')->insertGetId(['nama_olahraga' => $name, 'slug' => $slug, 'icon_url' => '/icons/x.png', 'status_olahraga' => 'aktif']);
    }

    public function test_sport_name_must_be_unique_ignoring_case(): void
    {
        $this->sport('Futsal', 'futsal');

        $this->assertTrue($this->fails('olahraga', ['nama' => 'futsal']));
        $this->assertFalse($this->fails('olahraga', ['nama' => 'Padel']));
    }

    public function test_names_that_produce_the_same_slug_are_rejected_on_create(): void
    {
        $this->sport('Mini Soccer', 'mini-soccer');

        $this->assertTrue($this->fails('olahraga', ['nama' => 'Mini-Soccer']));
        $this->assertTrue($this->fails('olahraga', ['nama' => '!!!']), 'Nama tanpa huruf atau angka tidak menghasilkan slug.');
    }

    public function test_city_is_unique_per_province(): void
    {
        DB::table('master_cities')->insert(['nama_kota' => 'Bandung', 'provinsi' => 'Jawa Barat', 'status_kota' => 'aktif']);

        $this->assertTrue($this->fails('kota', ['nama' => 'Bandung', 'provinsi' => 'Jawa Barat']));
        $this->assertFalse($this->fails('kota', ['nama' => 'Bandung', 'provinsi' => 'Banten']));
    }

    public function test_creating_a_sport_stores_a_safe_icon_and_an_automatic_slug(): void
    {
        $admin = $this->makeUser('admin');

        $this->svc->save('olahraga', null, ['nama' => 'Padel'], UploadedFile::fake()->image('ikon.png', 64, 64), $admin);

        $row = DB::table('master_sports')->where('nama_olahraga', 'Padel')->first();
        $this->assertSame('padel', $row->slug);
        $this->assertStringStartsWith('/storage/master-icons/', $row->icon_url);
        Storage::disk('public')->assertExists(Str::after($row->icon_url, '/storage/'));
        $this->assertDatabaseHas('audit_logs', ['entitas_type' => 'MasterSport', 'entitas_id' => $row->id, 'aksi' => 'tambah']);
    }

    public function test_renaming_keeps_the_slug(): void
    {
        $admin = $this->makeUser('admin');
        $id = $this->sport('Padel', 'padel');

        $this->svc->save('olahraga', $id, ['nama' => 'Padel Tenis'], null, $admin);

        $this->assertDatabaseHas('master_sports', ['id' => $id, 'nama_olahraga' => 'Padel Tenis', 'slug' => 'padel']);
    }

    public function test_replacing_an_icon_deletes_only_icons_uploaded_through_the_page(): void
    {
        $admin = $this->makeUser('admin');
        $id = $this->sport('Padel', 'padel'); // ikon seed: /icons/x.png, bukan milik halaman ini

        $this->svc->save('olahraga', $id, ['nama' => 'Padel'], UploadedFile::fake()->image('a.png', 64, 64), $admin);
        $first = DB::table('master_sports')->where('id', $id)->value('icon_url');
        Storage::disk('public')->assertExists(Str::after($first, '/storage/'));

        $this->svc->save('olahraga', $id, ['nama' => 'Padel'], UploadedFile::fake()->image('b.png', 64, 64), $admin);
        $second = DB::table('master_sports')->where('id', $id)->value('icon_url');

        $this->assertNotSame($first, $second);
        Storage::disk('public')->assertMissing(Str::after($first, '/storage/'));
        Storage::disk('public')->assertExists(Str::after($second, '/storage/'));
    }

    public function test_deactivating_and_reactivating_are_audited_and_idempotent(): void
    {
        $admin = $this->makeUser('admin');
        $id = $this->sport('Padel', 'padel');

        $this->svc->setStatus('olahraga', $id, 'nonaktif', $admin);
        $this->assertDatabaseHas('master_sports', ['id' => $id, 'status_olahraga' => 'nonaktif']);
        $this->assertDatabaseHas('audit_logs', ['entitas_id' => $id, 'aksi' => 'nonaktifkan']);

        $again = $this->svc->setStatus('olahraga', $id, 'nonaktif', $admin);
        $this->assertStringContainsString('sudah', $again);
        $this->assertDatabaseCount('audit_logs', 1);

        $this->svc->setStatus('olahraga', $id, 'aktif', $admin);
        $this->assertDatabaseHas('master_sports', ['id' => $id, 'status_olahraga' => 'aktif']);
    }
}