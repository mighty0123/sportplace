<?php

namespace App\Livewire\Mitra\Withdrawals;

use App\Livewire\Mitra\MitraComponent;
use App\Models\AuditLog;
use App\Models\BookingDetail;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Withdrawal;
use App\Services\Mitra\CommissionResolver;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class WithdrawalManager extends MitraComponent
{
    use WithPagination;

    // Tab Aktif: 'withdrawals' (Riwayat Penarikan) atau 'transactions' (Mutasi Dompet)
    #[Url(as: 'tab', history: true)]
    public string $activeTab = 'withdrawals';

    // Filter Status Penarikan
    #[Url(as: 'status', history: true)]
    public string $statusFilter = 'semua';

    public int $perPage = 10;

    // Modal Form State
    public bool $showWithdrawModal = false;

    public bool $showConfirmModal = false;

    // Field Form Penarikan
    public string $nominal_tarik = '';

    public string $bank_kode = 'BCA';

    public string $nomor_rekening = '';

    public string $nama_pemilik_rek = '';

    public function mount(CommissionResolver $commissionResolver): void
    {
        $this->authorizeMitraApproved();
        $this->syncUncreditedCompletedBookings($commissionResolver);
    }

    /**
     * Sinkronisasi pendapatan pesanan yang telah selesai (Completed) ke dalam Wallet mitra.
     * Mengkreditkan pendapatan bersih (setelah komisi platform) dan menghitung saldo tertahan dari sesi yang masih berlangsung.
     */
    public function syncUncreditedCompletedBookings(CommissionResolver $commissionResolver): void
    {
        $user = $this->mitraUser();
        $profile = $this->mitraProfile();

        if (! $profile) {
            return;
        }

        $wallet = $this->wallet();

        // 1. Ambil seluruh booking selesai yang pembayarannya lunas
        $completedDetails = BookingDetail::forMitra($profile->id)
            ->where('status_item', 'completed')
            ->whereHas('header', function ($q) {
                $q->where('status', 'payment')
                    ->orWhereHas('payment', fn ($p) => $p->where('status_pembayaran', 'succes'));
            })
            ->with(['field'])
            ->get();

        if ($completedDetails->isNotEmpty()) {
            // Ambil ID detail yang sudah tercatat di transaksi wallet
            $existingCreditedIds = WalletTransaction::where('wallet_id', $wallet->id)
                ->where('referensi_tipe', 'booking_detail')
                ->pluck('referensi_id')
                ->all();

            $existingCreditedLookup = array_flip($existingCreditedIds);

            DB::transaction(function () use ($completedDetails, $existingCreditedLookup, $wallet, $commissionResolver) {
                /** @var Wallet $lockedWallet */
                $lockedWallet = Wallet::lockForUpdate()->find($wallet->id);

                if (! $lockedWallet) {
                    return;
                }

                $totalNewCredit = 0.0;

                foreach ($completedDetails as $detail) {
                    if (isset($existingCreditedLookup[$detail->id])) {
                        continue;
                    }

                    $gross = (float) $detail->harga_slot;
                    $calc = $commissionResolver->calculate($gross);
                    $net = (float) $calc['net'];

                    WalletTransaction::create([
                        'wallet_id' => $lockedWallet->id,
                        'jenis_mutasi' => 'kredit',
                        'kategori_transaksi' => 'booking_revenue',
                        'nominal' => $net,
                        'referensi_tipe' => 'booking_detail',
                        'referensi_id' => $detail->id,
                        'deskripsi' => 'Pendapatan sewa #'.$detail->id.' ('.$detail->field?->nama_lapangan.')',
                        'created_at' => $detail->tanggal_sewa ? Carbon::parse($detail->tanggal_sewa)->toDateTimeString() : now(),
                    ]);

                    $totalNewCredit += $net;
                }

                if ($totalNewCredit > 0) {
                    $lockedWallet->saldo_aktif += $totalNewCredit;
                    $lockedWallet->updated_at = now();
                    $lockedWallet->save();
                }
            });
        }

        // 2. Perbarui estimasi saldo tertahan (ongoing bookings yang lunas)
        $ongoingDetails = BookingDetail::forMitra($profile->id)
            ->where('status_item', 'ongoing')
            ->whereHas('header', function ($q) {
                $q->where('status', 'payment')
                    ->orWhereHas('payment', fn ($p) => $p->where('status_pembayaran', 'succes'));
            })
            ->get();

        $ongoingGross = (float) $ongoingDetails->sum('harga_slot');
        $calcOngoing = $commissionResolver->calculate($ongoingGross);
        $pendingNet = (float) $calcOngoing['net'];

        if ((float) $wallet->saldo_tertahan !== $pendingNet) {
            $wallet->saldo_tertahan = $pendingNet;
            $wallet->updated_at = now();
            $wallet->save();
        }
    }

    #[Computed]
    public function wallet(): Wallet
    {
        $user = $this->mitraUser();

        return Wallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'saldo_aktif' => 0.0,
                'saldo_tertahan' => 0.0,
                'updated_at' => now(),
            ]
        );
    }

    #[Computed]
    public function stats(): array
    {
        $user = $this->mitraUser();
        $wallet = $this->wallet();

        $totalWithdrawn = $this->calculateTotalWithdrawn($user->id);
        $pendingWithdrawn = $this->calculatePendingWithdrawn($user->id);

        return [
            'saldo_aktif' => (float) $wallet->saldo_aktif,
            'saldo_tertahan' => (float) $wallet->saldo_tertahan,
            'total_withdrawn' => $totalWithdrawn,
            'pending_withdrawn' => $pendingWithdrawn,
        ];
    }

    public function calculateTotalWithdrawn(int $userId): float
    {
        return (float) Withdrawal::where('user_id', $userId)
            ->where('status', 'completed')
            ->sum('nominal_tarik');
    }

    public function calculatePendingWithdrawn(int $userId): float
    {
        return (float) Withdrawal::where('user_id', $userId)
            ->whereIn('status', ['pending', 'processing'])
            ->sum('nominal_tarik');
    }

    public function availableBanks(): array
    {
        return [
            'BCA' => 'Bank Central Asia (BCA)',
            'MANDIRI' => 'Bank Mandiri',
            'BNI' => 'Bank Negara Indonesia (BNI)',
            'BRI' => 'Bank Rakyat Indonesia (BRI)',
            'BSI' => 'Bank Syariah Indonesia (BSI)',
            'CIMB' => 'CIMB Niaga',
            'PERMATA' => 'Bank Permata',
            'DANAMON' => 'Bank Danamon',
            'JAGO' => 'Bank Jago',
            'SEABANK' => 'SeaBank',
            'LAINNYA' => 'Bank Lainnya',
        ];
    }

    public function openWithdrawModal(): void
    {
        $profile = $this->mitraProfile();
        $wallet = $this->wallet();

        if ((float) $wallet->saldo_aktif < 10000) {
            $this->notify('Saldo aktif Anda belum mencapai batas minimum penarikan (Rp 10.000).', 'warning');

            return;
        }

        // Isi otomatis data rekening dari profil mitra yang terdaftar
        $this->bank_kode = $profile?->bank_nama ? strtoupper(trim($profile->bank_nama)) : 'BCA';
        $this->nomor_rekening = (string) ($profile?->nomor_rekening ?? '');
        $this->nama_pemilik_rek = (string) ($profile?->nama_rekening ?? $this->mitraUser()->name);
        $this->nominal_tarik = '';

        $this->resetValidation();
        $this->showWithdrawModal = true;
    }

    public function closeWithdrawModal(): void
    {
        $this->showWithdrawModal = false;
        $this->resetValidation();
    }

    public function setQuickAmount(float $amount): void
    {
        $wallet = $this->wallet();
        $max = (float) $wallet->saldo_aktif;

        $this->nominal_tarik = (string) min($amount, $max);
        $this->resetValidation('nominal_tarik');
    }

    public function setMaxAmount(): void
    {
        $wallet = $this->wallet();
        $this->nominal_tarik = (string) floor((float) $wallet->saldo_aktif);
        $this->resetValidation('nominal_tarik');
    }

    public function proceedToConfirm(): void
    {
        $wallet = $this->wallet();
        $saldoAktif = (float) $wallet->saldo_aktif;

        $cleanNominal = (float) preg_replace('/[^\d.]/', '', $this->nominal_tarik);
        $this->nominal_tarik = (string) $cleanNominal;

        $this->validate([
            'nominal_tarik' => [
                'required',
                'numeric',
                'min:10000',
                'max:'.max(10000.0, $saldoAktif),
            ],
            'bank_kode' => ['required', 'string', 'max:50'],
            'nomor_rekening' => ['required', 'string', 'min:5', 'max:50'],
            'nama_pemilik_rek' => ['required', 'string', 'min:3', 'max:255'],
        ], [
            'nominal_tarik.required' => 'Nominal penarikan wajib diisi.',
            'nominal_tarik.numeric' => 'Nominal penarikan harus berupa angka.',
            'nominal_tarik.min' => 'Batas minimum penarikan dana adalah Rp 10.000.',
            'nominal_tarik.max' => 'Nominal penarikan melebihi saldo aktif Anda (Rp '.number_format($saldoAktif, 0, ',', '.').').',
            'bank_kode.required' => 'Bank tujuan penarikan wajib dipilih.',
            'nomor_rekening.required' => 'Nomor rekening wajib diisi.',
            'nomor_rekening.min' => 'Nomor rekening minimal 5 digit.',
            'nama_pemilik_rek.required' => 'Nama pemilik rekening wajib diisi.',
        ]);

        $this->showWithdrawModal = false;
        $this->showConfirmModal = true;
    }

    public function cancelConfirm(): void
    {
        $this->showConfirmModal = false;
        $this->showWithdrawModal = true;
    }

    public function executeWithdrawal(): void
    {
        $user = $this->mitraUser();
        $nominal = (float) $this->nominal_tarik;

        if ($nominal < 10000) {
            $this->notify('Nominal penarikan minimal Rp 10.000.', 'error');
            $this->showConfirmModal = false;

            return;
        }

        $bankKode = strtoupper(trim($this->bank_kode));
        $nomorRek = trim($this->nomor_rekening);
        $namaPemilik = trim($this->nama_pemilik_rek);

        $result = DB::transaction(function () use ($user, $nominal, $bankKode, $nomorRek, $namaPemilik) {
            /** @var Wallet $lockedWallet */
            $lockedWallet = Wallet::lockForUpdate()->where('user_id', $user->id)->first();

            if (! $lockedWallet || (float) $lockedWallet->saldo_aktif < $nominal) {
                return [
                    'status' => 'error',
                    'message' => 'Saldo aktif Anda tidak mencukupi untuk melakukan penarikan ini.',
                ];
            }

            // Potong saldo aktif di dompet mitra
            $lockedWallet->saldo_aktif -= $nominal;
            $lockedWallet->updated_at = now();
            $lockedWallet->save();

            // Buat kode referensi penarikan unik
            $disbursementId = 'WD-'.date('YmdHis').'-'.strtoupper(Str::random(6));

            /** @var Withdrawal $withdrawal */
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'bank_kode' => $bankKode,
                'nomor_rekening' => $nomorRek,
                'nama_pemilik_rek' => $namaPemilik,
                'nominal_tarik' => $nominal,
                'biaya_admin_bank' => 0.0,
                'status' => 'pending',
                'pg_disbursement_id' => $disbursementId,
                'alasan_gagal' => null,
                'created_at' => now(),
                'complated_at' => null,
            ]);

            // Catat mutasi debit di buku besar dompet
            WalletTransaction::create([
                'wallet_id' => $lockedWallet->id,
                'jenis_mutasi' => 'debit',
                'kategori_transaksi' => 'withdrawal',
                'nominal' => $nominal,
                'referensi_tipe' => 'withdrawal',
                'referensi_id' => $withdrawal->id,
                'deskripsi' => 'Penarikan dana ke rekening '.$bankKode.' '.$nomorRek.' a.n '.$namaPemilik,
                'created_at' => now(),
            ]);

            // Catat log audit
            AuditLog::record(
                $user,
                'Withdrawal',
                $withdrawal->id,
                'request_withdrawal',
                null,
                'pending',
                'Mitra mengajukan penarikan dana sebesar Rp '.number_format($nominal, 0, ',', '.').' (Ref: '.$disbursementId.')'
            );

            return [
                'status' => 'success',
                'message' => 'Pengajuan penarikan dana sebesar Rp '.number_format($nominal, 0, ',', '.').' berhasil diajukan dan sedang diproses.',
            ];
        });

        $this->showConfirmModal = false;

        if ($result['status'] === 'success') {
            $this->notify($result['message'], 'success');
            $this->nominal_tarik = '';
        } else {
            $this->notify($result['message'], 'error');
        }
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['withdrawals', 'transactions'], true)) {
            $this->activeTab = $tab;
            $this->resetPage();
        }
    }

    public function setStatusFilter(string $status): void
    {
        if (in_array($status, ['semua', 'pending', 'processing', 'completed', 'failed'], true)) {
            $this->statusFilter = $status;
            $this->resetPage();
        }
    }

    public function notify(string $message, string $type = 'success'): void
    {
        $this->dispatch('notify', ['type' => $type, 'message' => $message]);
    }

    public function render(): View
    {
        $user = $this->mitraUser();
        $wallet = $this->wallet();

        // 1. Query Riwayat Penarikan Dana
        $withdrawalsQuery = Withdrawal::where('user_id', $user->id)->orderByDesc('id');

        if ($this->statusFilter !== 'semua') {
            $withdrawalsQuery->where('status', $this->statusFilter);
        }

        $withdrawalsList = $withdrawalsQuery->paginate($this->perPage, ['*'], 'withdrawalsPage');

        // 2. Query Mutasi Saldo Dompet
        $transactionsList = WalletTransaction::where('wallet_id', $wallet->id)
            ->orderByDesc('id')
            ->paginate($this->perPage, ['*'], 'transactionsPage');

        return view('livewire.mitra.withdrawals.withdrawal-manager', [
            'wallet' => $wallet,
            'stats' => $this->stats,
            'withdrawalsList' => $withdrawalsList,
            'transactionsList' => $transactionsList,
            'availableBanks' => $this->availableBanks(),
        ]);
    }
}
