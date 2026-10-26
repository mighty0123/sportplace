<x-layouts.mitra title="Tambah Venue Baru — SportPlace Mitra">
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Breadcrumb & Back Link -->
        <div class="flex items-center justify-between">
            <nav class="flex items-center gap-2 text-xs font-medium text-slate-500" aria-label="Breadcrumb">
                <a href="{{ route('mitra.dashboard') }}" class="hover:text-emerald-700 transition">SportPlace Mitra</a>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('mitra.venues.index') }}" class="hover:text-emerald-700 transition">Vanue & Lapangan</a>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-slate-800 font-semibold">Tambah Vanue</span>
            </nav>

            <a href="{{ route('mitra.venues.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-emerald-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali</span>
            </a>
        </div>

        <!-- Page Header -->
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Tambah Vanue Baru</h1>
            <p class="text-sm text-slate-500 mt-1">Lengkapi informasi dasar lokasi venue untuk mengelola lapangan olahraga.</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 sm:p-8">
            <form method="POST" action="{{ route('mitra.venues.store') }}" class="space-y-6">
                @csrf

                <!-- Section 1: Informasi Utama -->
                <div class="space-y-4">
                    <h2 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#0B7B69]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>Informasi Lokasi & Nama Venue</span>
                    </h2>

                    <!-- Nama Venue -->
                    <div>
                        <label for="nama_venue" class="block text-sm font-semibold text-slate-700 mb-1">
                            Nama Venue <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="nama_venue" name="nama_venue" value="{{ old('nama_venue') }}" placeholder="Contoh: GOR Tirta Sport Denpasar" required maxlength="200" class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('nama_venue') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-200' }} text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-[#0B7B69] transition min-h-[44px]">
                        @error('nama_venue')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kota/Wilayah -->
                    <div>
                        <label for="city_id" class="block text-sm font-semibold text-slate-700 mb-1">
                            Kota / Wilayah Operasional <span class="text-rose-500">*</span>
                        </label>
                        <select id="city_id" name="city_id" required class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('city_id') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-200' }} text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-[#0B7B69] transition bg-white min-h-[44px] cursor-pointer">
                            <option value="">Pilih Kota / Wilayah...</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                    {{ $city->nama_kota }} ({{ $city->provinsi }})
                                </option>
                            @endforeach
                        </select>
                        @error('city_id')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Alamat Lengkap -->
                    <div>
                        <label for="alamat_lengkap" class="block text-sm font-semibold text-slate-700 mb-1">
                            Alamat Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" required placeholder="Tuliskan nama jalan, nomor bangunan, RT/RW, kelurahan, dan kecamatan secara detail..." class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('alamat_lengkap') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-200' }} text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-[#0B7B69] transition leading-relaxed">{{ old('alamat_lengkap') }}</textarea>
                        @error('alamat_lengkap')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Map URL -->
                    <div>
                        <label for="map_url" class="block text-sm font-semibold text-slate-700 mb-1">
                            Tautan Google Maps <span class="text-xs font-normal text-slate-400">(Opsional)</span>
                        </label>
                        <input type="url" id="map_url" name="map_url" value="{{ old('map_url') }}" placeholder="https://maps.google.com/?q=..." maxlength="255" class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('map_url') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-200' }} text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-[#0B7B69] transition min-h-[44px]">
                        <p class="mt-1 text-[11px] text-slate-400">Salin tautan bagikan lokasi dari aplikasi Google Maps untuk memudahkan calon penyewa.</p>
                        @error('map_url')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Section 2: Jam Operasional -->
                <div class="space-y-4 pt-4">
                    <h2 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#0B7B69]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Jam Operasional Venue</span>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Jam Buka -->
                        <div>
                            <label for="jam_buka" class="block text-sm font-semibold text-slate-700 mb-1">
                                Jam Buka <span class="text-rose-500">*</span>
                            </label>
                            <input type="time" id="jam_buka" name="jam_buka" value="{{ old('jam_buka', '08:00') }}" required class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('jam_buka') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-200' }} text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-[#0B7B69] transition min-h-[44px]">
                            @error('jam_buka')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jam Tutup -->
                        <div>
                            <label for="jam_tutup" class="block text-sm font-semibold text-slate-700 mb-1">
                                Jam Tutup <span class="text-rose-500">*</span>
                            </label>
                            <input type="time" id="jam_tutup" name="jam_tutup" value="{{ old('jam_tutup', '23:00') }}" required class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('jam_tutup') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-200' }} text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-[#0B7B69] transition min-h-[44px]">
                            @error('jam_tutup')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <p class="text-xs text-slate-500">Jam operasional ini menjadi batasan waktu aktif bagi seluruh lapangan olahraga di venue ini.</p>
                </div>

                <!-- Section 3: Deskripsi Tambahan (GAP-17) -->
                <div class="space-y-4 pt-4">
                    <h2 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#0B7B69]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Deskripsi Venue (Opsional)</span>
                    </h2>

                    <div>
                        <label for="deskripsi" class="block text-sm font-semibold text-slate-700 mb-1">
                            Deskripsi & Fasilitas Umum
                        </label>
                        <textarea id="deskripsi" name="deskripsi" rows="3" maxlength="1000" placeholder="Ceritakan keunggulan venue Anda, fasilitas umum seperti musholla, parkir luas, kantin, aksesibilitas..." class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('deskripsi') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-200' }} text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-[#0B7B69] transition leading-relaxed">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <a href="{{ route('mitra.venues.index') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 text-sm font-medium transition text-center min-h-[44px] flex items-center justify-center cursor-pointer">
                        Batal
                    </a>

                    <div class="w-full sm:w-auto flex flex-col sm:flex-row items-center gap-3">
                        <button type="submit" name="action" value="draft" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition min-h-[44px] cursor-pointer">
                            Simpan sebagai Draft
                        </button>
                        <button type="submit" name="action" value="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-[#0B7B69] hover:bg-[#086354] text-white text-sm font-semibold shadow-sm hover:shadow transition min-h-[44px] cursor-pointer flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            <span>Simpan & Ajukan</span>
                        </button>
                    </div>
                </div>

            </form>
        </div>

    </div>
</x-layouts.mitra>

