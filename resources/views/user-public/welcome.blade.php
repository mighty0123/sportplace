<x-layouts.public title="SportPlace — Cari Lapangan. Main Tanpa Ribet.">

    <!-- 1. Hero Section -->
    <x-public.hero />

    <!-- 2. Section: Main Olahraga Favoritmu -->
    <section id="kategori" class="py-16 sm:py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-10 text-left">
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#1B2724] mb-2">
                Main Olahraga Favoritmu
            </h2>
            <p class="text-sm sm:text-base text-[#5A6C5B]">
                Temukan lapangan dari berbagai cabang olahraga.
            </p>
        </div>

        <!-- Sport Categories Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 sm:gap-5">
            @foreach($sports as $sport)
                <x-public.category-card 
                    :type="$sport['type']" 
                    :name="$sport['name']" 
                    :count="$sport['count']" 
                    :href="$sport['href']" 
                />
            @endforeach
        </div>
    </section>

    <!-- 3. Section: Lapangan Populer -->
    <section id="lapangan-populer" class="py-12 sm:py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 sm:mb-10 gap-4">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#1B2724] mb-2">
                    Lapangan Populer
                </h2>
                <p class="text-sm sm:text-base text-[#5A6C5B]">
                    Temukan pilihan lapangan yang banyak diminati.
                </p>
            </div>

            <a 
                href="{{ route('penyewa.lapangan') }}" 
                class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#0A7660] hover:text-[#0B604F] group transition"
            >
                <span>Lihat semua lapangan</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>

        <!-- Popular Field Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($popularFields as $card)
                <x-public.field-card 
                    :image="$card['image']"
                    :sport="$card['sport']"
                    :city="$card['city']"
                    :venue="$card['venue_name'] ?? null"
                    :name="$card['name']"
                    :rating="$card['rating']"
                    :reviews="$card['reviews']"
                    :facilities="$card['facilities']"
                    :price="$card['price']"
                    :href="$card['href']"
                />
            @endforeach
        </div>
    </section>

    <!-- 4. Section: Keunggulan (Booking Lapangan Lebih Mudah) -->
    <section id="keunggulan" class="py-16 sm:py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16">
            <!-- Section Badge -->
            <span class="inline-block text-[11px] font-bold uppercase tracking-wider text-[#0A7660] bg-[#EFFBF3] border border-[#BAEECD] px-3.5 py-1 rounded-full mb-3">
                Keunggulan
            </span>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-[#1B2724] mb-3">
                Booking Lapangan Lebih Mudah
            </h2>
            <p class="text-sm sm:text-base text-[#5A6C5B] leading-relaxed">
                Kemudahan mencari dan mengamankan jadwal lapangan olahraga tanpa ribet telepon atau antri manual.
            </p>
        </div>

        <!-- 4 Feature Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-public.feature-card 
                icon="choice"
                title="Banyak Pilihan"
                description="Temukan lapangan dari berbagai cabang olahraga dalam satu platform praktis."
            />
            <x-public.feature-card 
                icon="price"
                title="Harga Transparan"
                description="Tarif harga sesuai jam main secara jelas sebelum melakukan booking tanpa biaya tersembunyi."
            />
            <x-public.feature-card 
                icon="calendar"
                title="Jadwal Jelas"
                description="Cek ketersediaan slot jam secara langsung dan akurat sebelum memilih jadwal main."
            />
            <x-public.feature-card 
                icon="bolt"
                title="Booking Lebih Praktis"
                description="Nikmati proses reservasi yang sederhana, terarah, dan konfirmasi instan."
            />
        </div>
    </section>

    <!-- 5. Section: Cara Booking (Proses Cepat) -->
    <section class="py-16 sm:py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14 sm:mb-16">
            <!-- Section Badge -->
            <span class="inline-block text-[11px] font-bold uppercase tracking-wider text-[#0A7660] bg-[#EFFBF3] border border-[#BAEECD] px-3.5 py-1 rounded-full mb-3">
                Proses Cepat
            </span>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-[#1B2724] mb-3">
                Cara Booking
            </h2>
            <p class="text-sm sm:text-base text-[#5A6C5B] leading-relaxed">
                Empat langkah mudah untuk mulai bermain bersama timmu.
            </p>
        </div>

        <!-- 4 Connected Process Steps -->
        <div class="relative">
            <!-- Desktop Connecting Horizontal Line -->
            <div class="hidden md:block absolute top-7 left-[12%] right-[12%] h-[2px] bg-[#E1E3D9] z-0"></div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 md:gap-6 relative z-10">
                <x-public.step-card 
                    number="01"
                    title="Cari"
                    description="Pilih cabang olahraga, lokasi kota, dan rentang harga yang sesuai."
                />
                <x-public.step-card 
                    number="02"
                    title="Pilih Lapangan"
                    description="Bandingkan fasilitas, foto lapangan, ulasan, dan harga."
                />
                <x-public.step-card 
                    number="03"
                    title="Pilih Jadwal"
                    description="Lihat slot jam yang tersedia dan tentukan durasi main."
                />
                <x-public.step-card 
                    number="04"
                    title="Booking"
                    description="Lakukan konfirmasi reservasi dan dapatkan tiket booking digital."
                    :highlight="true"
                    :isLast="true"
                />
            </div>
        </div>
    </section>

    <!-- 6. Section: Verified Guarantee Banner -->
    <section class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-public.verified-banner />
    </section>

    <!-- 7. Section: CTA Banner (Siap Mulai Main?) -->
    <x-public.cta-section />

</x-layouts.public>
