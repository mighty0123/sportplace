@php
    use Carbon\CarbonImmutable;

    $dt = fn ($d, $fmt = 'j F Y, H.i') => $d ? CarbonImmutable::parse($d)->locale('id')->translatedFormat($fmt) : null;
    $initials = \Illuminate\Support\Str::of($user->name)->explode(' ')->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    $others = $sessions->where('current', false)->count();

    $verbs = ['ubah_profil' => 'Mengubah profil', 'ganti_password' => 'Mengganti kata sandi', 'keluar_perangkat_lain' => 'Mengeluarkan perangkat lain',
        'setujui' => 'Menyetujui', 'tolak' => 'Menolak', 'suspend' => 'Menangguhkan', 'banned' => 'Memblokir', 'aktifkan' => 'Mengaktifkan',
        'nonaktifkan' => 'Menonaktifkan', 'tambah' => 'Menambah', 'ubah' => 'Mengubah', 'ekspor' => 'Mengekspor', 'lihat_dokumen' => 'Membuka dokumen',
        'lihat_data' => 'Menampilkan data sensitif', 'kedaluwarsa' => 'Mengedaluwarsakan'];
    $targets = ['MitraProfile' => 'mitra', 'Venue' => 'venue', 'Field' => 'lapangan', 'User' => 'akun', 'MasterSport' => 'cabang olahraga',
        'MasterCity' => 'kota', 'MasterFacility' => 'fasilitas', 'Laporan' => 'laporan', 'BookingHeader' => 'booking'];
@endphp

<x-layouts.admin title="Profil saya" hero-size="compact">
    <x-slot:hero>
        <x-admin.page-header tone="dark" title="Profil saya" description="Kelola data akun dan keamanan akunmu." />
    </x-slot:hero>

    <x-ui.card>
        <div class="flex items-center gap-4">
            <div class="grid size-14 shrink-0 place-items-center rounded-full bg-primary-100 text-h5 text-primary-700" aria-hidden="true">
                <x-ui.avatar :name="$user->name" :src="$user->avatar_url" size="size-14" class="text-h5" />
            </div>
            <div class="min-w-0">
                <p class="truncate text-h5 text-ink">{{ $user->name }}</p>
                <p class="break-all text-body-sm text-ink-muted">{{ $user->email }}</p>
                <div class="mt-1.5 flex flex-wrap items-center gap-2">
                    <x-ui.badge>Admin Platform</x-ui.badge>
                    <x-admin.status-badge :status="$user->status_akun" />
                    <span class="text-caption text-ink-muted">Terdaftar {{ $dt($user->created_at, 'j F Y') }}</span>
                </div>
            </div>
        </div>
    </x-ui.card>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1fr)_24rem] lg:items-start">
        <div class="grid min-w-0 grid-cols-1 gap-4">

            {{-- Data akun --}}
            <x-ui.card class="min-w-0">
                <h2 class="text-h6 text-ink">Data akun</h2>
                <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="mt-3 space-y-4">
                    @csrf
                    @method('PUT')
                
                    <div class="flex items-center gap-4">
                        <x-ui.avatar :name="$user->name" :src="$user->avatar_url" size="size-16" class="text-h5" />
                        <div class="min-w-0">
                            <label for="foto_profil" class="text-label-md text-ink-strong">Foto profil</label>
                            <input id="foto_profil" type="file" name="foto_profil" accept="image/jpeg,image/png,image/webp"
                                    class="mt-1 block w-full text-body-sm text-ink">
                            <p class="mt-0.5 text-caption text-ink-muted">JPG, PNG, atau WebP, maksimal 2 MB.</p>
                            @error('foto_profil') <p class="mt-1 text-caption text-danger-700">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <x-ui.input name="name" label="Nama lengkap" :required="true" maxlength="100"
                                value="{{ old('name', $user->name) }}" autocomplete="name" />
                    <x-ui.input name="no_hp" type="tel" label="Nomor HP" :required="true" inputmode="tel"
                                value="{{ old('no_hp', $user->no_hp) }}" autocomplete="tel"
                                hint="Contoh: 081234567890. Mengubah nomor mengulang verifikasi." />

                    <div>
                        <p class="text-label-md text-ink-strong">Email</p>
                        <p class="mt-1 break-all text-body-md text-ink">{{ $user->email }}</p>
                        <p class="mt-0.5 text-caption text-ink-muted">Email adalah identitas login dan tidak bisa diubah dari sini. Hubungi pengelola server bila perlu diganti.</p>
                    </div>

                    <x-ui.button type="submit">Simpan perubahan</x-ui.button>
                </form>
            </x-ui.card>

            {{-- Kata sandi --}}
            <x-ui.card class="min-w-0">
                <h2 class="text-h6 text-ink">Ganti kata sandi</h2>
                <p class="mt-0.5 text-caption text-ink-muted">Setelah diganti, semua perangkat lain otomatis dikeluarkan.</p>
                <form method="POST" action="{{ route('admin.profile.password') }}" class="mt-3 space-y-4">
                    @csrf
                    @method('PUT')

                    <x-ui.input name="current_password" type="password" label="Kata sandi saat ini" :required="true" autocomplete="current-password" />
                    <x-ui.input name="password" type="password" label="Kata sandi baru" :required="true" autocomplete="new-password"
                                hint="Minimal 10 karakter, memuat huruf besar, huruf kecil, dan angka." />
                    <x-ui.input name="password_confirmation" type="password" label="Ulangi kata sandi baru" :required="true" autocomplete="new-password" />

                    <x-ui.button type="submit">Ganti kata sandi</x-ui.button>
                </form>
            </x-ui.card>
        </div>

        <div class="grid min-w-0 grid-cols-1 content-start gap-4">

            {{-- Sesi aktif --}}
            <x-ui.card class="min-w-0">
                <h2 class="text-h6 text-ink">Sesi aktif</h2>
                @if (! $sessionsSupported)
                    <p class="mt-2 text-body-sm text-ink-muted">Daftar perangkat hanya tersedia bila sesi disimpan di database (<code>SESSION_DRIVER=database</code>).</p>
                @elseif ($sessions->isEmpty())
                    <p class="mt-2 text-body-sm text-ink-muted">Tidak ada sesi tercatat.</p>
                @else
                    <ul class="mt-1 divide-y divide-line">
                        @foreach ($sessions as $s)
                            <li class="py-3">
                                <div class="flex items-start justify-between gap-3">
                                    <p class="text-body-sm text-ink">{{ $s->device }}</p>
                                    @if ($s->current)<x-ui.badge tone="success">Perangkat ini</x-ui.badge>@endif
                                </div>
                                <p class="text-caption text-ink-muted">{{ $s->ip }}, aktif {{ $s->last->locale('id')->diffForHumans() }}</p>
                            </li>
                        @endforeach
                    </ul>

                    @if ($others > 0)
                        <form method="POST" action="{{ route('admin.profile.sessions.logout') }}" class="mt-3 space-y-3 border-t border-line pt-3">
                            @csrf
                            <x-ui.input name="logout_password" type="password" label="Kata sandi" :required="true" autocomplete="current-password"
                                        hint="Diminta untuk memastikan ini benar-benar kamu." />
                            <x-ui.button type="submit" variant="danger-outline">Keluar dari {{ $others }} perangkat lain</x-ui.button>
                        </form>
                    @endif
                @endif
            </x-ui.card>

            {{-- Aktivitas --}}
            <x-ui.card class="min-w-0">
                <h2 class="text-h6 text-ink">Aktivitas terakhirmu</h2>
                @if ($activity->isEmpty())
                    <p class="mt-2 text-body-sm text-ink-muted">Belum ada tindakan tercatat.</p>
                @else
                    <ol class="mt-3">
                        @foreach ($activity as $log)
                            <li class="border-l-2 border-line pb-3 pl-4 last:pb-0">
                                <p class="text-body-sm text-ink">
                                    {{ $verbs[$log->aksi] ?? \Illuminate\Support\Str::headline($log->aksi) }}
                                    {{ $targets[class_basename($log->entitas_type)] ?? '' }}{{ $log->entitas_id ? ' #' . $log->entitas_id : '' }}
                                </p>
                                <p class="text-caption text-ink-muted">{{ $dt($log->created_at, 'j M Y, H.i') }}</p>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </x-ui.card>
        </div>
    </div>
</x-layouts.admin>