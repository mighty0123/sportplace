<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesFixtures;
use Tests\TestCase;

class ListingModerationTest extends TestCase
{
    use RefreshDatabase, CreatesFixtures;

    private const VENUE_CHECKLIST = ['info_sesuai' => 1, 'deskripsi_layak' => 1];
    private const FIELD_CHECKLIST = ['foto_layak' => 1, 'harga_wajar' => 1];

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    /** @return array{0:int,1:int,2:int} [id mitra, id venue, id lapangan] */
    private function listing(string $mitraStatus = 'approved', array $field = []): array
    {
        [$city, $sport] = $this->cityAndSport();
        $mitra = $this->mitraProfile($this->makeUser('mitra'), ['status' => $mitraStatus]);
        $venue = $this->venue($mitra, $city);

        return [$mitra, $venue, $this->field($venue, $sport, $field)];
    }

    public function test_field_cannot_be_approved_until_its_venue_is_active(): void
    {
        [, $venue, $field] = $this->listing();

        $this->actingAs($this->makeUser('admin'))->post(route('admin.moderasi.approve', ['lapangan', $field]), ['checklist' => self::FIELD_CHECKLIST])
            ->assertRedirect(route('admin.moderasi.show', ['lapangan', $field]))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('fields', ['id' => $field, 'status_lapangan' => 'pending_review']);
    }

    public function test_venue_then_field_can_be_approved_in_order(): void
    {
        $admin = $this->makeUser('admin');
        [, $venue, $field] = $this->listing();

        $this->actingAs($admin)->post(route('admin.moderasi.approve', ['venue', $venue]), ['checklist' => self::VENUE_CHECKLIST]);
        $this->assertDatabaseHas('venues', ['id' => $venue, 'status_vanue' => 'aktif', 'reviewed_by' => $admin->id]);

        $this->actingAs($admin)->post(route('admin.moderasi.approve', ['lapangan', $field]), ['checklist' => self::FIELD_CHECKLIST]);
        $this->assertDatabaseHas('fields', ['id' => $field, 'status_lapangan' => 'aktif', 'reviewed_by' => $admin->id]);
        $this->assertDatabaseHas('audit_logs', ['entitas_type' => 'Field', 'entitas_id' => $field, 'aksi' => 'setujui']);
    }

    public function test_venue_of_an_unapproved_mitra_cannot_be_approved(): void
    {
        [, $venue] = $this->listing('pending');

        $this->actingAs($this->makeUser('admin'))->post(route('admin.moderasi.approve', ['venue', $venue]), ['checklist' => self::VENUE_CHECKLIST])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('venues', ['id' => $venue, 'status_vanue' => 'pending_review']);
    }

    public function test_field_with_non_positive_price_cannot_be_approved(): void
    {
        $admin = $this->makeUser('admin');
        [, $venue, $field] = $this->listing('approved', ['harga_perjam' => 0]);
        $this->actingAs($admin)->post(route('admin.moderasi.approve', ['venue', $venue]), ['checklist' => self::VENUE_CHECKLIST]);

        $this->actingAs($admin)->post(route('admin.moderasi.approve', ['lapangan', $field]), ['checklist' => self::FIELD_CHECKLIST])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('fields', ['id' => $field, 'status_lapangan' => 'pending_review']);
    }

    public function test_rejecting_requires_a_reason_and_cannot_be_repeated(): void
    {
        $admin = $this->makeUser('admin');
        [, $venue] = $this->listing();

        $this->actingAs($admin)->post(route('admin.moderasi.reject', ['venue', $venue]), ['alasan_penolakan' => 'x'])->assertSessionHasErrors('alasan_penolakan');

        $this->actingAs($admin)->post(route('admin.moderasi.reject', ['venue', $venue]), ['alasan_penolakan' => 'Deskripsi menyesatkan pengunjung']);
        $this->assertDatabaseHas('venues', ['id' => $venue, 'status_vanue' => 'rejected', 'alasan_penolakan' => 'Deskripsi menyesatkan pengunjung']);

        $this->actingAs($admin)->post(route('admin.moderasi.reject', ['venue', $venue]), ['alasan_penolakan' => 'Percobaan kedua yang sama'])
            ->assertSessionHas('error');
    }

    public function test_unknown_listing_type_is_not_found(): void
    {
        $this->actingAs($this->makeUser('admin'))->get('/admin/moderasi/bukan-jenis/1')->assertNotFound();
    }
}