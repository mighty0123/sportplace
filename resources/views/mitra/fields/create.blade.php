<x-layouts.mitra title="Tambah Lapangan — SportPlace Mitra">
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
            <a href="{{ route('mitra.venues.fields.index', $venue) }}" class="hover:text-emerald-700 transition">{{ $venue->nama_venue }}</a>
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-slate-800 font-semibold">Tambah Lapangan</span>
        </nav>

        <!-- Flash Alert Error -->
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

        @if ($errors->any())
            <div class="rounded-2xl bg-rose-50 border border-rose-200 p-4 sm:p-5 text-rose-800 text-sm shadow-xs">
                <div class="flex items-center gap-2 font-bold mb-2">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Mohon lengkapi formulir dengan benar:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Header Halaman -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 text-xs font-bold tracking-wider text-emerald-800 uppercase mb-1">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Unit Lapangan Baru</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Tambah Unit Lapangan</h1>
                <p class="text-sm text-slate-500 mt-1">Lengkapi spesifikasi fisik, foto fasilitas, tarif sewa, dan tata tertib lapangan.</p>
            </div>

            <a href="{{ route('mitra.venues.fields.index', $venue) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold transition cursor-pointer min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali ke Lapangan</span>
            </a>
        </div>

        <!-- Formulir Tambah Lapangan (2-Column Grid Sesuai Mockup Figma) -->
        <form id="field-form" method="POST" action="{{ route('mitra.venues.fields.store', $venue) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="venue_id" value="{{ $venue->id }}">

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
                                        <div class="text-sm font-bold text-slate-900">{{ $venue->nama_venue }}</div>
                                        <div class="text-xs text-slate-500">{{ $venue->alamat_lengkap }}, {{ $venue->city?->nama_kota }}</div>
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-full text-[11px] font-semibold {{ $venue->status_vanue === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    Venue {{ ucfirst($venue->status_vanue) }}
                                </span>
                            </div>
                        </div>

                        <!-- Nama Lapangan & Cabang Olahraga -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="nama_lapangan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nama Lapangan <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="nama_lapangan" name="nama_lapangan" value="{{ old('nama_lapangan') }}" placeholder="Contoh: Lapangan Futsal 1 (Vinyl Pro)" required class="w-full px-4 py-2.5 text-sm rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#0B7B69] focus:border-transparent min-h-[44px]">
                            </div>

                            <div>
                                <label for="sport_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Cabang Olahraga <span class="text-rose-500">*</span>
                                </label>
                                <select id="sport_id" name="sport_id" required class="w-full px-4 py-2.5 text-sm rounded-2xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-[#0B7B69] focus:border-transparent min-h-[44px] cursor-pointer">
                                    <option value="">-- Pilih Cabang Olahraga --</option>
                                    @foreach ($sports as $sport)
                                        <option value="{{ $sport->id }}" {{ old('sport_id') == $sport->id ? 'selected' : '' }} data-sport-name="{{ $sport->nama_olahraga }}">
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
                                <input type="text" id="jenis_permukaan" name="jenis_permukaan" value="{{ old('jenis_permukaan') }}" placeholder="Contoh: Rumput Sintetis / Vinyl Taraflex" list="permukaan-options" required class="w-full px-4 py-2.5 text-sm rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#0B7B69] focus:border-transparent min-h-[44px]">
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
                                    <input type="number" id="harga_perjam" name="harga_perjam" value="{{ old('harga_perjam') }}" placeholder="150000" min="1000" step="5000" required class="w-full pl-11 pr-4 py-2.5 text-sm rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#0B7B69] focus:border-transparent min-h-[44px]">
                                </div>
                            </div>
                        </div>

                        <!-- Dimensi (Panjang x Lebar) & Kapasitas Pemain -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="panjang" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                                    Panjang (Meter)
                                </label>
                                <input type="number" id="panjang" name="panjang" value="{{ old('panjang') }}" placeholder="Contoh: 25" min="1" max="200" step="0.1" class="w-full px-4 py-2.5 text-sm rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#0B7B69] focus:border-transparent min-h-[44px]">
                            </div>

                            <div>
                                <label for="lebar" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                                    Lebar (Meter)
                                </label>
                                <input type="number" id="lebar" name="lebar" value="{{ old('lebar') }}" placeholder="Contoh: 15" min="1" max="200" step="0.1" class="w-full px-4 py-2.5 text-sm rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#0B7B69] focus:border-transparent min-h-[44px]">
                            </div>

                            <div>
                                <label for="kapasitas" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                                    Kapasitas Pemain
                                </label>
                                <input type="text" id="kapasitas" name="kapasitas" value="{{ old('kapasitas') }}" placeholder="Contoh: 10 - 12 Orang" class="w-full px-4 py-2.5 text-sm rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#0B7B69] focus:border-transparent min-h-[44px]">
                            </div>
                        </div>

                        <!-- Tipe Bangunan / Sirkulasi Udara (Radio Cards Sesuai Mockup) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Tipe Bangunan / Sirkulasi Udara <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <!-- Indoor -->
                                <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition select-none hover:bg-slate-50 border-slate-200 has-checked:border-[#0B7B69] has-checked:bg-emerald-50/40">
                                    <input type="radio" name="tipe_bangunan" value="indoor" {{ old('tipe_bangunan', 'indoor') === 'indoor' ? 'checked' : '' }} class="sr-only">
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
                                    <input type="radio" name="tipe_bangunan" value="semi_outdoor" {{ old('tipe_bangunan') === 'semi_outdoor' ? 'checked' : '' }} class="sr-only">
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
                                    <input type="radio" name="tipe_bangunan" value="outdoor" {{ old('tipe_bangunan') === 'outdoor' ? 'checked' : '' }} class="sr-only">
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
                            <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Jelaskan keunggulan lapangan, spesifikasi jaring/gawang, pencahayaan LED lux, dsb..." class="w-full px-4 py-2.5 text-sm rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#0B7B69] focus:border-transparent">{{ old('deskripsi') }}</textarea>
                        </div>
                    </div>

                    <!-- Bagian 2: Unggah Foto Fasilitas Lapangan (T5.3) -->
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-xs space-y-5">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                            <div class="w-9 h-9 rounded-2xl bg-emerald-50 text-[#0B7B69] flex items-center justify-center font-bold text-sm">
                                2
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Foto Fasilitas Lapangan</h2>
                                <p class="text-xs text-slate-500">Unggah foto sudut lapangan, permukaan, dan fasilitas penunjang (maks 8 foto).</p>
                            </div>
                        </div>

                        <!-- Dropzone Upload Area -->
                        <div class="relative border-2 border-dashed border-slate-200 hover:border-[#0B7B69] rounded-3xl p-8 text-center transition bg-slate-50/50 hover:bg-emerald-50/20 cursor-pointer" onclick="document.getElementById('photos-input').click()">
                            <input type="file" id="photos-input" name="photos[]" multiple accept="image/jpeg,image/png,image/webp" class="sr-only" onchange="handlePhotoSelection(event)">
                            <div class="w-14 h-14 rounded-full bg-emerald-50 text-[#0B7B69] flex items-center justify-center mx-auto mb-3 shadow-xs">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="text-sm font-bold text-slate-800">
                                Klik untuk memilih foto atau seret ke sini
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                Format JPG, PNG, atau WebP. Maksimal 8 foto, maks 5 MB per berkas.
                            </p>
                        </div>

                        <!-- Preview Foto Grid -->
                        <div id="photos-preview-container" class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 hidden">
                            <!-- Diisi secara dinamis oleh JavaScript -->
                        </div>
                        <input type="hidden" name="primary_photo_index" id="primary-photo-index" value="0">
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

                        @if ($facilities->isEmpty())
                            <p class="text-xs text-slate-400 italic">Belum ada data fasilitas master terdaftar.</p>
                        @else
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach ($facilities as $facility)
                                    <label class="flex items-center gap-3 p-3.5 rounded-2xl border border-slate-200 hover:bg-slate-50 cursor-pointer select-none transition has-checked:border-[#0B7B69] has-checked:bg-emerald-50/40">
                                        <input type="checkbox" name="facility_ids[]" value="{{ $facility->id }}" class="rounded text-[#0B7B69] focus:ring-[#0B7B69] w-4 h-4 border-slate-300 facility-checkbox" {{ is_array(old('facility_ids')) && in_array($facility->id, old('facility_ids')) ? 'checked' : '' }} onchange="updateProgress()">
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
                            <textarea id="aturan_lapangan" name="aturan_lapangan" rows="4" placeholder="Contoh:&#10;1. Wajib mengenakan sepatu sol karet non-marking.&#10;2. Dilarang merokok dan membawa minuman manis ke dalam area lapangan.&#10;3. Hadir 10 menit sebelum jam reservasi dimulai." class="w-full px-4 py-3 text-sm rounded-2xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#0B7B69] focus:border-transparent font-mono text-xs">{{ old('aturan_lapangan') }}</textarea>
                        </div>
                    </div>

                    <!-- Tombol Aksi Formulir (Draft vs Ajukan) -->
                    <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-3">
                        <button type="submit" name="action" value="draft" class="w-full sm:w-auto px-6 py-2.5 rounded-full border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold transition cursor-pointer min-h-[44px]">
                            Simpan sebagai Draft
                        </button>
                        <button type="submit" name="action" value="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-2.5 rounded-full bg-[#0B7B69] hover:bg-[#086354] text-white text-sm font-semibold shadow-sm hover:shadow transition duration-150 cursor-pointer min-h-[44px]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            <span>Ajukan Verifikasi</span>
                        </button>
                    </div>

                </div>

                <!-- Kolom Kanan: Sidebar Widget & Live Preview (4 Kolom) -->
                <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-6">

                    <!-- Widget Kelengkapan Data -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900">Kelengkapan Formulir</h3>
                            <span id="progress-percentage" class="text-xs font-bold text-[#0B7B69] px-2.5 py-1 rounded-full bg-emerald-50">20%</span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div id="progress-bar-fill" class="bg-[#0B7B69] h-2 rounded-full transition-all duration-300" style="width: 20%;"></div>
                        </div>

                        <!-- Checklist Items -->
                        <div class="space-y-2.5 text-xs text-slate-600 pt-1">
                            <div id="check-info" class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded-full border border-slate-300 flex items-center justify-center text-[10px] text-slate-400">○</span>
                                <span>Nama & Spesifikasi Fisik</span>
                            </div>
                            <div id="check-price" class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded-full border border-slate-300 flex items-center justify-center text-[10px] text-slate-400">○</span>
                                <span>Tarif Sewa Reguler / Jam</span>
                            </div>
                            <div id="check-photo" class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded-full border border-slate-300 flex items-center justify-center text-[10px] text-slate-400">○</span>
                                <span>Minimal 1 Foto Fasilitas</span>
                            </div>
                            <div id="check-facilities" class="flex items-center gap-2">
                                <span class="w-4 h-4 rounded-full border border-slate-300 flex items-center justify-center text-[10px] text-slate-400">○</span>
                                <span>Fasilitas Penunjang</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pratinjau Kartu Pelanggan (Live Preview Card) -->
                    <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pratinjau Kartu</h3>
                            <span class="text-[10px] text-emerald-700 font-semibold bg-emerald-50 px-2.5 py-1 rounded-full">Tampilan Pelanggan</span>
                        </div>

                        <!-- Card Mockup -->
                        <div class="rounded-2xl border border-slate-200 overflow-hidden shadow-xs bg-white">
                            <div class="relative h-36 bg-slate-100 overflow-hidden">
                                <img id="preview-card-img" src="https://images.unsplash.com/photo-1574629810360-7efbbe195018?q=80&w=600&auto=format&fit=crop" alt="Pratinjau Lapangan" class="w-full h-full object-cover">
                                <span id="preview-sport-badge" class="absolute top-2.5 left-2.5 px-3 py-1 rounded-full bg-black/60 backdrop-blur-xs text-white text-[10px] font-bold">
                                    Olahraga
                                </span>
                                <span id="preview-type-badge" class="absolute top-2.5 right-2.5 px-2.5 py-1 rounded-full bg-white/90 text-slate-800 text-[10px] font-semibold capitalize">
                                    Indoor
                                </span>
                            </div>
                            <div class="p-4 space-y-2">
                                <div>
                                    <h4 id="preview-name" class="font-bold text-slate-900 text-sm truncate">
                                        Nama Lapangan
                                    </h4>
                                    <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5 truncate">
                                        <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span>{{ $venue->nama_venue }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
                                    <span id="preview-surface" class="text-slate-500 text-[11px] truncate">
                                        Permukaan Standar
                                    </span>
                                    <div class="font-bold text-[#0B7B69] shrink-0">
                                        <span id="preview-price">Rp 0</span>
                                        <span class="text-[10px] font-normal text-slate-400">/ jam</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Persyaratan Moderasi Admin (OQ-010) -->
                    <div class="rounded-3xl bg-emerald-50/70 border border-emerald-200/80 p-6 space-y-2 text-xs text-emerald-900">
                        <div class="flex items-center gap-2 font-bold text-emerald-950">
                            <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Kebijakan Verifikasi Lapangan</span>
                        </div>
                        <p class="leading-relaxed text-emerald-800">
                            Pengajuan lapangan akan ditinjau oleh Admin dalam <strong>1x24 jam kerja</strong>. Pastikan foto jelas dan mencakup seluruh sudut lapangan.
                        </p>
                        @if ($venue->status_vanue !== 'aktif')
                            <p class="mt-1 text-amber-800 font-semibold bg-amber-50 p-3 rounded-2xl border border-amber-200">
                                Perhatian: Venue ini masih berstatus {{ ucfirst($venue->status_vanue) }}. Lapangan baru dapat diajukan verifikasi setelah venue berstatus Aktif (OQ-010).
                            </p>
                        @endif
                    </div>

                </div>

            </div>
        </form>

    </div>

    <!-- Script Live Preview, Checklist, and Photo Upload Handler -->
    <script>
        const uploadedFiles = [];

        function handlePhotoSelection(event) {
            const files = Array.from(event.target.files);
            const container = document.getElementById('photos-preview-container');
            
            if (files.length === 0) return;

            container.innerHTML = '';
            container.classList.remove('hidden');

            files.forEach((file, index) => {
                if (index >= 8) return; // maks 8 foto

                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative group rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 aspect-video';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-1.5 p-1">
                            <button type="button" onclick="setPrimaryPreview(${index})" class="px-2.5 py-1 rounded-full bg-white text-slate-800 text-[10px] font-bold shadow hover:bg-emerald-50">
                                Jadikan Utama
                            </button>
                        </div>
                        <span id="badge-primary-${index}" class="absolute top-1 left-1 px-2 py-0.5 rounded-full text-[9px] font-bold ${index === 0 ? 'bg-emerald-600 text-white' : 'hidden'}">
                            Utama
                        </span>
                    `;
                    container.appendChild(div);

                    if (index === 0) {
                        document.getElementById('preview-card-img').src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            });

            document.getElementById('primary-photo-index').value = 0;
            updateProgress();
        }

        function setPrimaryPreview(index) {
            document.getElementById('primary-photo-index').value = index;
            document.querySelectorAll('[id^="badge-primary-"]').forEach((badge, idx) => {
                if (idx === index) {
                    badge.classList.remove('hidden');
                    badge.className = 'absolute top-1 left-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-600 text-white';
                } else {
                    badge.classList.add('hidden');
                }
            });

            const files = document.getElementById('photos-input').files;
            if (files[index]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-card-img').src = e.target.result;
                };
                reader.readAsDataURL(files[index]);
            }
        }

        // Live Preview Bindings
        const inputNama = document.getElementById('nama_lapangan');
        const selectSport = document.getElementById('sport_id');
        const inputHarga = document.getElementById('harga_perjam');
        const inputPermukaan = document.getElementById('jenis_permukaan');
        const radioBangunan = document.querySelectorAll('input[name="tipe_bangunan"]');

        inputNama.addEventListener('input', () => {
            document.getElementById('preview-name').textContent = inputNama.value || 'Nama Lapangan';
            updateProgress();
        });

        selectSport.addEventListener('change', () => {
            const selectedOpt = selectSport.options[selectSport.selectedIndex];
            document.getElementById('preview-sport-badge').textContent = selectedOpt ? selectedOpt.text : 'Olahraga';
            updateProgress();
        });

        inputHarga.addEventListener('input', () => {
            const val = parseInt(inputHarga.value) || 0;
            document.getElementById('preview-price').textContent = 'Rp ' + val.toLocaleString('id-ID');
            updateProgress();
        });

        inputPermukaan.addEventListener('input', () => {
            document.getElementById('preview-surface').textContent = inputPermukaan.value || 'Permukaan Standar';
            updateProgress();
        });

        radioBangunan.forEach(r => {
            r.addEventListener('change', () => {
                const checked = document.querySelector('input[name="tipe_bangunan"]:checked');
                if (checked) {
                    document.getElementById('preview-type-badge').textContent = checked.value.replace('_', ' ');
                }
            });
        });

        function updateProgress() {
            let score = 0;
            const hasName = inputNama.value.trim().length > 0;
            const hasSport = selectSport.value.length > 0;
            const hasSurface = inputPermukaan.value.trim().length > 0;
            const hasPrice = (parseInt(inputHarga.value) || 0) >= 1000;
            const hasPhotos = (document.getElementById('photos-input').files?.length || 0) > 0;
            const hasFacilities = document.querySelectorAll('.facility-checkbox:checked').length > 0;

            const checkInfo = document.getElementById('check-info');
            const checkPrice = document.getElementById('check-price');
            const checkPhoto = document.getElementById('check-photo');
            const checkFacilities = document.getElementById('check-facilities');

            const checkIcon = '<svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>';

            if (hasName && hasSport && hasSurface) {
                score += 25;
                checkInfo.children[0].className = 'w-4 h-4 rounded-full bg-emerald-600 text-white flex items-center justify-center';
                checkInfo.children[0].innerHTML = checkIcon;
            } else {
                checkInfo.children[0].className = 'w-4 h-4 rounded-full border border-slate-300 flex items-center justify-center';
                checkInfo.children[0].innerHTML = '';
            }

            if (hasPrice) {
                score += 25;
                checkPrice.children[0].className = 'w-4 h-4 rounded-full bg-emerald-600 text-white flex items-center justify-center';
                checkPrice.children[0].innerHTML = checkIcon;
            } else {
                checkPrice.children[0].className = 'w-4 h-4 rounded-full border border-slate-300 flex items-center justify-center';
                checkPrice.children[0].innerHTML = '';
            }

            if (hasPhotos) {
                score += 25;
                checkPhoto.children[0].className = 'w-4 h-4 rounded-full bg-emerald-600 text-white flex items-center justify-center';
                checkPhoto.children[0].innerHTML = checkIcon;
            } else {
                checkPhoto.children[0].className = 'w-4 h-4 rounded-full border border-slate-300 flex items-center justify-center';
                checkPhoto.children[0].innerHTML = '';
            }

            if (hasFacilities) {
                score += 25;
                checkFacilities.children[0].className = 'w-4 h-4 rounded-full bg-emerald-600 text-white flex items-center justify-center';
                checkFacilities.children[0].innerHTML = checkIcon;
            } else {
                checkFacilities.children[0].className = 'w-4 h-4 rounded-full border border-slate-300 flex items-center justify-center';
                checkFacilities.children[0].innerHTML = '';
            }

            document.getElementById('progress-percentage').textContent = score + '%';
            document.getElementById('progress-bar-fill').style.width = score + '%';
        }

        // Jalankan kalkulasi awal jika ada old input
        updateProgress();
    </script>
</x-layouts.mitra>

