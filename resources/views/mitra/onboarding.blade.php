<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F4F7F5]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pendaftaran Mitra — SportPlace</title>

    <!-- Google Fonts Poppins (DS II §1) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="min-h-full font-sans text-slate-800 antialiased bg-[#F4F7F5] flex flex-col justify-between">

    <!-- Top Navbar Sesuai Figma (#06231B) -->
    <header class="bg-[#06231B] text-white py-3.5 px-4 sm:px-8 border-b border-[#0A3025] sticky top-0 z-30 shadow-xs">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <x-application-logo subtitle="MitraHub" :dark="true" />
            <div class="flex items-center gap-4 text-xs">
                <span class="text-emerald-100/70 hidden sm:inline">Pusat Bantuan: +62 811-9876-5432</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-[#07261F] hover:bg-rose-700 text-emerald-100 hover:text-white transition font-semibold cursor-pointer">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="max-w-4xl w-full mx-auto px-4 py-8 sm:py-10">

        <!-- Header Title Section -->
        <div class="mb-8">
            <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-[#0B7B69] border border-emerald-200 mb-3">
                <span class="w-1.5 h-1.5 rounded-full bg-[#0B7B69]"></span>
                PENDAFTARAN MITRA
            </span>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Daftarkan usaha lapanganmu</h1>
            <p class="text-sm text-slate-500 mt-1.5 leading-relaxed">
                Lengkapi data usaha dan dokumen legalitas. Tim kami akan meninjau pengajuanmu dalam 1–2 hari kerja.
            </p>
        </div>

        <!-- 3-Step Indicator Bar Sesuai Desain Figma -->
        <div class="grid grid-cols-3 gap-2 sm:gap-4 mb-8 text-center sm:text-left">
            <div class="border-t-4 border-[#0B7B69] pt-2">
                <div class="flex items-center gap-1.5 justify-center sm:justify-start">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#0B7B69] text-white shrink-0">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                    <span class="text-xs font-bold text-slate-900">Data Pemilik</span>
                </div>
                <span class="text-[11px] text-[#0B7B69] font-medium hidden sm:block">Selesai</span>
            </div>

            <div class="border-t-4 border-[#0B7B69] pt-2">
                <div class="flex items-center gap-1.5 justify-center sm:justify-start">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#0B7B69] text-white shrink-0">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                    <span class="text-xs font-bold text-slate-900">Data Usaha</span>
                </div>
                <span class="text-[11px] text-[#0B7B69] font-medium hidden sm:block">Lengkap</span>
            </div>

            <div class="border-t-4 border-[#0B7B69] pt-2">
                <div class="flex items-center gap-1.5 justify-center sm:justify-start">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#06231B] text-white text-[11px] font-bold shrink-0">3</span>
                    <span class="text-xs font-bold text-slate-900">Dokumen Legalitas</span>
                </div>
                <span class="text-[11px] text-slate-500 font-medium hidden sm:block">Tahap Akhir</span>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-700">
                <div class="font-bold mb-1 flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <span>Terdapat beberapa data yang perlu diperbaiki:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('mitra.onboarding.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Card 1: Data Pemilik & NIK -->
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-7 shadow-xs">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-[#0B7B69] text-xs font-bold">✓</span>
                        <h2 class="text-base font-bold text-slate-900">1. Data Pemilik Lapangan</h2>
                    </div>
                    <span class="text-xs text-slate-400">Akun Terdaftar</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block mb-1">Nama Lengkap</span>
                        <span class="font-semibold text-slate-800 text-sm">{{ $user->name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Email Terdaftar</span>
                        <span class="font-semibold text-slate-800 text-sm truncate block">{{ $user->email }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Nomor WhatsApp</span>
                        <span class="font-semibold text-slate-800 text-sm">{{ $user->no_hp }}</span>
                    </div>
                    <div>
                        <x-input-label for="no_ktp" value="Nomor KTP (NIK)" required class="text-xs mb-1 text-slate-600" />
                        <x-text-input
                            type="text"
                            name="no_ktp"
                            id="no_ktp"
                            :value="old('no_ktp', $profile?->no_ktp)"
                            placeholder="16 digit NIK"
                            maxlength="20"
                            required
                        />
                        <x-input-error :messages="$errors->get('no_ktp')" />
                    </div>
                </div>
            </div>

            <!-- Card 2: Data Usaha & Rekening Pencairan -->
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-7 shadow-xs">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-[#0B7B69] text-xs font-bold">✓</span>
                        <h2 class="text-base font-bold text-slate-900">2. Ringkasan Data Usaha & Rekening Pencairan</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div>
                        <x-input-label for="nama_usaha" value="Nama Usaha Lapangan" required />
                        <x-text-input
                            type="text"
                            name="nama_usaha"
                            id="nama_usaha"
                            :value="old('nama_usaha', $profile?->nama_usaha)"
                            placeholder="Contoh: GOR Tirta Sport"
                            required
                        />
                        <x-input-error :messages="$errors->get('nama_usaha')" />
                    </div>

                    <div>
                        <x-input-label for="bentuk_badan_usaha" value="Bentuk Badan Usaha" required />
                        <x-select-input name="bentuk_badan_usaha" id="bentuk_badan_usaha" required>
                            <option value="Perorangan" {{ old('bentuk_badan_usaha', $profile?->bentuk_badan_usaha) === 'Perorangan' ? 'selected' : '' }}>Perorangan</option>
                            <option value="CV / Badan Usaha" {{ old('bentuk_badan_usaha', $profile?->bentuk_badan_usaha) === 'CV / Badan Usaha' ? 'selected' : '' }}>CV / Badan Usaha</option>
                            <option value="PT / Perseroan" {{ old('bentuk_badan_usaha', $profile?->bentuk_badan_usaha) === 'PT / Perseroan' ? 'selected' : '' }}>PT / Perseroan</option>
                        </x-select-input>
                        <x-input-error :messages="$errors->get('bentuk_badan_usaha')" />
                    </div>

                    <div>
                        <x-input-label for="kota_operasional" value="Kota / Wilayah Operasional" required />
                        <x-text-input
                            type="text"
                            name="kota_operasional"
                            id="kota_operasional"
                            :value="old('kota_operasional', $profile?->kota_operasional)"
                            placeholder="Contoh: Denpasar, Bali"
                            required
                        />
                        <x-input-error :messages="$errors->get('kota_operasional')" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-4">
                    <div>
                        <x-input-label for="npwp" value="NPWP Usaha / Pribadi" />
                        <x-text-input
                            type="text"
                            name="npwp"
                            id="npwp"
                            :value="old('npwp', $profile?->npwp)"
                            placeholder="00.000.000.0-000.000"
                            maxlength="24"
                        />
                        <x-input-error :messages="$errors->get('npwp')" />
                    </div>

                    <div>
                        <x-input-label for="bank_nama" value="Nama Bank" required />
                        <x-select-input name="bank_nama" id="bank_nama" required>
                            <option value="BCA" {{ old('bank_nama', $profile?->bank_nama) === 'BCA' ? 'selected' : '' }}>BCA</option>
                            <option value="Mandiri" {{ old('bank_nama', $profile?->bank_nama) === 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                            <option value="BRI" {{ old('bank_nama', $profile?->bank_nama) === 'BRI' ? 'selected' : '' }}>BRI</option>
                            <option value="BNI" {{ old('bank_nama', $profile?->bank_nama) === 'BNI' ? 'selected' : '' }}>BNI</option>
                            <option value="BSI" {{ old('bank_nama', $profile?->bank_nama) === 'BSI' ? 'selected' : '' }}>BSI</option>
                        </x-select-input>
                        <x-input-error :messages="$errors->get('bank_nama')" />
                    </div>

                    <div>
                        <x-input-label for="nomor_rekening" value="Nomor Rekening" required />
                        <x-text-input
                            type="text"
                            name="nomor_rekening"
                            id="nomor_rekening"
                            :value="old('nomor_rekening', $profile?->nomor_rekening)"
                            placeholder="Contoh: 8205128901"
                            required
                        />
                        <x-input-error :messages="$errors->get('nomor_rekening')" />
                    </div>

                    <div>
                        <x-input-label for="nama_rekening" value="Nama Pemilik Rekening" required />
                        <x-text-input
                            type="text"
                            name="nama_rekening"
                            id="nama_rekening"
                            :value="old('nama_rekening', $profile?->nama_rekening)"
                            placeholder="Sesuai buku tabungan"
                            required
                        />
                        <x-input-error :messages="$errors->get('nama_rekening')" />
                    </div>
                </div>

                <div>
                    <x-input-label for="deskripsi_usaha" value="Deskripsi Fasilitas & Lapangan Singkat" />
                    <textarea
                        name="deskripsi_usaha"
                        id="deskripsi_usaha"
                        rows="2"
                        placeholder="Contoh: Pusat fasilitas olahraga terpadu dengan 4 lapangan futsal vinyl dan 2 court badminton standar BWF."
                        class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition focus:outline-hidden focus:border-[#0B7B69] focus:ring-3 focus:ring-[#0B7B69]/20"
                    >{{ old('deskripsi_usaha', $profile?->deskripsi_usaha) }}</textarea>
                    <x-input-error :messages="$errors->get('deskripsi_usaha')" />
                </div>
            </div>

            <!-- Card 3: Dokumen Legalitas Usaha Sesuai Figma -->
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-7 shadow-xs">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-4 border-b border-slate-100 mb-5 gap-2">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#06231B] text-white text-xs font-bold">3</span>
                        <h2 class="text-base font-bold text-slate-900">Dokumen Legalitas Usaha</h2>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-[#0B7B69] border border-emerald-200">
                        <span>🛡️</span>
                        <span>Verifikasi Kepatuhan Platform</span>
                    </span>
                </div>
                <p class="text-xs text-slate-500 mb-6 leading-relaxed">
                    Unggah dokumen resmi untuk verifikasi keamanan dan kelayakan pembayaran platform. Format yang didukung: JPG, PNG, atau PDF (maksimal 5 MB per berkas).
                </p>

                <!-- Grid 4 Upload Boxes -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- 1. KTP Pemilik -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-bold text-slate-900">KTP Pemilik <span class="text-rose-500">*</span></span>
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 font-semibold border border-amber-200">Wajib</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mb-3">KTP Asli pemilik usaha sesuai NIK yang didaftarkan.</p>
                        <input
                            type="file"
                            name="file_ktp"
                            id="file_ktp"
                            accept=".jpg,.jpeg,.png,.pdf"
                            class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-[#0B7B69] hover:file:bg-emerald-100 cursor-pointer"
                            required
                        >
                        <x-input-error :messages="$errors->get('file_ktp')" />
                    </div>

                    <!-- 2. NPWP Usaha / Pribadi -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-bold text-slate-900">NPWP Usaha / Pribadi</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-medium">Opsional</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mb-3">Kartu atau surat keterangan NPWP yang masih aktif.</p>
                        <input
                            type="file"
                            name="file_npwp"
                            id="file_npwp"
                            accept=".jpg,.jpeg,.png,.pdf"
                            class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-[#0B7B69] hover:file:bg-emerald-100 cursor-pointer"
                        >
                        <x-input-error :messages="$errors->get('file_npwp')" />
                    </div>

                    <!-- 3. NIB / Izin Usaha OSS -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-bold text-slate-900">NIB / Izin Usaha OSS</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-medium">Opsional</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mb-3">Nomor Induk Berusaha resmi diterbitkan Lembaga OSS.</p>
                        <input
                            type="file"
                            name="file_nib"
                            id="file_nib"
                            accept=".jpg,.jpeg,.png,.pdf"
                            class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-[#0B7B69] hover:file:bg-emerald-100 cursor-pointer"
                        >
                        <x-input-error :messages="$errors->get('file_nib')" />
                    </div>

                    <!-- 4. Surat Kepemilikan / Sewa Lahan -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-bold text-slate-900">Surat Kepemilikan / Sewa Lahan</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-medium">Opsional</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mb-3">Sertifikat hak milik atau perjanjian sewa lokasi lapangan.</p>
                        <input
                            type="file"
                            name="file_sewa_lahan"
                            id="file_sewa_lahan"
                            accept=".jpg,.jpeg,.png,.pdf"
                            class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-[#0B7B69] hover:file:bg-emerald-100 cursor-pointer"
                        >
                        <x-input-error :messages="$errors->get('file_sewa_lahan')" />
                    </div>
                </div>

                <!-- Terms & Privacy Agreement Checkbox -->
                <div class="mt-6 pt-5 border-t border-slate-100">
                    <label class="flex items-start gap-3 cursor-pointer select-none">
                        <input
                            type="checkbox"
                            name="setuju_syarat"
                            value="1"
                            class="mt-1 w-4 h-4 rounded-md border-slate-300 text-[#0B7B69] focus:ring-[#0B7B69] cursor-pointer"
                            {{ old('setuju_syarat') ? 'checked' : '' }}
                            required
                        >
                        <span class="text-xs text-slate-600 leading-relaxed">
                            Saya menyatakan data yang diberikan adalah sah dan menyetujui
                            <a href="#" class="font-semibold text-[#0B7B69] hover:underline">Syarat & Ketentuan Kemitraan SportPlace</a>
                            serta <a href="#" class="font-semibold text-[#0B7B69] hover:underline">Kebijakan Privasi</a> platform.
                        </span>
                    </label>
                    <x-input-error :messages="$errors->get('setuju_syarat')" />
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-4">
                <button
                    type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-[#0B7B69] hover:bg-[#086354] px-6 py-3 text-sm font-semibold text-white shadow-xs transition duration-150 ease-in-out min-h-[44px] cursor-pointer"
                >
                    <span>Kirim Pengajuan Kemitraan</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </div>
        </form>

        <!-- WhatsApp Help Banner Sesuai Figma -->
        <div class="mt-8 rounded-2xl bg-blue-50/80 border border-blue-200/80 p-4 sm:p-5 flex items-center gap-3.5 text-xs text-blue-900">
            <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <div class="leading-relaxed">
                <span>Butuh panduan dokumen legalitas? Hubungi Tim Onboarding Mitra melalui WhatsApp di </span>
                <a href="https://wa.me/6281198765432" target="_blank" class="font-bold underline text-blue-800 hover:text-blue-950">+62 811-9876-5432</a>
                <span>(Senin – Jumat, 09:00 – 18:00 WIB).</span>
            </div>
        </div>

    </main>

    <!-- Footer Simple -->
    <footer class="bg-white border-t border-slate-200 py-6 px-4 text-center text-xs text-slate-500 mt-12">
        <p>&copy; {{ date('Y') }} SportPlace Mitra Indonesia. Seluruh hak cipta dilindungi undang-undang.</p>
    </footer>

</body>
</html>

