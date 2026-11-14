@php
    use App\Services\Admin\UserAccountService as Accounts;
    use Carbon\CarbonImmutable;

    $dt = fn ($d, $fmt = 'j F Y, H.i') => $d ? CarbonImmutable::parse($d)->locale('id')->translatedFormat($fmt) : null;
    $rp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
    $initials = \Illuminate\Support\Str::of($user->name)->explode(' ')->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    $isSelf = (int) auth()->id() === (int) $user->id;
    $mitra = $overview['mitra'];
    $cust  = $overview['customer'];
    $verbs = ['aktifkan' => 'Diaktifkan kembali', 'suspend' => 'Ditangguhkan', 'banned' => 'Diblokir'];

    // Ringkasan risiko yang ditampilkan di dialog konfirmasi
    $stakes = null;
    if ($mitra && $mitra['venues']) {
        $stakes = "Mitra ini memiliki {$mitra['venues']->aktif} venue aktif dan " . ($mitra['fields']->aktif ?? 0) . ' lapangan aktif.';
    } elseif ($cust) {
        $stakes = "Penyewa ini memiliki {$cust['upcoming']} slot mendatang yang sudah dibayar.";
    }
@endphp

<x-layouts.admin :title="$user->name" hero-size="compact">
    <x-slot:hero>
        <nav aria-label="Breadcrumb" class="mb-3 text-caption text-primary-200">
            <a href="{{ route('admin.pengguna.index') }}" class="underline hover:text-white">Manajemen Pengguna</a>
            <span aria-hidden="true" class="mx-1">/</span>
            <span aria-current="page">{{ $user->name }}</span>
        </nav>

        <x-admin.page-header tone="dark" :title="$user->name"
                            :description="'Terdaftar ' . $dt($user->created_at, 'j F Y')">
            <x-slot:actions>
                <div class="flex flex-wrap items-center gap-2">
                    <x-ui.badge>{{ Accounts::ROLES[$user->role] ?? $user->role }}</x-ui.badge>
                    <x-admin.status-badge :status="$user->status_akun" />
                </div>
            </x-slot:actions>
        </x-admin.page-header>
    </x-slot:hero>

    {{-- Seluruh isi memakai satu keadaan Alpine untuk dialog status. --}}
    <div x-data="{ target: 'suspend', reason: '', submitting: false,
                get needsReason() { return this.target !== 'aktif' },
                get valid() { return ! this.needsReason || this.reason.trim().length >= 10 } }"
        @if ($errors->has('alasan') || $errors->has('status'))
            x-init="target = @js(old('status', 'suspend')); reason = @js(old('alasan', '')); $nextTick(() => $dispatch('open-modal', 'account-status'))"
        @endif>

        <x-ui.card>
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex min-w-0 items-center gap-4">
                    <div class="grid size-14 shrink-0 place-items-center rounded-full bg-primary-100 text-h5 text-primary-700" aria-hidden="true">{{ $initials }}</div>
                    <div class="min-w-0">
                        <p class="truncate text-h5 text-ink">{{ $user->name }}</p>
                        <p class="text-body-sm text-ink-muted"><span class="break-all">{{ $user->email }}</span>{{ $user->no_hp ? ', ' . $user->no_hp : '' }}</p>
                    </div>
                </div>

                @if ($canAct)
                    <div class="flex flex-col gap-2 sm:flex-row">
                        @if ($user->status_akun === 'aktif')
                            <x-ui.button variant="danger-outline" type="button" x-on:click="target = 'suspend'; reason = ''; $dispatch('open-modal', 'account-status')">Tangguhkan</x-ui.button>
                            <x-ui.button variant="danger" type="button" x-on:click="target = 'banned'; reason = ''; $dispatch('open-modal', 'account-status')">Blokir</x-ui.button>
                        @else
                            <x-ui.button type="button" x-on:click="target = 'aktif'; reason = ''; $dispatch('open-modal', 'account-status')">Aktifkan kembali</x-ui.button>
                            @if ($user->status_akun === 'suspend')
                                <x-ui.button variant="danger-outline" type="button" x-on:click="target = 'banned'; reason = ''; $dispatch('open-modal', 'account-status')">Blokir</x-ui.button>
                            @endif
                        @endif
                    </div>
                @else
                    <p class="max-w-sm text-body-sm text-ink-muted lg:text-right">
                        {{ $isSelf ? 'Kamu tidak bisa mengubah status akunmu sendiri.' : 'Akun admin tidak dapat diubah dari halaman ini.' }}
                    </p>
                @endif
            </div>
        </x-ui.card>

        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1fr)_22rem] lg:items-start">
            <div class="grid min-w-0 grid-cols-1 gap-4">
                <x-ui.card class="min-w-0">
                    <h2 class="text-h6 text-ink">Data akun</h2>
                    <dl class="mt-1">
                        <x-admin.info-row label="Peran" :value="Accounts::ROLES[$user->role] ?? $user->role" />
                        <x-admin.info-row label="Status akun">
                            <x-admin.status-badge :status="$user->status_akun" />
                        </x-admin.info-row>
                        <x-admin.info-row label="Email" :value="$user->email" />
                        <x-admin.info-row label="Email terverifikasi" :value="$user->email_verified_at ? $dt($user->email_verified_at) : 'Belum'" />
                        <x-admin.info-row label="No. HP" :value="$user->no_hp" />
                        <x-admin.info-row label="HP terverifikasi" :value="$user->phone_verified_at ? $dt($user->phone_verified_at) : 'Belum'" />
                        <x-admin.info-row label="Terdaftar" :value="$dt($user->created_at)" />
                        <x-admin.info-row label="Diperbarui" :value="$dt($user->updated_at)" />
                        @if ($overview['sessions'] !== null)
                            <x-admin.info-row label="Sesi aktif" :value="$overview['sessions'] . ' perangkat'" />
                        @endif
                    </dl>
                </x-ui.card>

                @if ($mitra)
                    <x-ui.card class="min-w-0">
                        <h2 class="text-h6 text-ink">Usaha mitra</h2>
                        @if ($mitra['profile'])
                            <dl class="mt-1">
                                <x-admin.info-row label="Nama usaha">
                                    <a href="{{ route('admin.verifikasi.show', $mitra['profile']->id) }}" class="text-brand-link underline">{{ $mitra['profile']->nama_usaha ?: 'Mitra #' . $mitra['profile']->id }}</a>
                                    <x-admin.status-badge :status="$mitra['profile']->status" class="ml-1" />
                                </x-admin.info-row>
                                <x-admin.info-row label="Venue" :value="$mitra['venues']->total . ' total, ' . $mitra['venues']->aktif . ' aktif'" />
                                <x-admin.info-row label="Lapangan" :value="$mitra['fields']->total . ' total, ' . $mitra['fields']->aktif . ' aktif'" />
                                @if ($mitra['wallet'])
                                    <x-admin.info-row label="Saldo aktif" :value="$rp($mitra['wallet']->saldo_aktif)" />
                                    <x-admin.info-row label="Saldo tertahan" :value="$rp($mitra['wallet']->saldo_tertahan)" />
                                @endif
                            </dl>
                        @else
                            <p class="mt-2 text-body-sm text-ink-muted">Akun ini berperan mitra, tetapi belum mengirim pendaftaran usaha.</p>
                        @endif
                    </x-ui.card>
                @endif

                @if ($cust)
                    <x-ui.card class="min-w-0">
                        <h2 class="text-h6 text-ink">Aktivitas penyewa</h2>
                        <dl class="mt-1">
                            <x-admin.info-row label="Booking" :value="$cust['bookings']->total . ' total, ' . $cust['bookings']->paid . ' dibayar, ' . $cust['bookings']->pending . ' menunggu'" />
                            <x-admin.info-row label="Total dibayar" :value="$rp($cust['spent'])" />
                            <x-admin.info-row label="Slot mendatang" :value="$cust['upcoming'] . ' (sudah dibayar)'" />
                            <x-admin.info-row label="Refund berjalan" :value="(string) $cust['refunds']" />
                        </dl>
                    </x-ui.card>
                @endif
            </div>

            <div class="grid min-w-0 grid-cols-1 content-start gap-4">
                <x-ui.card class="min-w-0">
                    <h2 class="text-h6 text-ink">Riwayat tindakan</h2>
                    @if ($history->isEmpty())
                        <p class="mt-2 text-body-sm text-ink-muted">Belum ada tindakan atas akun ini.</p>
                    @else
                        <ol class="mt-3">
                            @foreach ($history as $log)
                                <li class="border-l-2 border-line pb-4 pl-4 last:pb-0">
                                    <p class="text-body-sm text-ink">
                                        <span class="font-medium">{{ $verbs[$log->aksi] ?? \Illuminate\Support\Str::headline($log->aksi) }}</span>
                                        <span class="text-ink-muted">oleh {{ $log->user?->name ?? 'sistem' }}</span>
                                    </p>
                                    <p class="text-caption text-ink-muted">{{ $log->created_at->locale('id')->translatedFormat('j M Y, H.i') }}</p>
                                    @if ($log->catatan)
                                        <p class="mt-0.5 whitespace-pre-line wrap-break-word text-caption text-ink-body">{{ $log->catatan }}</p>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </x-ui.card>
            </div>
        </div>

        @if ($canAct)
            <x-ui.modal name="account-status" title="Ubah status akun">
                <form id="status-form" method="POST" action="{{ route('admin.pengguna.status', $user->id) }}"
                    x-on:submit="submitting = true" class="mt-3 space-y-3">
                    @csrf
                    <input type="hidden" name="status" :value="target">

                    <div class="rounded-md bg-surface-muted px-3 py-2 text-body-sm text-ink-body">
                        <p class="font-medium text-ink">{{ $user->name }}</p>
                        <p x-show="target === 'suspend'">Ditangguhkan sementara. Pengguna langsung keluar dari semua perangkat dan tidak bisa masuk sampai diaktifkan kembali.</p>
                        <p x-show="target === 'banned'" x-cloak>Diblokir untuk pelanggaran berat. Pengguna langsung keluar dan tidak bisa masuk. Data dan riwayat transaksi tetap disimpan.</p>
                        <p x-show="target === 'aktif'" x-cloak>Pengguna bisa masuk kembali.</p>
                        @if ($stakes)
                            <p x-show="target !== 'aktif'" x-cloak class="mt-1 text-caption text-ink-muted">{{ $stakes }} Booking yang sudah dibayar tidak dibatalkan otomatis.</p>
                        @endif
                    </div>

                    <div x-show="needsReason" x-cloak class="flex flex-wrap gap-1.5">
                        @foreach (['Laporan penipuan', 'Pelanggaran ketentuan layanan', 'Aktivitas mencurigakan'] as $text)
                            <button type="button" x-on:click="reason = @js($text)"
                                    class="rounded-full border border-line-strong px-3 py-1.5 text-caption text-ink-body hover:bg-surface-muted">{{ $text }}</button>
                        @endforeach
                    </div>

                    <x-ui.textarea name="alasan" label="Alasan" :maxlength="255" :rows="3"
                                hint="Wajib untuk penangguhan dan pemblokiran, minimal 10 karakter. Hanya tercatat di log audit, tidak ditampilkan ke pengguna."
                                x-model="reason" />
                </form>

                <x-slot:footer>
                    <x-ui.button variant="neutral" type="button" x-on:click="$dispatch('close-modal', 'account-status')">Batal</x-ui.button>
                    <span x-show="target !== 'aktif'" x-cloak>
                        <x-ui.button variant="danger" type="submit" form="status-form" ::disabled="!valid || submitting">
                            <span x-text="target === 'banned' ? 'Blokir akun' : 'Tangguhkan akun'"></span>
                        </x-ui.button>
                    </span>
                    <span x-show="target === 'aktif'" x-cloak>
                        <x-ui.button type="submit" form="status-form" ::disabled="submitting">Aktifkan kembali</x-ui.button>
                    </span>
                </x-slot:footer>
            </x-ui.modal>
        @endif
    </div>
</x-layouts.admin>