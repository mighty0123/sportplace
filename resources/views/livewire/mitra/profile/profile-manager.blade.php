<div class="space-y-6 max-w-full min-w-0" @keydown.window.escape="if ($wire.showEditModal) $wire.closeEditModal()">

    <!-- Header Banner Profil & Status Legalitas -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex items-start gap-4 sm:gap-5">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-emerald-50 text-[#0B7B69] border border-emerald-100 flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1.5">
                        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                            {{ $this->profile->nama_usaha }}
                        </h1>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-[#0B7B69] border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-[#0B7B69]"></span>
                            Terverifikasi & Aktif
                        </span>
                    </div>
                    <p class="text-sm text-slate-500 max-w-2xl leading-relaxed">
                        Akun mitra resmi SportPlace terdaftar di bawah pengelolaan <strong class="text-slate-700">{{ $this->user->name }}</strong>. Seluruh berkas legalitas dan rekening pencairan telah disetujui.
                    </p>
                    <div class="flex items-center gap-4 mt-3 text-xs text-slate-400">
                        <span>Bentuk Usaha: <strong class="text-slate-600">{{ $this->profile->bentuk_badan_usaha }}</strong></span>
                        <span>&bull;</span>
                        <span>Kota: <strong class="text-slate-600">{{ $this->profile->kota_operasional }}</strong></span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <button
                    type="button"
                    wire:click="openEditModal"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-semibold shadow-xs transition duration-150 whitespace-nowrap min-h-[42px] cursor-pointer"
                >
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    <span>Ubah Data Usaha</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Grid Informasi Profil & Rekening -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Card 1: Data Identitas Pemilik & Akun -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Identitas Pemilik & Akun</h3>
                    </div>
                    <span class="text-xs font-semibold text-slate-400">ID Mitra: #{{ $this->profile->id }}</span>
                </div>

                <div class="space-y-4 text-xs sm:text-sm">
                    <div class="flex items-center justify-between py-2 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">Nama Penanggung Jawab</span>
                        <span class="font-bold text-slate-800">{{ $this->user->name }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">Alamat Email</span>
                        <span class="font-semibold text-slate-800">{{ $this->user->email }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">Nomor WhatsApp / HP</span>
                        <span class="font-semibold text-slate-800">{{ $this->user->phone ?: '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">Nomor KTP (NIK)</span>
                        <span class="font-mono font-semibold text-slate-800">{{ $this->profile->maskedValue('nik') ?: $this->profile->no_ktp }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-slate-500 font-medium">NPWP Usaha / Pribadi</span>
                        <span class="font-mono font-semibold text-slate-800">{{ $this->profile->maskedValue('npwp') ?: ($this->profile->npwp ?: 'Tidak ada') }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                <span>Status KTP: <strong class="text-emerald-600 font-semibold">Tervalidasi</strong></span>
                <span>Terdaftar sejak: {{ $this->profile->created_at ? $this->profile->created_at->format('d M Y') : '-' }}</span>
            </div>
        </div>

        <!-- Card 2: Rekening Pencairan Dana & Wallet -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-[#0B7B69] flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Rekening Bank Pencairan Dana</h3>
                    </div>
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-[#0B7B69] bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                        Aktif
                    </span>
                </div>

                <div class="space-y-4 text-xs sm:text-sm">
                    <div class="flex items-center justify-between py-2 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">Bank Tujuan</span>
                        <span class="font-bold text-slate-800">{{ $this->profile->bank_nama }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">Nomor Rekening</span>
                        <span class="font-mono font-bold text-slate-800 text-base tracking-wide">{{ $this->profile->maskedAccountNumber() ?: $this->profile->nomor_rekening }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">Atas Nama Rekening</span>
                        <span class="font-bold text-slate-800 uppercase">{{ $this->profile->nama_rekening }}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600 flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-[#0B7B69] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Pencairan dana sewa lapangan dari saldo aktif Anda akan ditransfer secara langsung ke rekening ini.</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs text-slate-500">Ingin mencairkan saldo pendapatan?</span>
                <a href="{{ url('/mitra/penarikan') }}" wire:navigate class="text-xs font-semibold text-[#0B7B69] hover:underline flex items-center gap-1">
                    <span>Menu Tarik Dana</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

    </div>

    <!-- Section: Berkas Dokumen Legalitas Privat -->
    <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-slate-100 gap-3">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-slate-900">Berkas Dokumen Legalitas Usaha</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Dokumen persyaratan resmi yang telah diverifikasi oleh tim kepatuhan SportPlace.</p>
            </div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span>Penyimpanan Terproteksi</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-6">
            @foreach ($this->documentsList as $doc)
                <div class="p-5 rounded-2xl border border-slate-200/90 bg-slate-50/50 hover:bg-white hover:border-emerald-300 transition duration-150 flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-700 flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">{{ $doc['name'] }}</h4>
                                    <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ $doc['description'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 mt-3 border-t border-slate-200/60 flex items-center justify-between">
                        @if ($doc['path'])
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0B7B69]">
                                <svg class="w-3.5 h-3.5 text-[#0B7B69]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Terverifikasi</span>
                            </span>

                            <a
                                href="{{ $doc['download_url'] }}"
                                target="_blank"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white border border-slate-300 hover:border-[#0B7B69] hover:text-[#0B7B69] text-xs font-semibold text-slate-700 shadow-2xs transition cursor-pointer min-h-[34px]"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                <span>Unduh / Lihat</span>
                            </a>
                        @else
                            <span class="text-xs text-slate-400 italic">Tidak Dilampirkan (Opsional)</span>
                            <span class="text-xs text-slate-400">-</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- MODAL EDIT DATA USAHA -->
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" wire:click="closeEditModal"></div>

            <div class="flex min-h-full items-center justify-center p-4 sm:p-6 text-center">
                <div class="relative w-full max-w-2xl transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all border border-slate-200" @click.stop>
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Perbarui Informasi Usaha & Kontak</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Ubah nama usaha, kota operasional, deskripsi, atau kontak penanggung jawab.</p>
                        </div>
                        <button type="button" wire:click="closeEditModal" class="p-2 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-200/70 transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form wire:submit="updateProfile" class="p-6 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="edit_nama_usaha" value="Nama Usaha Lapangan" required />
                                <input
                                    type="text"
                                    id="edit_nama_usaha"
                                    wire:model="nama_usaha"
                                    class="w-full rounded-full border {{ $errors->has('nama_usaha') ? 'border-rose-400' : 'border-slate-300' }} px-4 py-2 text-sm focus:outline-hidden focus:border-[#0B7B69]"
                                    required
                                >
                                @error('nama_usaha') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-input-label for="edit_bentuk_badan_usaha" value="Bentuk Badan Usaha" required />
                                <select
                                    id="edit_bentuk_badan_usaha"
                                    wire:model="bentuk_badan_usaha"
                                    class="w-full rounded-full border border-slate-300 bg-white px-4 py-2 text-sm focus:outline-hidden focus:border-[#0B7B69]"
                                >
                                    <option value="Perorangan">Perorangan</option>
                                    <option value="CV / Badan Usaha">CV / Badan Usaha</option>
                                    <option value="PT / Perseroan">PT / Perseroan</option>
                                </select>
                                @error('bentuk_badan_usaha') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-input-label for="edit_kota" value="Kota Operasional" required />
                                <input
                                    type="text"
                                    id="edit_kota"
                                    wire:model="kota_operasional"
                                    class="w-full rounded-full border {{ $errors->has('kota_operasional') ? 'border-rose-400' : 'border-slate-300' }} px-4 py-2 text-sm focus:outline-hidden focus:border-[#0B7B69]"
                                    required
                                >
                                @error('kota_operasional') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-input-label for="edit_user_name" value="Nama Penanggung Jawab" required />
                                <input
                                    type="text"
                                    id="edit_user_name"
                                    wire:model="user_name"
                                    class="w-full rounded-full border {{ $errors->has('user_name') ? 'border-rose-400' : 'border-slate-300' }} px-4 py-2 text-sm focus:outline-hidden focus:border-[#0B7B69]"
                                    required
                                >
                                @error('user_name') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <x-input-label for="edit_user_phone" value="Nomor WhatsApp / HP" />
                                <input
                                    type="text"
                                    id="edit_user_phone"
                                    wire:model="user_phone"
                                    class="w-full rounded-full border {{ $errors->has('user_phone') ? 'border-rose-400' : 'border-slate-300' }} px-4 py-2 text-sm focus:outline-hidden focus:border-[#0B7B69]"
                                    placeholder="Contoh: 081234567890"
                                >
                                @error('user_phone') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <x-input-label for="edit_deskripsi" value="Deskripsi Profil Usaha" />
                            <textarea
                                id="edit_deskripsi"
                                wire:model="deskripsi_usaha"
                                rows="3"
                                class="w-full rounded-2xl border border-slate-300 px-4 py-2.5 text-sm focus:outline-hidden focus:border-[#0B7B69]"
                                placeholder="Jelaskan fasilitas olahraga dan profil usaha Anda..."
                            ></textarea>
                            @error('deskripsi_usaha') <p class="mt-1 text-[11px] text-rose-600 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                            <button
                                type="button"
                                wire:click="closeEditModal"
                                class="px-5 py-2.5 rounded-full bg-white border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer min-h-[40px]"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-xs font-semibold text-white shadow-xs transition cursor-pointer min-h-[40px] disabled:opacity-60"
                            >
                                <span wire:loading.remove wire:target="updateProfile">Simpan Perubahan</span>
                                <span wire:loading wire:target="updateProfile">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

</div>
