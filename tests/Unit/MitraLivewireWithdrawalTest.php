<?php

namespace Tests\Unit;

use App\Livewire\Mitra\Withdrawals\WithdrawalManager;
use App\Models\MitraProfile;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Services\Mitra\CommissionResolver;
use Carbon\Carbon;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\TestCase;

class MitraLivewireWithdrawalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $app = require dirname(__DIR__, 2).'/bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();
        Carbon::setTestNow('2026-11-18 10:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function createMockWithdrawalManager(?Wallet $wallet = null): WithdrawalManager
    {
        $profile = new MitraProfile;
        $profile->id = 10;
        $profile->status = 'approved';
        $profile->bank_nama = 'BCA';
        $profile->nomor_rekening = '1234567890';
        $profile->nama_rekening = 'Budi Mitra Futsal';

        $user = new User;
        $user->id = 10;
        $user->name = 'Budi Mitra';
        $user->role = 'mitra';
        $user->setRelation('mitraProfile', $profile);

        if (! $wallet) {
            $wallet = new Wallet;
            $wallet->id = 5;
            $wallet->user_id = 10;
            $wallet->saldo_aktif = 250000.0;
            $wallet->saldo_tertahan = 100000.0;
        }

        $user->setRelation('wallet', $wallet);

        $manager = new class extends WithdrawalManager
        {
            public ?User $currentUser = null;

            public ?Wallet $mockWallet = null;

            public array $dispatchedEvents = [];

            public function wallet(): Wallet
            {
                return $this->mockWallet ?? parent::wallet();
            }

            public function calculateTotalWithdrawn(int $userId): float
            {
                return 0.0;
            }

            public function calculatePendingWithdrawn(int $userId): float
            {
                return 0.0;
            }

            public function syncUncreditedCompletedBookings(CommissionResolver $commissionResolver): void
            {
                // No-op in unit test to avoid hitting MySQL socket
            }

            public function dispatch($event, ...$params): void
            {
                $this->dispatchedEvents[] = ['event' => $event, 'params' => $params];
            }

            public function resetPage($pageName = 'page'): void
            {
                // No-op in unit test
            }
        };

        $manager->currentUser = $user;
        $manager->mockWallet = $wallet;

        return $manager;
    }

    public function test_initial_state_and_summary_metrics(): void
    {
        $manager = $this->createMockWithdrawalManager();

        $this->assertEquals('withdrawals', $manager->activeTab);
        $this->assertEquals('semua', $manager->statusFilter);
        $this->assertFalse($manager->showWithdrawModal);
        $this->assertFalse($manager->showConfirmModal);

        $stats = $manager->stats;
        $this->assertEquals(250000.0, $stats['saldo_aktif']);
        $this->assertEquals(100000.0, $stats['saldo_tertahan']);
    }

    public function test_tab_and_filter_switching(): void
    {
        $manager = $this->createMockWithdrawalManager();

        $manager->setTab('transactions');
        $this->assertEquals('transactions', $manager->activeTab);

        $manager->setTab('invalid_tab');
        $this->assertEquals('transactions', $manager->activeTab); // Abaikan nilai invalid

        $manager->setStatusFilter('completed');
        $this->assertEquals('completed', $manager->statusFilter);

        $manager->setStatusFilter('invalid_status');
        $this->assertEquals('completed', $manager->statusFilter); // Abaikan nilai invalid
    }

    public function test_open_withdraw_modal_prefills_bank_details_from_profile(): void
    {
        $manager = $this->createMockWithdrawalManager();

        $manager->openWithdrawModal();

        $this->assertTrue($manager->showWithdrawModal);
        $this->assertEquals('BCA', $manager->bank_kode);
        $this->assertEquals('1234567890', $manager->nomor_rekening);
        $this->assertEquals('Budi Mitra Futsal', $manager->nama_pemilik_rek);
        $this->assertEquals('', $manager->nominal_tarik);

        $manager->closeWithdrawModal();
        $this->assertFalse($manager->showWithdrawModal);
    }

    public function test_quick_amount_and_max_amount_setters(): void
    {
        $manager = $this->createMockWithdrawalManager();

        $manager->setQuickAmount(100000);
        $this->assertEquals('100000', $manager->nominal_tarik);

        // Nilai melebihi saldo aktif (250000) akan dibatasi ke nilai saldo aktif
        $manager->setQuickAmount(500000);
        $this->assertEquals('250000', $manager->nominal_tarik);

        $manager->setMaxAmount();
        $this->assertEquals('250000', $manager->nominal_tarik);
    }

    public function test_validation_blocks_withdrawal_exceeding_saldo_aktif(): void
    {
        $manager = $this->createMockWithdrawalManager();
        $manager->openWithdrawModal();

        // Nominal melebihi saldo aktif (250.000)
        $manager->nominal_tarik = '300000';

        try {
            $manager->proceedToConfirm();
            $this->fail('Seharusnya gagal validasi karena nominal melebihi saldo aktif.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('nominal_tarik', $e->errors());
        }
    }

    public function test_validation_blocks_withdrawal_below_minimum(): void
    {
        $manager = $this->createMockWithdrawalManager();
        $manager->openWithdrawModal();

        // Nominal kurang dari minimum Rp 10.000
        $manager->nominal_tarik = '5000';

        try {
            $manager->proceedToConfirm();
            $this->fail('Seharusnya gagal validasi karena nominal di bawah minimum.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('nominal_tarik', $e->errors());
        }
    }

    public function test_validation_blocks_empty_account_details(): void
    {
        $manager = $this->createMockWithdrawalManager();
        $manager->openWithdrawModal();

        $manager->nominal_tarik = '50000';
        $manager->nomor_rekening = '';
        $manager->nama_pemilik_rek = '';

        try {
            $manager->proceedToConfirm();
            $this->fail('Seharusnya gagal validasi karena rekening dan pemilik kosong.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('nomor_rekening', $e->errors());
            $this->assertArrayHasKey('nama_pemilik_rek', $e->errors());
        }
    }

    public function test_proceed_to_confirm_and_cancel_lifecycle(): void
    {
        $manager = $this->createMockWithdrawalManager();
        $manager->openWithdrawModal();

        $manager->nominal_tarik = '100000';
        $manager->bank_kode = 'MANDIRI';
        $manager->nomor_rekening = '9876543210';
        $manager->nama_pemilik_rek = 'Budi Santoso';

        $manager->proceedToConfirm();

        $this->assertFalse($manager->showWithdrawModal);
        $this->assertTrue($manager->showConfirmModal);

        $manager->cancelConfirm();

        $this->assertTrue($manager->showWithdrawModal);
        $this->assertFalse($manager->showConfirmModal);
    }

    public function test_open_withdraw_modal_rejected_when_balance_insufficient(): void
    {
        $wallet = new Wallet;
        $wallet->id = 6;
        $wallet->user_id = 10;
        $wallet->saldo_aktif = 5000.0; // Kurang dari Rp 10.000
        $wallet->saldo_tertahan = 0.0;

        $manager = $this->createMockWithdrawalManager($wallet);
        $manager->openWithdrawModal();

        $this->assertFalse($manager->showWithdrawModal);
        $this->assertCount(1, $manager->dispatchedEvents);
        $this->assertEquals('warning', $manager->dispatchedEvents[0]['params'][0]['type']);
    }
}
