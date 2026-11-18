<?php

namespace Tests\Feature\Commission;

use App\Exceptions\Commission\CommissionRuleException;
use App\Exceptions\Commission\NoCommissionRateException;
use App\Services\Commission\CommissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\CreatesFixtures;
use Tests\TestCase;

class CommissionServiceTest extends TestCase
{
    use RefreshDatabase, CreatesFixtures;

    private CommissionService $svc;
    private int $city;
    private int $sport;

    protected function setUp(): void
    {
        parent::setUp();
        config(['commission.max_percent' => 50, 'commission.max_schedule_days' => 365, 'booking.service_fee' => 2500]);
        $this->svc = app(CommissionService::class);
        [$this->city, $this->sport] = $this->cityAndSport();
    }

    /** @return array{0:int,1:int} [id mitra, id lapangan] */
    private function mitraWithField(string $status = 'approved'): array
    {
        $mitra = $this->mitraProfile($this->makeUser('mitra'), ['status' => $status]);
        $field = $this->field($this->venue($mitra, $this->city, ['status_vanue' => 'aktif']), $this->sport, ['status_lapangan' => 'aktif']);

        return [$mitra, $field];
    }

    private function rate(?int $mitra, ?int $sport, ?string $persen, $from): int
    {
        return DB::table('commission_rates')->insertGetId([
            'scope_key' => CommissionService::scopeKey($mitra, $sport), 'mitra_id' => $mitra, 'sport_id' => $sport, 'persen' => $persen,
            'berlaku_mulai' => $from instanceof \DateTimeInterface ? $from->format('Y-m-d H:i:s') : $from, 'dibuat_oleh' => null,
            'catatan' => 'Tarif uji', 'created_at' => now(),
        ]);
    }

    // ------------------------------------------------------------ perhitungan

    public static function splits(): array
    {
        return [
            'normal 20%'                 => [150000, '20', 30000, 120000],
            'tarif naik 25%'             => [150000, '25', 37500, 112500],
            'tarif khusus 10%'           => [150000, '10', 15000, 135000],
            'pembulatan setengah naik'   => [133350, '15', 20003, 113347],
            'dua desimal'                => [100000, '33.33', 33330, 66670],
            'nol persen'                 => [150000, '0', 0, 150000],
            'seratus persen'             => [150000, '100', 150000, 0],
            'satu rupiah 50%'            => [1, '50', 1, 0],
            'harga nol'                  => [0, '20', 0, 0],
        ];
    }

    #[DataProvider('splits')]
    public function test_split_matches_the_agreed_examples(int $price, string $percent, int $komisi, int $mitra): void
    {
        $this->assertSame(['komisi' => $komisi, 'mitra' => $mitra], $this->svc->split($price, $percent));
    }

    public function test_split_always_adds_up_and_never_goes_out_of_range(): void
    {
        mt_srand(42);
        for ($i = 0; $i < 500; $i++) {
            $price = mt_rand(0, 5_000_000);
            $percent = mt_rand(0, 5000) / 100;

            $s = $this->svc->split($price, $percent);

            $this->assertSame($price, $s['komisi'] + $s['mitra']);
            $this->assertGreaterThanOrEqual(0, $s['komisi']);
            $this->assertLessThanOrEqual($price, $s['komisi']);
        }
    }

    public function test_split_rejects_invalid_input(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->svc->split(150000, '101');
    }

    // ---------------------------------------------------------------- tarif

    public function test_the_initial_global_rate_is_twenty_percent_for_everyone(): void
    {
        [$mitra] = $this->mitraWithField();

        $rate = $this->svc->resolve($mitra, $this->sport);

        $this->assertSame('20.00', $rate->persen);
        $this->assertSame('global', $rate->level);
    }

    public function test_a_future_rate_changes_nothing_until_its_time(): void
    {
        $this->rate(null, null, '25.00', now()->addDays(2));

        $this->assertSame('20.00', $this->svc->resolve(null)->persen);
        $this->assertSame('25.00', $this->svc->resolve(null, null, now()->addDays(3))->persen);
    }

    public function test_a_mitra_rate_beats_the_global_rate_and_leaves_other_mitra_alone(): void
    {
        [$a] = $this->mitraWithField();
        [$b] = $this->mitraWithField();
        $this->rate($a, null, '10.00', now()->subDay());

        $this->assertSame('10.00', $this->svc->resolve($a)->persen);
        $this->assertSame('mitra', $this->svc->resolve($a)->level);
        $this->assertSame('20.00', $this->svc->resolve($b)->persen);
    }

    public function test_an_empty_mitra_row_returns_the_mitra_to_the_global_rate(): void
    {
        [$a] = $this->mitraWithField();
        $this->rate($a, null, '10.00', now()->subDays(10));
        $this->rate($a, null, null, now()->subDays(2)); // "ikuti global"

        $this->assertSame('20.00', $this->svc->resolve($a)->persen);
        $this->assertSame('10.00', $this->svc->resolve($a, null, now()->subDays(5))->persen, 'Sebelum baris "ikuti", tarif khusus masih berlaku.');
    }

    public function test_priority_is_mitra_and_sport_then_mitra_then_sport_then_global(): void
    {
        [$a] = $this->mitraWithField();
        $this->rate(null, $this->sport, '12.00', now()->subDay());
        $this->assertSame('olahraga', $this->svc->resolve($a, $this->sport)->level);

        $this->rate($a, null, '10.00', now()->subDay());
        $this->assertSame('mitra', $this->svc->resolve($a, $this->sport)->level);

        $this->rate($a, $this->sport, '5.00', now()->subDay());
        $this->assertSame('mitra_olahraga', $this->svc->resolve($a, $this->sport)->level);
        $this->assertSame('5.00', $this->svc->resolve($a, $this->sport)->persen);
    }

    public function test_resolving_without_any_rate_fails_instead_of_assuming_zero(): void
    {
        DB::table('commission_rates')->delete();

        $this->expectException(NoCommissionRateException::class);
        $this->svc->resolve(null);
    }

    public function test_preview_charges_the_service_fee_once_and_never_commissions_it(): void
    {
        [$mitra] = $this->mitraWithField();

        $p = $this->svc->preview(150000, 2, $mitra);

        $this->assertSame(300000, $p['subtotal']);
        $this->assertSame(60000, $p['komisi']);
        $this->assertSame(240000, $p['mitra']);
        $this->assertSame(302500, $p['dibayar_penyewa']);
        $this->assertSame(62500, $p['platform']);
    }

    // ------------------------------------------------------------ penjadwalan

    private function admin()
    {
        return $this->makeUser('admin');
    }

    public function test_scheduling_adds_a_row_and_an_audit_entry_and_never_touches_old_rows(): void
    {
        $admin = $this->admin();
        $before = DB::table('commission_rates')->where('scope_key', 'g')->first();

        $id = $this->svc->schedule($admin, null, '25', now()->addDay(), 'Kenaikan tarif sesuai rapat tim');

        $this->assertDatabaseHas('commission_rates', ['id' => $id, 'scope_key' => 'g', 'persen' => '25.00', 'dibuat_oleh' => $admin->id]);
        $this->assertEquals($before, DB::table('commission_rates')->where('id', $before->id)->first());
        $this->assertDatabaseHas('audit_logs', ['entitas_type' => 'CommissionRate', 'entitas_id' => $id, 'aksi' => 'tambah_tarif', 'status_lama' => '20%', 'status_baru' => '25%']);
    }

    public function test_leaving_the_start_time_empty_means_now(): void
    {
        $this->svc->schedule($this->admin(), null, '15', null, 'Tarif berlaku langsung hari ini');

        $this->assertSame('15.00', $this->svc->resolve(null)->persen);
    }

    public static function badRules(): array
    {
        return [
            'tarif global kosong'      => ['global', null, '+1 day', 'persen'],
            'di atas batas'            => ['global', '51', '+1 day', 'persen'],
            'negatif'                  => ['global', '-5', '+1 day', 'persen'],
            'bukan angka'              => ['global', 'abc', '+1 day', 'persen'],
            'masa lalu'                => ['global', '25', '-2 days', 'berlaku_mulai'],
            'terlalu jauh'             => ['global', '25', '+400 days', 'berlaku_mulai'],
            'sama dengan yang berlaku' => ['global', '20', '+1 day', 'persen'],
        ];
    }

    #[DataProvider('badRules')]
    public function test_invalid_rates_are_refused_and_leave_no_trace(string $scope, ?string $persen, string $when, string $field): void
    {
        $count = DB::table('commission_rates')->count();

        try {
            $this->svc->schedule($this->admin(), null, $persen, now()->modify($when), 'Alasan yang cukup panjang');
            $this->fail('Seharusnya ditolak.');
        } catch (CommissionRuleException $e) {
            $this->assertSame($field, $e->field);
        }

        $this->assertSame($count, DB::table('commission_rates')->count());
        $this->assertDatabaseMissing('audit_logs', ['aksi' => 'tambah_tarif']);
    }

    public function test_a_short_reason_is_refused(): void
    {
        $this->expectException(CommissionRuleException::class);
        $this->svc->schedule($this->admin(), null, '25', now()->addDay(), 'pendek');
    }

    public function test_two_rates_at_the_same_time_for_one_scope_are_refused(): void
    {
        $at = now()->addDays(2)->startOfMinute();
        $this->svc->schedule($this->admin(), null, '25', $at, 'Tarif pertama pada waktu ini');

        $this->expectException(CommissionRuleException::class);
        $this->svc->schedule($this->admin(), null, '30', $at, 'Tarif kedua pada waktu yang sama');
    }

    public function test_only_approved_mitra_can_have_their_own_rate(): void
    {
        [$pending] = $this->mitraWithField('pending');

        try {
            $this->svc->schedule($this->admin(), $pending, '10', null, 'Mitra belum disetujui');
            $this->fail('Seharusnya ditolak.');
        } catch (CommissionRuleException $e) {
            $this->assertSame('mitra_id', $e->field);
        }

        $this->expectException(CommissionRuleException::class);
        $this->svc->schedule($this->admin(), 999999, '10', null, 'Mitra tidak ada sama sekali');
    }

    public function test_pinning_a_mitra_to_the_same_percent_as_global_is_allowed_but_following_twice_is_not(): void
    {
        [$mitra] = $this->mitraWithField();

        $this->svc->schedule($this->admin(), $mitra, '20', null, 'Dikunci pada 20 persen walau global berubah');
        $this->assertSame('mitra', $this->svc->resolve($mitra)->level);

        $this->svc->schedule($this->admin(), $mitra, null, now()->addMinutes(5), 'Kembali mengikuti tarif global');
        $this->expectException(CommissionRuleException::class);
        $this->svc->schedule($this->admin(), $mitra, null, now()->addMinutes(10), 'Sudah mengikuti, ini tidak perlu');
    }

    // --------------------------------------------------------------- penilaian

    /** @return array{0:int,1:int} [id booking, id mitra] */
    private function bookingWith(int $field, int $mitra, array $prices): array
    {
        $header = $this->booking($this->makeUser(), ['total_tagihan' => array_sum($prices) + 2500, 'status' => 'payment']);
        foreach ($prices as $price) {
            $this->bookingDetail($header, $field, ['harga_slot' => $price]);
        }

        return [$header, $mitra];
    }

    public function test_assessing_stores_rounded_amounts_per_detail_with_the_rate_used(): void
    {
        [$mitra, $field] = $this->mitraWithField();
        $rateId = $this->rate($mitra, null, '15.00', now()->subDay());
        [$header] = $this->bookingWith($field, $mitra, [133350, 133350, 133350]);

        $sum = $this->svc->assess($header);

        $this->assertSame(['details' => 3, 'komisi' => 60009, 'mitra' => 340041], $sum);
        $rows = DB::table('booking_details')->where('header_id', $header)->get();
        foreach ($rows as $row) {
            $this->assertEquals(20003, $row->komisi_nominal);
            $this->assertEquals(113347, $row->pendapatan_mitra);
            $this->assertSame('15.00', $row->komisi_persen);
            $this->assertSame($rateId, (int) $row->komisi_rate_id);
            $this->assertEquals($row->harga_slot, $row->komisi_nominal + $row->pendapatan_mitra);
        }
    }

    public function test_assessing_twice_changes_nothing(): void
    {
        [$mitra, $field] = $this->mitraWithField();
        [$header] = $this->bookingWith($field, $mitra, [150000]);

        $this->svc->assess($header);
        $snapshot = DB::table('booking_details')->where('header_id', $header)->get();
        $this->rate(null, null, '30.00', now()->subSecond()); // tarif berubah setelah dinilai
        $second = $this->svc->assess($header);

        $this->assertSame(['details' => 0, 'komisi' => 0, 'mitra' => 0], $second);
        $this->assertEquals($snapshot, DB::table('booking_details')->where('header_id', $header)->get());
    }

    public function test_a_later_rate_change_never_rewrites_earlier_assessments(): void
    {
        [$mitra, $field] = $this->mitraWithField();
        [$first] = $this->bookingWith($field, $mitra, [150000]);
        $this->svc->assess($first);

        $this->rate(null, null, '25.00', now()->addMinute());
        [$second] = $this->bookingWith($field, $mitra, [150000]);
        $this->svc->assess($second, now()->addMinutes(2));

        $this->assertEquals(30000, DB::table('booking_details')->where('header_id', $first)->value('komisi_nominal'));
        $this->assertEquals(37500, DB::table('booking_details')->where('header_id', $second)->value('komisi_nominal'));
    }

    public function test_assessment_fails_as_a_whole_when_no_rate_exists(): void
    {
        [$mitra, $field] = $this->mitraWithField();
        [$header] = $this->bookingWith($field, $mitra, [150000, 150000]);
        DB::table('commission_rates')->delete();

        try {
            $this->svc->assess($header);
            $this->fail('Seharusnya gagal.');
        } catch (NoCommissionRateException) {
            // diharapkan
        }

        $this->assertSame(2, DB::table('booking_details')->where('header_id', $header)->whereNull('komisi_nominal')->count());
    }
}