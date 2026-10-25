<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F4F7F5]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perbaiki Dokumen Mitra — SportPlace</title>

    <!-- Google Fonts Poppins (DS II §1) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="min-h-full font-sans text-slate-800 antialiased bg-[#F4F7F5] flex flex-col justify-between">

    <!-- Navbar -->
    <header class="bg-[#06231B] text-white py-3.5 px-4 sm:px-8 border-b border-[#0A3025] sticky top-0 z-30 shadow-xs">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <x-application-logo subtitle="MitraHub" :dark="true" />
            <a href="{{ route('mitra.status') }}" class="text-xs font-semibold text-emerald-300 hover:underline">
                Kembali ke Status
            </a>
        </div>
    </header>

    <main class="max-w-3xl w-full mx-auto px-4 py-8 sm:py-10">

        <!-- Banner Alasan Penolakan dari Admin -->
        <div class="mb-8 rounded-2xl bg-rose-50 border-2 border-rose-200 p-5 sm:p-6">
            <div class="flex items-center gap-2 mb-2 text-rose-800 font-bold text-sm">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Catatan Perbaikan dari Tim Verifikator Admin:</span>
            </div>
            <p class="text-sm text-rose-900 font-medium leading-relaxed bg-white/70 p-3.5 rounded-xl border border-rose-200/80">
                &ldquo;{{ $profile->alasan_penolakan }}&rdquo;
            </p>
            <p class="text-xs text-rose-700 mt-2">
                Silakan perbaiki data atau unggah ulang dokumen yang sesuai dengan catatan di atas, lalu klik <strong>Kirim Ulang Pengajuan</strong>.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-700">
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('mitra.resubmit.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Data Usaha -->
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-7 shadow-xs">
                <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 mb-4">1. Data Usaha & Rekening</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <x-input-label for="nama_usaha" value="Nama Usaha Lapangan" required />
                        <x-text-input
                            type="text"
                            name="nama_usaha"
                            id="nama_usaha"
                            :value="old('nama_usaha', $profile->nama_usaha)"
                            required
                        />
                        <x-input-error :messages="$errors->get('nama_usaha')" />
                    </div>

                    <div>
                        <x-input-label for="bentuk_badan_usaha" value="Bentuk Badan Usaha" required />
                        <x-select-input name="bentuk_badan_usaha" id="bentuk_badan_usaha" required>
                            <option value="Perorangan" {{ old('bentuk_badan_usaha', $profile->bentuk_badan_usaha) === 'Perorangan' ? 'selected' : '' }}>Perorangan</option>
                            <option value="CV / Badan Usaha" {{ old('bentuk_badan_usaha', $profile->bentuk_badan_usaha) === 'CV / Badan Usaha' ? 'selected' : '' }}>CV / Badan Usaha</option>
                            <option value="PT / Perseroan" {{ old('bentuk_badan_usaha', $profile->bentuk_badan_usaha) === 'PT / Perseroan' ? 'selected' : '' }}>PT / Perseroan</option>
                        </x-select-input>
                    </div>

                    <div>
                        <x-input-label for="kota_operasional" value="Kota Operasional" required />
                        <x-text-input
                            type="text"
                            name="kota_operasional"
                            id="kota_operasional"
                            :value="old('kota_operasional', $profile->kota_operasional)"
                            required
                        />
                    </div>

                    <div>
                        <x-input-label for="no_ktp" value="Nomor KTP (NIK)" required />
                        <x-text-input
                            type="text"
                            name="no_ktp"
                            id="no_ktp"
                            :value="old('no_ktp', $profile->no_ktp)"
                            required
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="bank_nama" value="Bank Pencairan" required />
                        <x-select-input name="bank_nama" id="bank_nama" required>
                            <option value="BCA" {{ old('bank_nama', $profile->bank_nama) === 'BCA' ? 'selected' : '' }}>BCA</option>
                            <option value="Mandiri" {{ old('bank_nama', $profile->bank_nama) === 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                            <option value="BRI" {{ old('bank_nama', $profile->bank_nama) === 'BRI' ? 'selected' : '' }}>BRI</option>
                            <option value="BNI" {{ old('bank_nama', $profile->bank_nama) === 'BNI' ? 'selected' : '' }}>BNI</option>
                        </x-select-input>
                    </div>

                    <div>
                        <x-input-label for="nomor_rekening" value="Nomor Rekening" required />
                        <x-text-input
                            type="text"
                            name="nomor_rekening"
                            id="nomor_rekening"
                            :value="old('nomor_rekening', $profile->nomor_rekening)"
                            required
                        />
                    </div>

                    <div>
                        <x-input-label for="nama_rekening" value="Atas Nama Rekening" required />
                        <x-text-input
                            type="text"
                            name="nama_rekening"
                            id="nama_rekening"
                            :value="old('nama_rekening', $profile->nama_rekening)"
                            required
                        />
                    </div>
                </div>
            </div>

            <!-- Upload Ulang Dokumen -->
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-7 shadow-xs">
                <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 mb-4">2. Unggah Ulang Dokumen Revisi</h3>
                <p class="text-xs text-slate-500 mb-5">Pilih berkas baru jika dokumen sebelumnya perlu diganti. Kosongkan jika dokumen sudah benar.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                        <span class="text-xs font-bold text-slate-900 block mb-1">Unggah KTP Baru</span>
                        @if ($profile->file_ktp)
                            <span class="inline-flex items-center gap-1 text-[10px] text-emerald-600 mb-2 font-medium">
                                <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                Berkas KTP lama telah terunggah
                            </span>
                        @endif
                        <input type="file" name="file_ktp" accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-emerald-50 file:text-[#0B7B69] cursor-pointer">
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                        <span class="text-xs font-bold text-slate-900 block mb-1">Unggah NPWP Baru</span>
                        @if ($profile->file_npwp)
                            <span class="inline-flex items-center gap-1 text-[10px] text-emerald-600 mb-2 font-medium">
                                <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                Berkas NPWP lama telah terunggah
                            </span>
                        @endif
                        <input type="file" name="file_npwp" accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-emerald-50 file:text-[#0B7B69] cursor-pointer">
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                        <span class="text-xs font-bold text-slate-900 block mb-1">Unggah NIB Baru</span>
                        @if ($profile->file_nib)
                            <span class="inline-flex items-center gap-1 text-[10px] text-emerald-600 mb-2 font-medium">
                                <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                Berkas NIB lama telah terunggah
                            </span>
                        @endif
                        <input type="file" name="file_nib" accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-emerald-50 file:text-[#0B7B69] cursor-pointer">
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                        <span class="text-xs font-bold text-slate-900 block mb-1">Surat Sewa Lahan</span>
                        @if ($profile->file_sewa_lahan)
                            <span class="inline-flex items-center gap-1 text-[10px] text-emerald-600 mb-2 font-medium">
                                <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                Berkas lama terunggah
                            </span>
                        @endif
                        <input type="file" name="file_sewa_lahan" accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-emerald-50 file:text-[#0B7B69] cursor-pointer">
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100">
                    <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer select-none">
                        <input type="checkbox" name="setuju_syarat" value="1" class="w-4 h-4 rounded text-[#0B7B69] cursor-pointer" required checked>
                        <span>Saya menyatakan data yang diperbaiki telah benar dan sah sesuai aslinya.</span>
                    </label>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-slate-200/80">
                <a
                    href="{{ route('mitra.status') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 rounded-xl bg-white border border-white text-slate-700 hover:text-slate-900 hover:bg-slate-50 text-sm font-semibold shadow-xs transition duration-150 ease-in-out min-h-[44px] cursor-pointer"
                >
                    <span>Batal</span>
                </a>
                <button
                    type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-[#0B7B69] hover:bg-[#086354] px-6 py-3 text-sm font-semibold text-white shadow-xs transition duration-150 ease-in-out cursor-pointer min-h-[44px]"
                >
                    <span>Kirim Ulang Pengajuan</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </div>
        </form>
    </main>

    <footer class="bg-white border-t border-slate-200 py-6 px-4 text-center text-xs text-slate-500 mt-12">
        <p>&copy; {{ date('Y') }} SportPlace Mitra Indonesia. Seluruh hak cipta dilindungi undang-undang.</p>
    </footer>

</body>
</html>

