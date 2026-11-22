<div class="max-w-4xl w-full mx-auto px-4 py-8 sm:py-10">

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

    <!-- 3-Step Indicator Bar -->
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

    <form wire:submit="submit" class="space-y-6">
        <!-- Card 1: Data Pemilik & NIK -->
        <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-[#0B7B69]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </span>
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
                    <input
                        type="text"
                        id="no_ktp"
                        wire:model.live.debounce.300ms="no_ktp"
                        placeholder="16 digit NIK"
                        maxlength="20"
                        class="w-full rounded-full border {{ $errors->has('no_ktp') ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-500/20' : 'border-slate-300 focus:border-[#0B7B69] focus:ring-[#0B7B69]/20' }} bg-white px-4 py-2 text-sm text-slate-800 placeholder-slate-400 transition focus:outline-hidden focus:ring-3 tabular-nums"
                        required
                    />
                    @error('no_ktp')
                        <p class="mt-1 text-[11px] font-medium text-rose-600">{{ $message }}</p>
                    @elseif (strlen($no_ktp) >= 16)
                        <p class="mt-1 text-[11px] font-medium text-emerald-600 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            NIK valid ({{ strlen($no_ktp) }} digit)
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Card 2: Data Usaha & Rekening Pencairan -->
        <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-[#0B7B69]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <h2 class="text-base font-bold text-slate-900">2. Ringkasan Data Usaha & Rekening Pencairan</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                <div>
                    <x-input-label for="nama_usaha" value="Nama Usaha Lapangan" required />
                    <input
                        type="text"
                        id="nama_usaha"
                        wire:model.live.debounce.300ms="nama_usaha"
                        placeholder="Contoh: GOR Tirta Sport"
                        class="w-full rounded-full border {{ $errors->has('nama_usaha') ? 'border-rose-400 focus:border-rose-500' : 'border-slate-300 focus:border-[#0B7B69]' }} bg-white px-4 py-2 text-sm text-slate-800 placeholder-slate-400 transition focus:outline-hidden focus:ring-3 focus:ring-[#0B7B69]/20"
                        required
                    />
                    @error('nama_usaha') <p class="mt-1 text-[11px] font-medium text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label for="bentuk_badan_usaha" value="Bentuk Badan Usaha" required />
                    <select
                        id="bentuk_badan_usaha"
                        wire:model.live="bentuk_badan_usaha"
                        class="w-full rounded-full border border-slate-300 bg-white px-4 py-2 text-sm text-slate-800 transition focus:outline-hidden focus:border-[#0B7B69] focus:ring-3 focus:ring-[#0B7B69]/20"
                        required
                    >
                        <option value="Perorangan">Perorangan</option>
                        <option value="CV / Badan Usaha">CV / Badan Usaha</option>
                        <option value="PT / Perseroan">PT / Perseroan</option>
                    </select>
                    @error('bentuk_badan_usaha') <p class="mt-1 text-[11px] font-medium text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label for="kota_operasional" value="Kota / Wilayah Operasional" required />
                    <input
                        type="text"
                        id="kota_operasional"
                        wire:model.live.debounce.300ms="kota_operasional"
                        placeholder="Contoh: Denpasar, Bali"
                        class="w-full rounded-full border {{ $errors->has('kota_operasional') ? 'border-rose-400 focus:border-rose-500' : 'border-slate-300 focus:border-[#0B7B69]' }} bg-white px-4 py-2 text-sm text-slate-800 placeholder-slate-400 transition focus:outline-hidden focus:ring-3 focus:ring-[#0B7B69]/20"
                        required
                    />
                    @error('kota_operasional') <p class="mt-1 text-[11px] font-medium text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-4">
                <div>
                    <x-input-label for="npwp" value="NPWP Usaha / Pribadi" />
                    <input
                        type="text"
                        id="npwp"
                        wire:model.live.debounce.300ms="npwp"
                        placeholder="00.000.000.0-000.000"
                        maxlength="24"
                        class="w-full rounded-full border {{ $errors->has('npwp') ? 'border-rose-400 focus:border-rose-500' : 'border-slate-300 focus:border-[#0B7B69]' }} bg-white px-4 py-2 text-sm text-slate-800 placeholder-slate-400 transition focus:outline-hidden focus:ring-3 focus:ring-[#0B7B69]/20 tabular-nums"
                    />
                    @error('npwp') <p class="mt-1 text-[11px] font-medium text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label for="bank_nama" value="Nama Bank" required />
                    <select
                        id="bank_nama"
                        wire:model.live="bank_nama"
                        class="w-full rounded-full border border-slate-300 bg-white px-4 py-2 text-sm text-slate-800 transition focus:outline-hidden focus:border-[#0B7B69] focus:ring-3 focus:ring-[#0B7B69]/20"
                        required
                    >
                        <option value="BCA">BCA</option>
                        <option value="Mandiri">Mandiri</option>
                        <option value="BRI">BRI</option>
                        <option value="BNI">BNI</option>
                        <option value="BSI">BSI</option>
                    </select>
                    @error('bank_nama') <p class="mt-1 text-[11px] font-medium text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label for="nomor_rekening" value="Nomor Rekening" required />
                    <input
                        type="text"
                        id="nomor_rekening"
                        wire:model.live.debounce.300ms="nomor_rekening"
                        placeholder="Contoh: 8205128901"
                        class="w-full rounded-full border {{ $errors->has('nomor_rekening') ? 'border-rose-400 focus:border-rose-500' : 'border-slate-300 focus:border-[#0B7B69]' }} bg-white px-4 py-2 text-sm text-slate-800 placeholder-slate-400 transition focus:outline-hidden focus:ring-3 focus:ring-[#0B7B69]/20 tabular-nums"
                        required
                    />
                    @error('nomor_rekening') <p class="mt-1 text-[11px] font-medium text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label for="nama_rekening" value="Nama Pemilik Rekening" required />
                    <input
                        type="text"
                        id="nama_rekening"
                        wire:model.live.debounce.300ms="nama_rekening"
                        placeholder="Sesuai buku tabungan"
                        class="w-full rounded-full border {{ $errors->has('nama_rekening') ? 'border-rose-400 focus:border-rose-500' : 'border-slate-300 focus:border-[#0B7B69]' }} bg-white px-4 py-2 text-sm text-slate-800 placeholder-slate-400 transition focus:outline-hidden focus:ring-3 focus:ring-[#0B7B69]/20"
                        required
                    />
                    @error('nama_rekening') <p class="mt-1 text-[11px] font-medium text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <x-input-label for="deskripsi_usaha" value="Deskripsi Fasilitas & Lapangan Singkat" />
                <textarea
                    id="deskripsi_usaha"
                    wire:model.live.debounce.400ms="deskripsi_usaha"
                    rows="2"
                    placeholder="Contoh: Pusat fasilitas olahraga terpadu dengan 4 lapangan futsal vinyl dan 2 court badminton standar BWF."
                    class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition focus:outline-hidden focus:border-[#0B7B69] focus:ring-3 focus:ring-[#0B7B69]/20"
                ></textarea>
                @error('deskripsi_usaha') <p class="mt-1 text-[11px] font-medium text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Card 3: Dokumen Legalitas Usaha (Livewire WithFileUploads + Progress Bar) -->
        <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-4 border-b border-slate-100 mb-5 gap-2">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#06231B] text-white text-xs font-bold">3</span>
                    <h2 class="text-base font-bold text-slate-900">Dokumen Legalitas Usaha</h2>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-[#0B7B69] border border-emerald-200">
                    <svg class="w-3.5 h-3.5 text-[#0B7B69] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span>Verifikasi Kepatuhan Platform</span>
                </span>
            </div>
            <p class="text-xs text-slate-500 mb-6 leading-relaxed">
                Unggah dokumen resmi untuk verifikasi keamanan dan kelayakan pembayaran platform. Format yang didukung: JPG, PNG, atau PDF (maksimal 5 MB per berkas).
            </p>

            @php
                $docFields = [
                    [
                        'key' => 'file_ktp',
                        'label' => 'KTP Pemilik',
                        'required' => true,
                        'desc' => 'KTP Asli pemilik usaha sesuai NIK yang didaftarkan.',
                    ],
                    [
                        'key' => 'file_npwp',
                        'label' => 'NPWP Usaha / Pribadi',
                        'required' => false,
                        'desc' => 'Kartu atau surat keterangan NPWP yang masih aktif.',
                    ],
                    [
                        'key' => 'file_nib',
                        'label' => 'NIB / Izin Usaha OSS',
                        'required' => false,
                        'desc' => 'Nomor Induk Berusaha resmi diterbitkan Lembaga OSS.',
                    ],
                    [
                        'key' => 'file_sewa_lahan',
                        'label' => 'Surat Kepemilikan / Sewa Lahan',
                        'required' => false,
                        'desc' => 'Sertifikat hak milik atau perjanjian sewa lokasi lapangan.',
                    ],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach ($docFields as $doc)
                    @php $fieldKey = $doc['key']; $currentFile = $this->{$fieldKey}; @endphp
                    <div
                        x-data="{ isUploading: false, progress: 0 }"
                        x-on:livewire-upload-start="isUploading = true; progress = 0"
                        x-on:livewire-upload-finish="isUploading = false; progress = 100"
                        x-on:livewire-upload-cancel="isUploading = false"
                        x-on:livewire-upload-error="isUploading = false"
                        x-on:livewire-upload-progress="progress = $event.detail.progress"
                        class="p-5 rounded-2xl border {{ $errors->has($fieldKey) ? 'border-rose-300 bg-rose-50/30' : ($currentFile ? 'border-emerald-300 bg-emerald-50/20' : 'border-slate-200 bg-slate-50/50 hover:bg-slate-50') }} transition"
                    >
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-bold text-slate-900">
                                {{ $doc['label'] }}
                                @if ($doc['required']) <span class="text-rose-500">*</span> @endif
                            </span>
                            @if ($doc['required'])
                                <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 font-semibold border border-amber-200">Wajib</span>
                            @else
                                <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 font-medium">Opsional</span>
                            @endif
                        </div>

                        <p class="text-[11px] text-slate-500 mb-3">{{ $doc['desc'] }}</p>

                        <!-- Upload Input -->
                        <input
                            type="file"
                            id="{{ $fieldKey }}"
                            wire:model="{{ $fieldKey }}"
                            accept=".jpg,.jpeg,.png,.pdf"
                            class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-[#0B7B69] hover:file:bg-emerald-100 cursor-pointer"
                        >

                        <!-- Live Progress Bar -->
                        <div x-show="isUploading" x-cloak class="mt-3">
                            <div class="flex items-center justify-between text-[11px] font-medium text-[#0B7B69] mb-1">
                                <span>Mengunggah berkas...</span>
                                <span x-text="progress + '%'" class="tabular-nums"></span>
                            </div>
                            <div class="w-full h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                <div class="h-full bg-[#0B7B69] transition-all duration-150" :style="'width: ' + progress + '%'"></div>
                            </div>
                        </div>

                        <!-- Preview Status Tersimpan Sementara -->
                        @if ($currentFile && !$errors->has($fieldKey))
                            <div class="mt-3 flex items-center justify-between gap-2 px-3 py-2 rounded-xl bg-white border border-emerald-200 text-xs">
                                <div class="flex items-center gap-2 min-w-0">
                                    <svg class="w-4 h-4 text-[#0B7B69] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="font-medium text-slate-700 truncate">{{ $currentFile->getClientOriginalName() }}</span>
                                </div>
                                <button
                                    type="button"
                                    wire:click="removeFile('{{ $fieldKey }}')"
                                    class="text-rose-500 hover:text-rose-700 font-semibold text-[11px] shrink-0 cursor-pointer"
                                    title="Hapus file"
                                >
                                    Hapus
                                </button>
                            </div>
                        @endif

                        @error($fieldKey)
                            <p class="mt-2 text-[11px] font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>

            <!-- Terms & Privacy Agreement Checkbox -->
            <div class="mt-6 pt-5 border-t border-slate-100">
                <label class="flex items-start gap-3 cursor-pointer select-none">
                    <input
                        type="checkbox"
                        wire:model.live="setuju_syarat"
                        class="mt-1 w-4 h-4 rounded-md border-slate-300 text-[#0B7B69] focus:ring-[#0B7B69] cursor-pointer"
                    >
                    <span class="text-xs text-slate-600 leading-relaxed">
                        Saya menyatakan data yang diberikan adalah sah dan menyetujui
                        <a href="#" class="font-semibold text-[#0B7B69] hover:underline">Syarat & Ketentuan Kemitraan SportPlace</a>
                        serta <a href="#" class="font-semibold text-[#0B7B69] hover:underline">Kebijakan Privasi</a> platform.
                    </span>
                </label>
                @error('setuju_syarat')
                    <p class="mt-1.5 text-[11px] font-medium text-rose-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-4">
            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="submit,file_ktp,file_npwp,file_nib,file_sewa_lahan"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-full bg-[#0B7B69] hover:bg-[#086354] active:scale-95 px-7 py-3 text-sm font-semibold text-white shadow-xs transition duration-150 ease-in-out min-h-[44px] cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed"
            >
                <span wire:loading.remove wire:target="submit" class="inline-flex items-center gap-2">
                    <span>Kirim Pengajuan Kemitraan</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </span>
                <span wire:loading wire:target="submit" class="inline-flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Mengirim Pengajuan...</span>
                </span>
            </button>
        </div>
    </form>

    <!-- WhatsApp Help Banner -->
    <div class="mt-8 rounded-3xl bg-blue-50/80 border border-blue-200/80 p-5 sm:p-6 flex items-center gap-3.5 text-xs text-blue-900">
        <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <div class="leading-relaxed">
            <span>Butuh panduan dokumen legalitas? Hubungi Tim Onboarding Mitra melalui WhatsApp di </span>
            <a href="https://wa.me/6281198765432" target="_blank" class="font-bold underline text-blue-800 hover:text-blue-950">+62 811-9876-5432</a>
            <span>(Senin – Jumat, 09:00 – 18:00 WIB).</span>
        </div>
    </div>

</div>
