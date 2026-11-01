{{--
    Halaman Profil Penyewa — SportPlace
    Layout  : x-layouts.penyewa (Top navbar-based)
    Design  : Sesuai referensi Figma (Avatar Card, Informasi Pribadi Grid, Preferensi Olahraga, Alamat Utama, Aktivitas Booking Terkini)
--}}
<x-layouts.penyewa title="Profil Saya — SportPlace" active="profil">
<div class="space-y-6">

    {{-- Main Profile 2-Columns Grid on Desktop --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- =========================================================
             LEFT COLUMN (4 Cols): Avatar Card & Quick Contact
        ========================================================= --}}
        <div class="lg:col-span-4 sticky top-24">
            <x-penyewa.profile.avatar-card
                :name="$profile['name']"
                :member-since="$profile['member_since']"
                :status="$profile['status']"
                :email="$profile['email']"
                :phone="$profile['phone']"
                :avatar="$profile['avatar']"
                :initials="$profile['initials']"
            />
        </div>

        {{-- =========================================================
             RIGHT COLUMN (8 Cols): Personal Info, Sports, Address, Recent Activity
        ========================================================= --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- 1. INFORMASI PRIBADI CARD --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-xs">
                <!-- Header -->
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 leading-snug">
                            Informasi Pribadi
                        </h2>
                        <p class="text-xs text-slate-500 mt-1">
                            Kelola identitas, kontak langsung, dan preferensi akun Anda.
                        </p>
                    </div>

                    <button
                        type="button"
                        onclick="document.getElementById('edit-profile-modal').classList.remove('hidden')"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0B7B69] hover:text-[#086354] hover:underline transition shrink-0 cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Ubah Data</span>
                    </button>
                </div>

                <!-- 8-Field 2-Columns Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4 mt-5">
                    <x-penyewa.profile.info-field
                        label="NAMA LENGKAP"
                        :value="$profile['name']"
                    />
                    <x-penyewa.profile.info-field
                        label="NAMA PANGGILAN"
                        :value="$profile['nickname']"
                    />
                    <x-penyewa.profile.info-field
                        label="ALAMAT EMAIL"
                        :value="$profile['email']"
                    />
                    <x-penyewa.profile.info-field
                        label="NOMOR WHATSAPP / PONSEL"
                        :value="$profile['phone']"
                    />
                    <x-penyewa.profile.info-field
                        label="JENIS KELAMIN"
                        :value="$profile['gender']"
                    />
                    <x-penyewa.profile.info-field
                        label="TANGGAL LAHIR"
                        :value="$profile['birthdate']"
                    />
                    <x-penyewa.profile.info-field
                        label="PEKERJAAN / BIDANG"
                        :value="$profile['occupation']"
                    />
                    <x-penyewa.profile.info-field
                        label="KOTA / DOMISILI UTAMA"
                        :value="$profile['city']"
                    />
                </div>
            </div>

            {{-- 2. PREFERENSI OLAHRAGA FAVORIT --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-xs">
                <!-- Header -->
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-base">🎾</span>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">
                            Preferensi Olahraga Favorit
                        </h3>
                    </div>
                    <span class="text-xs text-slate-400 font-medium">
                        {{ count($sportsPreferences) }} Kategori Aktif
                    </span>
                </div>

                <!-- Badges -->
                <div class="flex flex-wrap items-center gap-2.5">
                    @foreach ($sportsPreferences as $sport)
                        <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-emerald-50/70 text-emerald-800 border border-emerald-200/80 shadow-2xs">
                            <span>{{ $sport['icon'] }}</span>
                            <span>{{ $sport['name'] }}</span>
                            @if (!empty($sport['spec']))
                                <span class="text-[11px] font-normal text-emerald-600/90">({{ $sport['spec'] }})</span>
                            @endif
                        </div>
                    @endforeach

                    <!-- Add Button -->
                    <button
                        type="button"
                        class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-full text-xs font-semibold border border-dashed border-slate-300 hover:border-emerald-500 hover:text-[#0B7B69] text-slate-500 transition cursor-pointer"
                    >
                        <span>+ Tambah</span>
                    </button>
                </div>
            </div>

            {{-- 3. ALAMAT UTAMA --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 p-4 rounded-xl bg-slate-50/80 border border-slate-100">
                    <div class="flex items-start gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-white border border-slate-200/80 text-[#0B7B69] flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-bold text-slate-900">
                                    {{ $primaryAddress['title'] }}
                                </h4>
                                @if (!empty($primaryAddress['is_primary']))
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wide bg-[#FACC15] text-slate-900 shadow-2xs">
                                        UTAMA
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                {{ $primaryAddress['address'] }}
                            </p>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="text-xs font-semibold text-[#0B7B69] hover:text-[#086354] hover:underline transition shrink-0 self-end sm:self-auto cursor-pointer"
                    >
                        Ganti Alamat
                    </button>
                </div>
            </div>

            {{-- 4. AKTIVITAS BOOKING TERKINI --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-xs">
                <!-- Header -->
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">
                            Aktivitas Booking Terkini
                        </h3>
                    </div>

                    <a
                        href="{{ route('penyewa.riwayat') }}"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-[#0B7B69] hover:text-[#086354] hover:underline transition"
                    >
                        <span>Lihat Semua Riwayat</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <!-- Activities List -->
                <div class="space-y-3">
                    @forelse ($recentActivities as $activity)
                        <x-penyewa.profile.activity-row
                            :sport="$activity['sport']"
                            :venue-name="$activity['venue_name']"
                            :schedule="$activity['schedule']"
                            :price="$activity['price']"
                            :payment-method="$activity['payment_method']"
                            :status-label="$activity['status_label']"
                        />
                    @empty
                        <div class="p-8 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            <p class="text-xs text-slate-500 font-medium">Belum ada riwayat booking lapangan.</p>
                            <a href="{{ route('penyewa.lapangan') }}" class="inline-block mt-2 text-xs font-bold text-[#0B7B69] hover:underline">
                                Cari dan Booking Lapangan Sekarang &rarr;
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>

{{-- =========================================================
     EDIT PROFIL MODAL
========================================================= --}}
<div
    id="edit-profile-modal"
    class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 transition-opacity"
    aria-modal="true"
    role="dialog"
>
    <div class="bg-white rounded-2xl border border-slate-200 max-w-md w-full p-6 shadow-2xl relative transform transition-all">
        <!-- Close Button -->
        <button
            type="button"
            onclick="document.getElementById('edit-profile-modal').classList.add('hidden')"
            class="absolute top-4 right-4 p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <h3 class="text-lg font-bold text-slate-900 mb-1">
            Edit Profil Penyewa
        </h3>
        <p class="text-xs text-slate-500 mb-5">
            Perbarui data nama, kontak, dan foto profil Anda.
        </p>

        <form method="POST" action="{{ route('penyewa.profil.update') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Nama Lengkap -->
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Nama Lengkap
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $profile['name']) }}"
                    required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-[#0B7B69] focus:ring-2 focus:ring-emerald-500/20 outline-none transition"
                >
            </div>

            <!-- Nomor WhatsApp / Ponsel -->
            <div>
                <label for="no_hp" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Nomor WhatsApp / Ponsel
                </label>
                <input
                    type="text"
                    id="no_hp"
                    name="no_hp"
                    value="{{ old('no_hp', $profile['phone']) }}"
                    required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:border-[#0B7B69] focus:ring-2 focus:ring-emerald-500/20 outline-none transition"
                >
            </div>

            <!-- Foto Profil -->
            <div>
                <label for="foto_profil" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Foto Profil Baru (Opsional)
                </label>
                <input
                    type="file"
                    id="foto_profil"
                    name="foto_profil"
                    accept="image/png,image/jpeg,image/jpg,image/webp"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-[#0B7B69] hover:file:bg-emerald-100 cursor-pointer focus:bg-white focus:border-[#0B7B69] outline-none transition"
                >
                <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, WEBP (Maksimal 2 MB)</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button
                    type="button"
                    onclick="document.getElementById('edit-profile-modal').classList.add('hidden')"
                    class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 rounded-xl bg-[#0B7B69] hover:bg-[#086354] active:scale-95 text-white text-xs font-bold shadow-xs transition cursor-pointer"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
</x-layouts.penyewa>
