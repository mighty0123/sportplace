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

        <!-- 6 Sport Categories Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 sm:gap-5">
            <x-public.category-card 
                type="futsal" 
                name="Futsal" 
                :count="14" 
                href="#lapangan-populer" 
            />
            <x-public.category-card 
                type="badminton" 
                name="Badminton" 
                :count="10" 
                href="#lapangan-populer" 
            />
            <x-public.category-card 
                type="basket" 
                name="Basket" 
                :count="8" 
                href="#lapangan-populer" 
            />
            <x-public.category-card 
                type="tenis" 
                name="Tenis" 
                :count="6" 
                href="#lapangan-populer" 
            />
            <x-public.category-card 
                type="voli" 
                name="Voli" 
                :count="4" 
                href="#lapangan-populer" 
            />
            <x-public.category-card 
                type="tenis-meja" 
                name="Tenis Meja" 
                :count="2" 
                href="#lapangan-populer" 
            />
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
                href="#lapangan-populer" 
                class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#0A7660] hover:text-[#0B604F] group transition"
            >
                <span>Lihat semua lapangan</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>

        <!-- 4 Popular Field Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1: Arena Sport Center -->
            <x-public.field-card 
                :image="asset('images/fields/arena-sport-center.jpg')"
                sport="Badminton"
                city="Denpasar"
                name="Arena Sport Center"
                rating="4.8"
                reviews="120"
                :facilities="['Vinyl', 'AC', 'Shower']"
                :price="80000"
                href="#detail-arena-sport"
            />

            <!-- Card 2: Green Court Arena -->
            <x-public.field-card 
                :image="asset('images/fields/green-court-arena.jpg')"
                sport="Futsal"
                city="Denpasar"
                name="Green Court Arena"
                rating="4.9"
                reviews="85"
                :facilities="['Sintetis', 'Kantin', 'Loker']"
                :price="120000"
                href="#detail-green-court"
            />

            <!-- Card 3: Bali Basket Hub -->
            <x-public.field-card 
                :image="asset('images/fields/bali-basket-hub.jpg')"
                sport="Basket"
                city="Gianyar"
                name="Bali Basket Hub"
                rating="4.7"
                reviews="95"
                :facilities="['Parquet', 'Locker', 'Tribun']"
                :price="100000"
                href="#detail-bali-basket"
            />

            <!-- Card 4: Sunrise Tennis Club -->
            <x-public.field-card 
                :image="asset('images/fields/sunrise-tennis-club.jpg')"
                sport="Tenis"
                city="Badung"
                name="Sunrise Tennis Club"
                rating="4.9"
                reviews="140"
                :facilities="['Hard Court', 'Rooftop', 'Cafe']"
                :price="150000"
                href="#detail-sunrise-tennis"
            />
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
