<div class="max-w-3xl w-full mx-auto px-4 py-8 sm:py-10">

    <!-- Header Tag -->
    <div class="mb-4 flex items-center justify-between flex-wrap gap-2">
        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-[#0B7B69] border border-emerald-200">
            <span class="w-1.5 h-1.5 rounded-full bg-[#0B7B69]"></span>
            STATUS TRACKER • ONBOARDING MITRA
        </span>

        @if ($currentProfile && $currentProfile->status === 'pending')
            <span class="text-[11px] text-slate-400 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                Memantau status secara otomatis
            </span>
        @endif
    </div>

    @if ($currentProfile && $currentProfile->isRejected())
        <!-- VARIAN B: DITOLAK / REVISI DIPERLUKAN -->
        <div class="bg-white rounded-3xl border-2 border-rose-300 p-6 sm:p-8 shadow-xs mb-8">
            <div class="flex items-center justify-between pb-4 border-b border-rose-100 mb-6">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                    <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                    Pengajuan Ditolak
                </span>
                <span class="text-xs font-semibold text-rose-600">Revisi Diperlukan</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between mb-2 gap-1">
                <h2 class="text-xl font-bold text-slate-900">Alasan Penolakan dari Admin</h2>
                @if ($currentProfile->reviewed_at)
                    <span class="text-xs text-slate-400 font-medium">Ditinjau pada {{ $currentProfile->reviewed_at->format('d M Y • H:i') }} WIB</span>
                @endif
            </div>
            <div class="p-4 rounded-2xl bg-rose-50/70 border border-rose-200 text-sm text-rose-900 leading-relaxed font-medium mb-4">
                &ldquo;{{ $currentProfile->alasan_penolakan ?? 'Dokumen legalitas yang diunggah belum memenuhi persyaratan verifikasi.' }}&rdquo;
            </div>

            @if (!empty($currentProfile->review_checklist['manual']))
                <div class="mb-5 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-2.5">Catatan Pemeriksaan Checklist:</h4>
                    <ul class="space-y-2 text-xs">
                        @foreach(\App\Services\Admin\MitraVerificationService::CHECKLIST as $key => $label)
                            @if(isset($currentProfile->review_checklist['manual'][$key]))
                                @php $isPassed = (bool) $currentProfile->review_checklist['manual'][$key]; @endphp
                                <li class="flex items-center gap-2">
                                    @if($isPassed)
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span class="text-slate-600">{{ $label }}</span>
                                    @else
                                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        <span class="font-semibold text-rose-700">{{ $label }} — Belum Memenuhi</span>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif

            <p class="text-xs text-slate-500 mb-6 leading-relaxed">
                Mohon periksa kembali kelengkapan dan kejelasan foto berkas KTP, NPWP, atau izin usaha Anda. Setelah diperbaiki, Anda dapat langsung mengajukan peninjauan ulang.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-100">
                <a
                    href="https://wa.me/6281198765432"
                    target="_blank"
                    class="text-xs font-semibold text-slate-600 hover:text-[#0B7B69] flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span>Konsultasi via WhatsApp</span>
                </a>

                <button
                    type="button"
                    wire:click="openResubmitModal"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-full bg-rose-600 hover:bg-rose-700 active:scale-95 px-7 py-3 text-sm font-semibold text-white shadow-xs transition duration-150 ease-in-out min-h-[44px] cursor-pointer"
                >
                    <span>Perbaiki & Ajukan Ulang</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>
            </div>
        </div>

    @elseif ($currentProfile && $currentProfile->isApproved())
        <!-- VARIAN C: DISETUJUI / SUDAH AKTIF -->
        <div class="bg-white rounded-3xl border-2 border-emerald-300 p-6 sm:p-8 shadow-xs mb-8">
            <div class="flex items-center justify-between pb-4 border-b border-emerald-100 mb-6">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-[#0B7B69] border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-[#0B7B69]"></span>
                    Lolos Verifikasi
                </span>
                <span class="text-xs font-semibold text-emerald-700">Akun Mitra Aktif</span>
            </div>

            <h2 class="text-2xl font-bold text-slate-900 mb-2">Selamat, akunmu sudah aktif!</h2>
            <p class="text-sm text-slate-600 mb-8 leading-relaxed">
                Data usaha dan berkas legalitas Anda telah berhasil diverifikasi oleh tim verifikator SportPlace. Sekarang Anda dapat mulai mendaftarkan venue dan unit lapangan olahraga.
            </p>

            <!-- 3 Panduan Aktivasi Venue -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 mb-8">
                <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50 text-center">
                    <span class="text-xs font-bold text-[#0B7B69] block mb-1">Tahap 1</span>
                    <span class="text-sm font-semibold text-slate-800">Profil Venue</span>
                </div>
                <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50 text-center">
                    <span class="text-xs font-bold text-[#0B7B69] block mb-1">Tahap 2</span>
                    <span class="text-sm font-semibold text-slate-800">Unit Lapangan</span>
                </div>
                <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50 text-center">
                    <span class="text-xs font-bold text-[#0B7B69] block mb-1">Tahap 3</span>
                    <span class="text-sm font-semibold text-slate-800">Jadwal & Tarif</span>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <a
                    href="{{ route('mitra.dashboard') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-full bg-[#0B7B69] hover:bg-[#086354] px-7 py-3 text-sm font-semibold text-white shadow-xs transition duration-150 ease-in-out min-h-[44px]"
                >
                    <span>Masuk ke Dashboard Mitra</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
        </div>

    @elseif ($currentProfile)
        <!-- VARIAN A: MENUNGGU VERIFIKASI (Dengan Auto-Polling Livewire 30s) -->
        <div wire:poll.30s="refreshStatus(false)" class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs mb-8">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                    <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Menunggu Verifikasi</span>
                </span>
                <span class="text-xs text-slate-400">ID: REG-{{ date('Ymd', strtotime($currentProfile->created_at ?? now())) }}-{{ str_pad($currentProfile->id, 3, '0', STR_PAD_LEFT) }}</span>
            </div>

            <h2 class="text-2xl font-bold text-slate-900 mb-2">Pengajuanmu sedang ditinjau</h2>
            <p class="text-sm text-slate-500 mb-8 leading-relaxed">
                Terima kasih telah mendaftar di SportPlace. Tim verifikator kami sedang memeriksa keabsahan dokumen legalitas dan data usahamu. Proses ini membutuhkan 1–2 hari kerja.
            </p>

            <!-- Vertical Timeline -->
            <div class="relative space-y-0">
                <div class="absolute left-3.5 top-3.5 bottom-3.5 w-0.5 bg-slate-200 -translate-x-1/2" aria-hidden="true"></div>
                <div class="absolute left-3.5 top-3.5 h-20 w-0.5 bg-emerald-500 -translate-x-1/2" aria-hidden="true"></div>

                <!-- Step 1: Pengajuan Dikirim -->
                <div class="relative flex items-start gap-4 pb-7">
                    <span class="relative z-10 w-7 h-7 rounded-full bg-[#0B7B69] text-white flex items-center justify-center shrink-0 ring-4 ring-white shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <div class="flex-1 pt-0.5">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-0.5">
                            <span class="text-sm font-bold text-slate-900">Pengajuan Dikirim</span>
                            <span class="text-xs text-slate-400 font-medium">{{ ($currentProfile->submitted_at ?? $currentProfile->created_at)?->format('d M Y • H:i') ?? now()->format('d M Y • H:i') }} WIB</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Formulir pendaftaran dan berkas legalitas awal berhasil diunggah.</p>
                    </div>
                </div>

                <!-- Step 2: Dokumen Diperiksa Admin -->
                <div class="relative flex items-start gap-4 pb-7">
                    <span class="relative z-10 w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0 ring-4 ring-white shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </span>
                    <div class="flex-1 pt-0.5">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-bold text-slate-900">Dokumen Diperiksa Admin</span>
                                <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-50 text-[#0B7B69] border border-emerald-200">Sedang Berjalan</span>
                            </div>
                            <span class="text-xs text-amber-600 font-semibold">Estimasi: Hari ini</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Pemeriksaan kecocokan KTP pemilik, NPWP, dan data rekening pencairan.</p>
                    </div>
                </div>

                <!-- Step 3: Keputusan Verifikasi -->
                <div class="relative flex items-start gap-4 pb-7">
                    <span class="relative z-10 w-7 h-7 rounded-full bg-slate-100 text-slate-400 border border-slate-300 flex items-center justify-center shrink-0 ring-4 ring-white">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </span>
                    <div class="flex-1 pt-0.5">
                        <span class="text-sm font-bold text-slate-400">Keputusan Verifikasi</span>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">Notifikasi keputusan akan dikirim melalui WhatsApp dan Email resmi SportPlace.</p>
                    </div>
                </div>

                <!-- Step 4: Akun Aktif & Setup Venue -->
                <div class="relative flex items-start gap-4">
                    <span class="relative z-10 w-7 h-7 rounded-full bg-slate-100 text-slate-400 border border-slate-300 flex items-center justify-center shrink-0 ring-4 ring-white">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </span>
                    <div class="flex-1 pt-0.5">
                        <span class="text-sm font-bold text-slate-400">Akun Aktif & Setup Venue</span>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">Akses penuh ke dashboard pengelolaan lapangan, tarif sewa, dan kalender booking.</p>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Pengajuan Mitra -->
            <div class="mt-8 p-6 rounded-2xl bg-slate-50/70 border border-slate-200">
                <span class="text-xs font-bold text-slate-700 block mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>Ringkasan Pengajuan Mitra</span>
                </span>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs mb-4">
                    <div>
                        <span class="text-slate-400 block mb-0.5">Nama Usaha</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $currentProfile->nama_usaha ?? 'Belum diisi' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Lokasi & Kota</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $currentProfile->kota_operasional ?? 'Belum diisi' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Tanggal Registrasi</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $currentProfile->created_at ? $currentProfile->created_at->format('d F Y') : now()->format('d F Y') }}</span>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-slate-200 text-[11px]">
                    <span class="text-slate-500 font-medium">Dokumen:</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-[#0B7B69] font-medium border border-emerald-200">
                        <svg class="w-3 h-3 text-[#0B7B69]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                        KTP Pemilik (OK)
                    </span>
                    @if ($currentProfile->file_npwp)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-[#0B7B69] font-medium border border-emerald-200">
                            <svg class="w-3 h-3 text-[#0B7B69]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                            NPWP Badan (OK)
                        </span>
                    @endif
                    @if ($currentProfile->file_nib)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-[#0B7B69] font-medium border border-emerald-200">
                            <svg class="w-3 h-3 text-[#0B7B69]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                            NIB OSS (OK)
                        </span>
                    @endif
                </div>
            </div>

            <!-- Action Buttons (Refresh Reaktif Tanpa Reload Halaman) -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-6 mt-6 border-t border-slate-100">
                <a
                    href="https://wa.me/6281198765432"
                    target="_blank"
                    class="text-xs font-semibold text-slate-600 hover:text-[#0B7B69] flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span>Hubungi Dukungan Onboarding</span>
                </a>

                <button
                    type="button"
                    wire:click="refreshStatus"
                    wire:loading.attr="disabled"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-full bg-white border border-slate-300 hover:bg-slate-50 active:scale-95 px-5 py-2.5 text-xs font-semibold text-slate-700 transition min-h-[44px] cursor-pointer disabled:opacity-60"
                >
                    <svg wire:loading.class="animate-spin" wire:target="refreshStatus" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span wire:loading.remove wire:target="refreshStatus">Segarkan Status</span>
                    <span wire:loading wire:target="refreshStatus">Memeriksa...</span>
                </button>
            </div>
        </div>
    @endif

    <!-- MODAL INTERAKTIF: EDIT & AJUKAN ULANG (MTR-12) -->
    @if ($showResubmitModal)
        <div
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-resubmit-title"
            role="dialog"
            aria-modal="true"
        >
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" wire:click="closeResubmitModal"></div>

            <!-- Modal Container -->
            <div class="flex min-h-full items-center justify-center p-4 sm:p-6 text-center">
                <div
                    class="relative w-full max-w-3xl transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all border border-slate-200"
                    @click.stop
                >
                    <!-- Modal Header -->
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900" id="modal-resubmit-title">
                                Perbaiki Data & Ajukan Ulang Verifikasi
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Lengkapi perbaikan yang diminta admin tanpa harus mendaftar ulang dari awal.
                            </p>
                        </div>
                        <button
                            type="button"
                            wire:click="closeResubmitModal"
                            class="p-2 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-200/70 transition cursor-pointer"
                            aria-label="Tutup Modal"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Form Body -->
                    <form wire:submit="resubmit" class="p-6 max-h-[80vh] overflow-y-auto space-y-6">
                        @if ($currentProfile?->alasan_penolakan)
                            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-900">
                                <span class="font-bold block mb-1 text-rose-700">Catatan Penolakan Admin:</span>
                                &ldquo;{{ $currentProfile->alasan_penolakan }}&rdquo;
                            </div>
                        @endif

                        <!-- Grid Data Identitas & Usaha -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="modal_no_ktp" value="Nomor KTP (NIK)" required />
                                <input
                                    type="text"
                                    id="modal_no_ktp"
                                    wire:model.live.debounce.300ms="no_ktp"
                                    maxlength="20"
                                    class="w-full rounded-full border {{ $errors->has('no_ktp') ? 'border-rose-400' : 'border-slate-300' }} px-4 py-2 text-sm tabular-nums focus:outline-hidden focus:border-[#0B7B69]"
                                    required
                                >
                                @error('no_ktp') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-input-label for="modal_npwp" value="NPWP Usaha / Pribadi" />
                                <input
                                    type="text"
                                    id="modal_npwp"
                                    wire:model.live.debounce.300ms="npwp"
                                    maxlength="24"
                                    class="w-full rounded-full border border-slate-300 px-4 py-2 text-sm tabular-nums focus:outline-hidden focus:border-[#0B7B69]"
                                >
                                @error('npwp') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-input-label for="modal_nama_usaha" value="Nama Usaha Lapangan" required />
                                <input
                                    type="text"
                                    id="modal_nama_usaha"
                                    wire:model.live.debounce.300ms="nama_usaha"
                                    class="w-full rounded-full border {{ $errors->has('nama_usaha') ? 'border-rose-400' : 'border-slate-300' }} px-4 py-2 text-sm focus:outline-hidden focus:border-[#0B7B69]"
                                    required
                                >
                                @error('nama_usaha') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-input-label for="modal_bentuk_badan_usaha" value="Bentuk Badan Usaha" required />
                                <select
                                    id="modal_bentuk_badan_usaha"
                                    wire:model.live="bentuk_badan_usaha"
                                    class="w-full rounded-full border border-slate-300 bg-white px-4 py-2 text-sm focus:outline-hidden focus:border-[#0B7B69]"
                                >
                                    <option value="Perorangan">Perorangan</option>
                                    <option value="CV / Badan Usaha">CV / Badan Usaha</option>
                                    <option value="PT / Perseroan">PT / Perseroan</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="modal_kota" value="Kota Operasional" required />
                                <input
                                    type="text"
                                    id="modal_kota"
                                    wire:model.live.debounce.300ms="kota_operasional"
                                    class="w-full rounded-full border {{ $errors->has('kota_operasional') ? 'border-rose-400' : 'border-slate-300' }} px-4 py-2 text-sm focus:outline-hidden focus:border-[#0B7B69]"
                                    required
                                >
                                @error('kota_operasional') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-input-label for="modal_bank_nama" value="Nama Bank Pencairan" required />
                                <select
                                    id="modal_bank_nama"
                                    wire:model.live="bank_nama"
                                    class="w-full rounded-full border border-slate-300 bg-white px-4 py-2 text-sm focus:outline-hidden focus:border-[#0B7B69]"
                                >
                                    <option value="BCA">BCA</option>
                                    <option value="Mandiri">Mandiri</option>
                                    <option value="BRI">BRI</option>
                                    <option value="BNI">BNI</option>
                                    <option value="BSI">BSI</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="modal_nomor_rekening" value="Nomor Rekening" required />
                                <input
                                    type="text"
                                    id="modal_nomor_rekening"
                                    wire:model.live.debounce.300ms="nomor_rekening"
                                    class="w-full rounded-full border {{ $errors->has('nomor_rekening') ? 'border-rose-400' : 'border-slate-300' }} px-4 py-2 text-sm tabular-nums focus:outline-hidden focus:border-[#0B7B69]"
                                    required
                                >
                                @error('nomor_rekening') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-input-label for="modal_nama_rekening" value="Nama Pemilik Rekening" required />
                                <input
                                    type="text"
                                    id="modal_nama_rekening"
                                    wire:model.live.debounce.300ms="nama_rekening"
                                    class="w-full rounded-full border {{ $errors->has('nama_rekening') ? 'border-rose-400' : 'border-slate-300' }} px-4 py-2 text-sm focus:outline-hidden focus:border-[#0B7B69]"
                                    required
                                >
                                @error('nama_rekening') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <x-input-label for="modal_deskripsi" value="Deskripsi Usaha" />
                            <textarea
                                id="modal_deskripsi"
                                wire:model.live.debounce.300ms="deskripsi_usaha"
                                rows="2"
                                class="w-full rounded-2xl border border-slate-300 px-4 py-2 text-sm focus:outline-hidden focus:border-[#0B7B69]"
                            ></textarea>
                        </div>

                        <!-- Grid Pembaruan Dokumen -->
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">Perbarui Dokumen (Opsional jika tidak berubah)</h4>
                            @php
                                $modalDocs = [
                                    ['key' => 'file_ktp', 'label' => 'KTP Pemilik', 'existing' => $currentProfile?->file_ktp],
                                    ['key' => 'file_npwp', 'label' => 'NPWP Usaha / Pribadi', 'existing' => $currentProfile?->file_npwp],
                                    ['key' => 'file_nib', 'label' => 'NIB / Izin OSS', 'existing' => $currentProfile?->file_nib],
                                    ['key' => 'file_sewa_lahan', 'label' => 'Surat Sewa / SHM', 'existing' => $currentProfile?->file_sewa_lahan],
                                ];
                            @endphp
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                @foreach ($modalDocs as $doc)
                                    @php $key = $doc['key']; $fileVal = $this->{$key}; @endphp
                                    <div
                                        x-data="{ isUploading: false, progress: 0 }"
                                        x-on:livewire-upload-start="isUploading = true; progress = 0"
                                        x-on:livewire-upload-finish="isUploading = false; progress = 100"
                                        x-on:livewire-upload-error="isUploading = false"
                                        x-on:livewire-upload-progress="progress = $event.detail.progress"
                                        class="p-4 rounded-2xl border border-slate-200 bg-slate-50/70"
                                    >
                                        <div class="flex items-center justify-between mb-1.5">
                                            <span class="text-xs font-bold text-slate-900">{{ $doc['label'] }}</span>
                                            @if ($doc['existing'])
                                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-50 text-[#0B7B69] font-semibold border border-emerald-200">Tersimpan</span>
                                            @endif
                                        </div>

                                        <input
                                            type="file"
                                            wire:model="{{ $key }}"
                                            accept=".jpg,.jpeg,.png,.pdf"
                                            class="block w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-[11px] file:font-semibold file:bg-emerald-50 file:text-[#0B7B69] hover:file:bg-emerald-100 cursor-pointer"
                                        >

                                        <div x-show="isUploading" x-cloak class="mt-2">
                                            <div class="w-full h-1 bg-slate-200 rounded-full overflow-hidden">
                                                <div class="h-full bg-[#0B7B69] transition-all" :style="'width: ' + progress + '%'"></div>
                                            </div>
                                        </div>

                                        @if ($fileVal && !$errors->has($key))
                                            <div class="mt-2 flex items-center justify-between text-[11px] text-emerald-700 font-medium">
                                                <span class="truncate">{{ $fileVal->getClientOriginalName() }}</span>
                                                <button type="button" wire:click="removeFile('{{ $key }}')" class="text-rose-600 hover:underline cursor-pointer">Batal</button>
                                            </div>
                                        @endif

                                        @error($key) <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Checkbox Persetujuan -->
                        <div class="pt-3 border-t border-slate-100">
                            <label class="flex items-start gap-2.5 cursor-pointer select-none">
                                <input type="checkbox" wire:model.live="setuju_syarat" class="mt-0.5 w-4 h-4 rounded-md border-slate-300 text-[#0B7B69] focus:ring-[#0B7B69] cursor-pointer">
                                <span class="text-xs text-slate-600">
                                    Saya menyatakan perbaikan data dan berkas yang diajukan ulang ini sudah benar dan akurat.
                                </span>
                            </label>
                            @error('setuju_syarat') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Modal Footer Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                            <button
                                type="button"
                                wire:click="closeResubmitModal"
                                class="px-5 py-2.5 rounded-full border border-slate-300 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 transition cursor-pointer min-h-[42px]"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="resubmit,file_ktp,file_npwp,file_nib,file_sewa_lahan"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-xs sm:text-sm font-semibold shadow-xs transition cursor-pointer min-h-[42px] disabled:opacity-60"
                            >
                                <span wire:loading.remove wire:target="resubmit">Kirim Ulang Pengajuan</span>
                                <span wire:loading wire:target="resubmit" class="inline-flex items-center gap-1.5">
                                    <svg class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    <span>Mengirim...</span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

</div>
