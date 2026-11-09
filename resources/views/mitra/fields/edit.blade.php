<x-layouts.mitra :title="'Edit Lapangan — ' . $field->nama_lapangan . ' — SportPlace Mitra'">
    <div class="space-y-6">

        <!-- Penanda Halaman / Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs font-medium text-slate-500" aria-label="Breadcrumb">
            <a href="{{ route('mitra.dashboard') }}" class="hover:text-emerald-700 transition">SportPlace Mitra</a>
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('mitra.venues.index') }}" class="hover:text-emerald-700 transition">Venue & Lapangan</a>
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('mitra.venues.fields.index', $field->venue) }}" class="hover:text-emerald-700 transition">{{ $field->venue?->nama_venue }}</a>
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-slate-800 font-semibold truncate max-w-xs">Edit: {{ $field->nama_lapangan }}</span>
        </nav>

        <!-- Flash Alert Success / Error -->
        @if (session('success'))
            <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 sm:p-5 flex items-center justify-between text-emerald-800 text-sm shadow-xs animate-in fade-in">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 cursor-pointer p-1" aria-label="Tutup notifikasi">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl bg-rose-50 border border-rose-200 p-4 sm:p-5 flex items-center justify-between text-rose-800 text-sm shadow-xs animate-in fade-in">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 cursor-pointer p-1" aria-label="Tutup notifikasi">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        <!-- Banner Catatan Penolakan Khusus jika Lapangan Ditolak (T5.5 / FR-FIELD-003) -->
        @if ($field->status_lapangan === 'rejected')
            <div class="rounded-3xl bg-rose-50 border-2 border-rose-200 p-6 sm:p-7 shadow-xs">
                <div class="flex items-start gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full bg-rose-600 text-white text-[11px] font-bold uppercase tracking-wider">
                                Revisi Diperlukan
                            </span>
                            <span class="text-xs text-rose-600 font-medium">
                                {{ $field->reviewed_at ? $field->reviewed_at->translatedFormat('d F Y, H:i') : '' }}
                            </span>
                        </div>
                        <h2 class="text-base font-bold text-rose-900 mt-1">Alasan Penolakan Lapangan oleh Admin:</h2>
                        <div class="mt-2 p-4 rounded-2xl bg-white/80 border border-rose-200 text-sm text-rose-800 leading-relaxed font-medium">
                            {{ $field->alasan_penolakan ?: 'Terdapat foto atau spesifikasi fasilitas yang belum memenuhi standar minimum SportPlace. Harap perbarui data dan unggah foto yang jelas.' }}
                        </div>
                        <p class="text-xs text-rose-700 mt-2">
                            Silakan sesuaikan data dan foto yang diminta di bawah, lalu klik tombol <strong class="text-rose-900">"Ajukan Peninjauan Kembali"</strong>.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Header Halaman -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 text-xs font-bold tracking-wider text-emerald-800 uppercase mb-1">
                    <span class="w-2 h-2 rounded-full bg-[#0B7B69]"></span>
                    <span>Status Saat Ini: {{ ucfirst($field->status_lapangan) }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Edit Unit Lapangan</h1>
                <p class="text-sm text-slate-500 mt-1">Perbarui informasi fisik, foto, fasilitas pendukung, atau tarif sewa.</p>
            </div>

            <a href="{{ route('mitra.venues.fields.index', $field->venue) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold transition cursor-pointer min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali ke Lapangan</span>
            </a>
        </div>

        <!-- Formulir Edit Lapangan (2-Column Grid Sesuai Mockup Figma) -->
        <form id="field-form" method="POST" action="{{ route('mitra.fields.update', $field) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Kolom Kiri: Formulir Input (8 Kolom) -->
                <div class="lg:col-span-8 space-y-6">

                    <!-- Bagian 1: Informasi Dasar Lapangan -->
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-xs space-y-5">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                            <div class="w-9 h-9 rounded-2xl bg-emerald-50 text-[#0B7B69] flex items-center justify-center font-bold text-sm">
                                1
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Informasi Fisik Lapangan</h2>
                                <p class="text-xs text-slate-500">Tentukan nama, jenis olahraga, dimensi, dan permukaan lapangan.</p>
                            </div>
                        </div>

                        <!-- Venue Induk (Read Only) -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Venue Induk</label>
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-slate-900">{{ $field->venue?->nama_venue }}</div>
                                        <div class="text-xs text-slate-500">{{ $field->venue?->alamat_lengkap }}, {{ $field->venue?->city?->nama_kota }}</div>
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-full text-[11px] font-semibold {{ $field->venue?->status_vanue === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    Venue {{ ucfirst($field->venue?->status_vanue) }}
                                </span>
                            </div>
                        </div>

                        <!-- Nama Lapangan & Cabang Olahraga -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="nama_lapangan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nama Lapangan <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="nama_lapangan" name="nama_lapangan" value="{{ old('nama_lapangan', $field->nama_lapangan) }}" placeholder="Contoh: Lapangan Futsal 1 (Vinyl Pro)" required class="w-full px-4 py-2.5 text-sm rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#0B7B69] focus:border-transparent min-h-[44px]">
                            </div>

                            <div>
                                <label for="sport_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Cabang Olahraga <span class="text-rose-500">*</span>
                                </label>
                                <select id="sport_id" name="sport_id" required class="w-full px-4 py-2.5 text-sm rounded-2xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-[#0B7B69] focus:border-transparent min-h-[44px] cursor-pointer">
                                    <option value="">-- Pilih Cabang Olahraga --</option>
                                    @foreach ($sports as $sport)
                                        <option value="{{ $sport->id }}" {{ old('sport_id', $field->sport_id) == $sport->id ? 'selected' : '' }} data-sport-name="{{ $sport->nama_olahraga }}">
                                            {{ $sport->nama_olahraga }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Jenis Permukaan & Tarif Sewa per Jam -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="jenis_permukaan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Jenis Permukaan Lantai <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="jenis_permukaan" name="jenis_permukaan" value="{{ old('jenis_permukaan', $field->jenis_permukaan) }}" placeholder="Contoh: Rumput Sintetis / Vinyl Taraflex" list="permukaan-options" required class="w-full px-4 py-2.5 text-sm rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#0B7B69] focus:border-transparent min-h-[44px]">
                                <datalist id="permukaan-options">
                                    <option value="Rumput Sintetis">
                                    <option value="Vinyl Taraflex">
                                    <option value="Karpet Interlock PP">
                                    <option value="Parket Kayu / Parquet">
                                    <option value="Semen Halus / Hard Court">
                                    <option value="Karet Fleksibel / Rubber">
                                    <option value="Karpet Karpet Badminton Standar BWF">
                                    </datalist>
                            </div>

                            <div>
                                <label for="harga_perjam" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Tarif Sewa Reguler / Jam <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-sm">
                                        Rp
                                    </div>
                                    <input type="number" id="harga_perjam" name="harga_perjam" value="{{ old('harga_perjam', (int)$field->harga_perjam) }}" placeholder="150000" min="1000" step="5000" required class="w-full pl-11 pr-4 py-2.5 text-sm rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#0B7B69] focus:border-transparent min-h-[44px]">
                                </div>
                                <span class="text-[11px] text-slate-500 mt-1 block">
                                    Perubahan harga tidak akan memengaruhi pesanan terdahulu yang sudah terbit e-tiketnya (T5.5).
                                </span>
                            </div>
                        </div>

                        <!-- Dimensi (Panjang x Lebar) & Kapasitas Pemain -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="panjang" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                                    Panjang (Meter)
                                </label>
                                <input type="number" id="panjang" name="panjang" value="{{ old('panjang', $field->panjang) }}" placeholder="Contoh: 25" min="1" max="200" step="0.1" class="w-full px-4 py-2.5 text-sm rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#0B7B69] focus:border-transparent min-h-[44px]">
                            </div>

                            <div>
                                <label for="lebar" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                                    Lebar (Meter)
                                </label>
                                <input type="number" id="lebar" name="lebar" value="{{ old('lebar', $field->lebar) }}" placeholder="Contoh: 15" min="1" max="200" step="0.1" class="w-full px-4 py-2.5 text-sm rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#0B7B69] focus:border-transparent min-h-[44px]">
                            </div>

                            <div>
                                <label for="kapasitas" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                                    Kapasitas Pemain
                                </label>
                                <input type="text" id="kapasitas" name="kapasitas" value="{{ old('kapasitas', $field->kapasitas) }}" placeholder="Contoh: 10 - 12 Orang" class="w-full px-4 py-2.5 text-sm rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#0B7B69] focus:border-transparent min-h-[44px]">
                            </div>
                        </div>

                        <!-- Tipe Bangunan / Sirkulasi Udara -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Tipe Bangunan / Sirkulasi Udara <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <!-- Indoor -->
                                <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition select-none hover:bg-slate-50 border-slate-200 has-checked:border-[#0B7B69] has-checked:bg-emerald-50/40">
                                    <input type="radio" name="tipe_bangunan" value="indoor" {{ old('tipe_bangunan', $field->tipe_bangunan) === 'indoor' ? 'checked' : '' }} class="sr-only">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-sm font-bold text-slate-900">Indoor</span>
                                        <div class="w-4 h-4 rounded-full border-2 border-slate-300 flex items-center justify-center checkmark-circle">
                                            <div class="w-2 h-2 rounded-full bg-[#0B7B69] hidden checkmark-dot"></div>
                                        </div>
                                    </div>
                                    <span class="text-xs text-slate-500 leading-relaxed">Tertutup terlindung cuaca dengan ventilasi/exhaust fan.</span>
                                </label>

                                <!-- Semi-Outdoor -->
                                <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition select-none hover:bg-slate-50 border-slate-200 has-checked:border-[#0B7B69] has-checked:bg-emerald-50/40">
                                    <input type="radio" name="tipe_bangunan" value="semi_outdoor" {{ old('tipe_bangunan', $field->tipe_bangunan) === 'semi_outdoor' ? 'checked' : '' }} class="sr-only">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-sm font-bold text-slate-900">Semi-Outdoor</span>
                                        <div class="w-4 h-4 rounded-full border-2 border-slate-300 flex items-center justify-center checkmark-circle">
                                            <div class="w-2 h-2 rounded-full bg-[#0B7B69] hidden checkmark-dot"></div>
                                        </div>
                                    </div>
                                    <span class="text-xs text-slate-500 leading-relaxed">Beratap dengan sisi terbuka untuk sirkulasi udara alami.</span>
                                </label>

                                <!-- Outdoor -->
                                <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition select-none hover:bg-slate-50 border-slate-200 has-checked:border-[#0B7B69] has-checked:bg-emerald-50/40">
                                    <input type="radio" name="tipe_bangunan" value="outdoor" {{ old('tipe_bangunan', $field->tipe_bangunan) === 'outdoor' ? 'checked' : '' }} class="sr-only">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-sm font-bold text-slate-900">Outdoor</span>
                                        <div class="w-4 h-4 rounded-full border-2 border-slate-300 flex items-center justify-center checkmark-circle">
                                            <div class="w-2 h-2 rounded-full bg-[#0B7B69] hidden checkmark-dot"></div>
                                        </div>
                                    </div>
                                    <span class="text-xs text-slate-500 leading-relaxed">Terbuka di bawah sinar matahari dengan pencahayaan malam.</span>
                                </label>
                            </div>
                        </div>

                        <!-- Deskripsi Singkat -->
                        <div>
                            <label for="deskripsi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Deskripsi Tambahan Lapangan
                            </label>
                            <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Jelaskan keunggulan lapangan..." class="w-full px-4 py-2.5 text-sm rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#0B7B69] focus:border-transparent">{{ old('deskripsi', $field->deskripsi) }}</textarea>
                        </div>
                    </div>

                    <!-- Bagian 2: Kelola Foto Fasilitas Lapangan (T5.3 & T5.5) -->
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-xs space-y-5">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                            <div class="w-9 h-9 rounded-2xl bg-emerald-50 text-[#0B7B69] flex items-center justify-center font-bold text-sm">
                                2
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Foto Fasilitas Lapangan</h2>
                                <p class="text-xs text-slate-500">Kelola foto yang sudah ada atau unggah foto baru (maks 8 foto total).</p>
                            </div>
                        </div>

                        <!-- Foto Saat Ini -->
                        @if ($field->photos->count() > 0)
                            <div>
                                <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Foto Terunggah</span>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
                                    @foreach ($field->photos as $photo)
                                        <div id="photo-box-{{ $photo->id }}" class="relative group rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 aspect-video">
                                            <img src="{{ str_starts_with($photo->foto_url, 'http') ? $photo->foto_url : asset('storage/' . $photo->foto_url) }}" alt="Foto Lapangan" class="w-full h-full object-cover">
                                            
                                            <!-- Overlay Aksi -->
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2 p-1">
                                                <button type="button" onclick="setPrimaryExisting({{ $photo->id }})" class="px-2.5 py-1 rounded-full bg-white text-slate-800 text-[10px] font-bold shadow hover:bg-emerald-50">
                                                    Utama
                                                </button>
                                                <button type="button" onclick="markPhotoForDeletion({{ $photo->id }})" class="p-1.5 rounded-full bg-rose-600 text-white hover:bg-rose-700 shadow" title="Hapus Foto">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </div>

                                            <span id="badge-existing-primary-{{ $photo->id }}" class="absolute top-1.5 left-1.5 px-2 py-0.5 rounded-full text-[9px] font-bold {{ $photo->is_primary ? 'bg-emerald-600 text-white' : 'hidden' }}">
                                                Cover Utama
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <input type="hidden" name="primary_photo_id" id="primary-photo-id" value="{{ $field->photos->firstWhere('is_primary', true)?->id ?? $field->photos->first()?->id }}">
                        <div id="deleted-photos-container"></div>

                        <!-- Dropzone Tambah Foto Baru -->
                        <div class="relative border-2 border-dashed border-slate-200 hover:border-[#0B7B69] rounded-3xl p-8 text-center transition bg-slate-50/50 hover:bg-emerald-50/20 cursor-pointer" onclick="document.getElementById('photos-input').click()">
                            <input type="file" id="photos-input" name="photos[]" multiple accept="image/jpeg,image/png,image/webp" class="sr-only" onchange="handleNewPhotoSelection(event)">
                            <div class="w-14 h-14 rounded-full bg-emerald-50 text-[#0B7B69] flex items-center justify-center mx-auto mb-3 shadow-xs">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <div class="text-sm font-bold text-slate-800">
                                Tambah Foto Fasilitas Lainnya
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                Format JPG, PNG, atau WebP. Sisa kuota foto dapat ditambahkan di sini.
                            </p>
                        </div>

                        <!-- Preview Foto Baru yang Dipilih -->
                        <div id="new-photos-preview-container" class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 hidden">
                            <!-- Diisi oleh JS -->
                        </div>
                    </div>

                    <!-- Bagian 3: Fasilitas Penunjang Lapangan -->
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-xs space-y-5">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                            <div class="w-9 h-9 rounded-2xl bg-emerald-50 text-[#0B7B69] flex items-center justify-center font-bold text-sm">
                                3
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Fasilitas Penunjang Lapangan</h2>
                                <p class="text-xs text-slate-500">Pilih fasilitas yang tersedia khusus untuk unit lapangan ini.</p>
                            </div>
                        </div>

                        @php
                            $currentFacilityIds = old('facility_ids', $field->facilities->pluck('id')->toArray());
                        @endphp

                        @if ($facilities->isEmpty())
                            <p class="text-xs text-slate-400 italic">Belum ada data fasilitas master terdaftar.</p>
                        @else
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach ($facilities as $facility)
                                    <label class="flex items-center gap-3 p-3.5 rounded-2xl border border-slate-200 hover:bg-slate-50 cursor-pointer select-none transition has-checked:border-[#0B7B69] has-checked:bg-emerald-50/40">
                                        <input type="checkbox" name="facility_ids[]" value="{{ $facility->id }}" class="rounded text-[#0B7B69] focus:ring-[#0B7B69] w-4 h-4 border-slate-300 facility-checkbox" {{ in_array($facility->id, $currentFacilityIds) ? 'checked' : '' }}>
                                        <div class="text-xs font-semibold text-slate-800 truncate">
                                            {{ $facility->nama }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Bagian 4: Peraturan & Tata Tertib E-Tiket -->
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-xs space-y-5">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                            <div class="w-9 h-9 rounded-2xl bg-emerald-50 text-[#0B7B69] flex items-center justify-center font-bold text-sm">
                                4
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Peraturan & Tata Tertib Pemakaian</h2>
                                <p class="text-xs text-slate-500">Aturan ini akan dicantumkan secara otomatis pada e-tiket pelanggan.</p>
                            </div>
                        </div>

                        <div>
                            <textarea id="aturan_lapangan" name="aturan_lapangan" rows="4" placeholder="Tata tertib pemakaian..." class="w-full px-4 py-3 text-sm rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#0B7B69] focus:border-transparent font-mono text-xs">{{ old('aturan_lapangan', $field->aturan_lapangan) }}</textarea>
                        </div>
                    </div>

                    <!-- Tombol Aksi Formulir -->
                    <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-3">
                        <a href="{{ route('mitra.venues.fields.index', $field->venue) }}" class="w-full sm:w-auto px-6 py-2.5 rounded-full border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold transition cursor-pointer min-h-[44px] text-center">
                            Batal
                        </a>
                        
                        <button type="submit" name="action" value="save" class="w-full sm:w-auto px-6 py-2.5 rounded-full border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold transition cursor-pointer min-h-[44px]">
                            Simpan Perubahan
                        </button>

                        @if (in_array($field->status_lapangan, ['draft', 'rejected'], true))
                            <button type="submit" name="action" value="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-sm font-semibold shadow-sm hover:shadow transition duration-150 cursor-pointer min-h-[44px]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                <span>{{ $field->status_lapangan === 'rejected' ? 'Perbaiki & Ajukan Ulang' : 'Ajukan Verifikasi' }}</span>
                            </button>
                        @endif
                    </div>

                </div>

                <!-- Kolom Kanan: Pratinjau Kartu & Info Status (4 Kolom) -->
                <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-6">

                    <!-- Kartu Pratinjau -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pratinjau Kartu</h3>
                            <span class="text-[10px] text-emerald-700 font-semibold bg-emerald-50 px-2.5 py-1 rounded-full">Tampilan Pelanggan</span>
                        </div>

                        <!-- Card Mockup -->
                        <div class="rounded-2xl border border-slate-200 overflow-hidden shadow-xs bg-white">
                            <div class="relative h-36 bg-slate-100 overflow-hidden">
                                <img id="preview-card-img" src="{{ $field->thumbnail_url }}" alt="Pratinjau Lapangan" class="w-full h-full object-cover">
                                <span id="preview-sport-badge" class="absolute top-2.5 left-2.5 px-3 py-1 rounded-full bg-black/60 backdrop-blur-xs text-white text-[10px] font-bold">
                                    {{ $field->sport?->nama_olahraga ?? 'Olahraga' }}
                                </span>
                                <span id="preview-type-badge" class="absolute top-2.5 right-2.5 px-2.5 py-1 rounded-full bg-white/90 text-slate-800 text-[10px] font-semibold capitalize">
                                    {{ str_replace('_', ' ', $field->tipe_bangunan ?? 'Indoor') }}
                                </span>
                            </div>
                            <div class="p-4 space-y-2">
                                <div>
                                    <h4 id="preview-name" class="font-bold text-slate-900 text-sm truncate">
                                        {{ $field->nama_lapangan }}
                                    </h4>
                                    <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5 truncate">
                                        <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span>{{ $field->venue?->nama_venue }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
                                    <span id="preview-surface" class="text-slate-500 text-[11px] truncate">
                                        {{ $field->jenis_permukaan ?? 'Permukaan Standar' }}
                                    </span>
                                    <div class="font-bold text-[#0B7B69] shrink-0">
                                        <span id="preview-price">{{ $field->formatted_harga }}</span>
                                        <span class="text-[10px] font-normal text-slate-400">/ jam</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Info Card -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs space-y-3 text-xs text-slate-600">
                        <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Status Lapangan</h3>
                        <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                            <span class="font-semibold text-slate-700">Status Moderasi</span>
                            <span class="font-bold capitalize {{ $field->status_lapangan === 'aktif' ? 'text-emerald-700' : ($field->status_lapangan === 'rejected' ? 'text-rose-700' : 'text-slate-700') }}">
                                {{ $field->status_lapangan }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            @if ($field->status_lapangan === 'aktif')
                                Lapangan aktif disewakan. Perubahan data di sini akan langsung diperbarui tanpa moderasi ulang (OQ-011).
                            @elseif ($field->status_lapangan === 'pending_review')
                                Lapangan sedang ditinjau oleh Admin. Anda tetap dapat memperbarui informasi sebelum keputusan akhir dibuat.
                            @elseif ($field->status_lapangan === 'rejected')
                                Lapangan membutuhkan revisi. Harap sesuaikan poin penolakan di atas lalu ajukan kembali.
                            @else
                                Lapangan berstatus draft dan belum dipublikasikan kepada pelanggan.
                            @endif
                        </p>
                    </div>

                </div>

            </div>
        </form>

    </div>

    <!-- Script Interaksi Edit -->
    <script>
        function setPrimaryExisting(photoId) {
            document.getElementById('primary-photo-id').value = photoId;
            document.querySelectorAll('[id^="badge-existing-primary-"]').forEach(b => b.classList.add('hidden'));
            const targetBadge = document.getElementById('badge-existing-primary-' + photoId);
            if (targetBadge) {
                targetBadge.classList.remove('hidden');
            }
        }

        function markPhotoForDeletion(photoId) {
            const photoBox = document.getElementById('photo-box-' + photoId);
            if (photoBox) {
                photoBox.style.opacity = '0.3';
                photoBox.style.filter = 'grayscale(100%)';
            }

            const container = document.getElementById('deleted-photos-container');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'deleted_photos[]';
            input.value = photoId;
            container.appendChild(input);
        }

        function handleNewPhotoSelection(event) {
            const files = Array.from(event.target.files);
            const container = document.getElementById('new-photos-preview-container');
            
            if (files.length === 0) return;

            container.innerHTML = '';
            container.classList.remove('hidden');

            files.forEach((file) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 aspect-video';
                    div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                    container.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }

        // Live Preview Bindings
        const inputNama = document.getElementById('nama_lapangan');
        const selectSport = document.getElementById('sport_id');
        const inputHarga = document.getElementById('harga_perjam');
        const inputPermukaan = document.getElementById('jenis_permukaan');
        const radioBangunan = document.querySelectorAll('input[name="tipe_bangunan"]');

        inputNama.addEventListener('input', () => {
            document.getElementById('preview-name').textContent = inputNama.value || 'Nama Lapangan';
        });

        selectSport.addEventListener('change', () => {
            const selectedOpt = selectSport.options[selectSport.selectedIndex];
            document.getElementById('preview-sport-badge').textContent = selectedOpt ? selectedOpt.text : 'Olahraga';
        });

        inputHarga.addEventListener('input', () => {
            const val = parseInt(inputHarga.value) || 0;
            document.getElementById('preview-price').textContent = 'Rp ' + val.toLocaleString('id-ID');
        });

        inputPermukaan.addEventListener('input', () => {
            document.getElementById('preview-surface').textContent = inputPermukaan.value || 'Permukaan Standar';
        });

        radioBangunan.forEach(r => {
            r.addEventListener('change', () => {
                const checked = document.querySelector('input[name="tipe_bangunan"]:checked');
                if (checked) {
                    document.getElementById('preview-type-badge').textContent = checked.value.replace('_', ' ');
                }
            });
        });
    </script>
</x-layouts.mitra>

