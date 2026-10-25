<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F4F7F5]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Status Verifikasi Akun — SportPlace</title>

    <!-- Google Fonts Poppins (DS II §1) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="min-h-full font-sans text-slate-800 antialiased bg-[#F4F7F5] flex flex-col justify-between">

    <!-- Navbar Sesuai Figma (#06231B) -->
    <header class="bg-[#06231B] text-white py-3.5 px-4 sm:px-8 border-b border-[#0A3025] sticky top-0 z-30 shadow-xs">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <x-application-logo subtitle="MitraHub" :dark="true" />
            <div class="flex items-center gap-4 text-xs">
                <span class="text-emerald-100/70 hidden sm:inline">{{ $user->email }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-[#07261F] hover:bg-rose-700 text-emerald-100 hover:text-white transition font-semibold cursor-pointer">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-3xl w-full mx-auto px-4 py-8 sm:py-10">

        <!-- Header Tag -->
        <div class="mb-4">
            <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-[#0B7B69] border border-emerald-200">
                <span class="w-1.5 h-1.5 rounded-full bg-[#0B7B69]"></span>
                STATUS TRACKER • ONBOARDING MITRA
            </span>
        </div>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="leading-relaxed">{{ session('success') }}</div>
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-6 rounded-2xl bg-amber-50 border border-amber-200 p-4 text-sm text-amber-800 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="leading-relaxed">{{ session('warning') }}</div>
            </div>
        @endif

        @if ($profile->isRejected())
            <!-- VARIAN B: DITOLAK / REVISI DIPERLUKAN (Sesuai Figma) -->
            <div class="bg-white rounded-2xl border-2 border-rose-300 p-6 sm:p-8 shadow-xs mb-8">
                <div class="flex items-center justify-between pb-4 border-b border-rose-100 mb-6">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                        Pengajuan Ditolak
                    </span>
                    <span class="text-xs font-semibold text-rose-600">Revisi Diperlukan</span>
                </div>

                <h2 class="text-xl font-bold text-slate-900 mb-2">Alasan Penolakan dari Admin</h2>
                <div class="p-4 rounded-xl bg-rose-50/70 border border-rose-200 text-sm text-rose-900 leading-relaxed font-medium mb-6">
                    &ldquo;{{ $profile->alasan_penolakan ?? 'Dokumen legalitas yang diunggah belum memenuhi persyaratan verifikasi.' }}&rdquo;
                </div>

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

                    <a
                        href="{{ route('mitra.resubmit') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 hover:bg-rose-700 px-6 py-3 text-sm font-semibold text-white shadow-xs transition duration-150 ease-in-out min-h-[44px]"
                    >
                        <span>Perbaiki & Ajukan Ulang</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>
            </div>

        @elseif ($profile->isApproved())
            <!-- VARIAN C: DISETUJUI / SUDAH AKTIF (Sesuai Figma) -->
            <div class="bg-white rounded-2xl border-2 border-emerald-300 p-6 sm:p-8 shadow-xs mb-8">
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
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 text-center">
                        <span class="text-xs font-bold text-[#0B7B69] block mb-1">Tahap 1</span>
                        <span class="text-sm font-semibold text-slate-800">Profil Venue</span>
                    </div>
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 text-center">
                        <span class="text-xs font-bold text-[#0B7B69] block mb-1">Tahap 2</span>
                        <span class="text-sm font-semibold text-slate-800">Unit Lapangan</span>
                    </div>
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 text-center">
                        <span class="text-xs font-bold text-[#0B7B69] block mb-1">Tahap 3</span>
                        <span class="text-sm font-semibold text-slate-800">Jadwal & Tarif</span>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <a
                        href="{{ route('mitra.dashboard') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-[#0B7B69] hover:bg-[#086354] px-6 py-3 text-sm font-semibold text-white shadow-xs transition duration-150 ease-in-out min-h-[44px]"
                    >
                        <span>Masuk ke Dashboard Mitra</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>
            </div>

        @else
            <!-- VARIAN A: MENUNGGU VERIFIKASI (Sesuai Figma) -->
            <div class="bg-white rounded-2xl border border-slate-200/90 p-6 sm:p-8 shadow-xs mb-8">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                        <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Menunggu Verifikasi</span>
                    </span>
                    <span class="text-xs text-slate-400">ID: REG-{{ date('Ymd', strtotime($profile->created_at ?? now())) }}-{{ str_pad($profile->id, 3, '0', STR_PAD_LEFT) }}</span>
                </div>

                <h2 class="text-2xl font-bold text-slate-900 mb-2">Pengajuanmu sedang ditinjau</h2>
                <p class="text-sm text-slate-500 mb-8 leading-relaxed">
                    Terima kasih telah mendaftar di SportPlace. Tim verifikator kami sedang memeriksa keabsahan dokumen legalitas dan data usahamu. Proses ini membutuhkan 1–2 hari kerja.
                </p>

                <!-- Vertical Timeline Sesuai Desain Figma (Presisi Tegak Lurus) -->
                <div class="relative space-y-0">
                    <!-- Garis Latar Belakang Vertikal Abu-abu (Penuh Step 1 s/d Step 4) -->
                    <div class="absolute left-3.5 top-3.5 bottom-3.5 w-0.5 bg-slate-200 -translate-x-1/2" aria-hidden="true"></div>
                    <!-- Garis Progres Hijau Aktif (Menghubungkan Step 1 ke Step 2) -->
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
                                <span class="text-xs text-slate-400 font-medium">{{ $profile->created_at ? $profile->created_at->format('d M Y • H:i') : now()->format('d M Y • H:i') }} WIB</span>
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
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-[#0B7B69] border border-emerald-200">Sedang Berjalan</span>
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

                <!-- Ringkasan Pengajuan Mitra Box Sesuai Figma -->
                <div class="mt-8 p-5 rounded-xl bg-slate-50/70 border border-slate-200">
                    <span class="text-xs font-bold text-slate-700 block mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span>Ringkasan Pengajuan Mitra</span>
                    </span>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs mb-4">
                        <div>
                            <span class="text-slate-400 block mb-0.5">Nama Usaha</span>
                            <span class="font-bold text-slate-900 text-sm">{{ $profile->nama_usaha ?? 'Belum diisi' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-0.5">Lokasi & Kota</span>
                            <span class="font-bold text-slate-900 text-sm">{{ $profile->kota_operasional ?? 'Belum diisi' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-0.5">Tanggal Registrasi</span>
                            <span class="font-bold text-slate-900 text-sm">{{ $profile->created_at ? $profile->created_at->format('d F Y') : now()->format('d F Y') }}</span>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-slate-200 text-[11px]">
                        <span class="text-slate-500 font-medium">Dokumen:</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-[#0B7B69] font-medium border border-emerald-200">
                            <svg class="w-3 h-3 text-[#0B7B69]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            KTP Pemilik (OK)
                        </span>
                        @if ($profile->file_npwp)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-[#0B7B69] font-medium border border-emerald-200">
                                <svg class="w-3 h-3 text-[#0B7B69]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                NPWP Badan (OK)
                            </span>
                        @endif
                        @if ($profile->file_nib)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-[#0B7B69] font-medium border border-emerald-200">
                                <svg class="w-3 h-3 text-[#0B7B69]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                NIB OSS (OK)
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
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
                        onclick="window.location.reload()"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 px-5 py-2.5 text-xs font-semibold text-slate-700 transition min-h-[44px] cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Segarkan Status</span>
                    </button>
                </div>
            </div>
        @endif

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 px-4 text-center text-xs text-slate-500 mt-12">
        <p>&copy; {{ date('Y') }} SportPlace Mitra Indonesia. Seluruh hak cipta dilindungi undang-undang.</p>
    </footer>

</body>
</html>
