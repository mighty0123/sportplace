<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Tiket — {{ $booking->kode_booking }} — SportPlace</title>

    <!-- Google Fonts Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased bg-[#F4F7F5] text-slate-800 flex flex-col justify-center items-center p-4 sm:p-6 min-h-screen">

    <div class="w-full max-w-md mx-auto space-y-4">

        {{-- Top Brand Header --}}
        <div class="text-center space-y-1">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
                <span class="text-2xl font-black text-[#06231D] tracking-tight">Sport<span class="text-[#0A7660]">Place</span></span>
            </a>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Portal Verifikasi Tiket Resmi</p>
        </div>

        {{-- Notification Alert --}}
        @if (session('success'))
            <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @elseif (session('warning'))
            <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>{{ session('warning') }}</span>
            </div>
        @elseif (session('error'))
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Ticket Card --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-lg overflow-hidden">

            {{-- Card Header --}}
            <div class="bg-[#06231D] text-white p-5 text-center relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-emerald-500/10 pointer-events-none"></div>

                <div class="inline-flex items-center justify-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider mb-2
                    {{ $isCheckedIn ? 'bg-slate-700 text-slate-200 border border-slate-600' : ($isPaid ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30' : 'bg-amber-500/20 text-amber-300 border border-amber-400/30') }}">
                    @if ($isCheckedIn)
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                        <span>SUDAH CHECK-IN</span>
                    @elseif ($isPaid)
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>TIKET VALID & LUNAS</span>
                    @else
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                        <span>MENUNGGU PEMBAYARAN</span>
                    @endif
                </div>

                <h1 class="text-xs text-emerald-200/80 uppercase tracking-widest font-semibold">KODE BOOKING</h1>
                <p class="text-2xl font-black font-mono tracking-wider text-white mt-0.5">
                    {{ $booking->kode_booking ?? ('SP-' . $booking->id) }}
                </p>
            </div>

            {{-- Card Body --}}
            <div class="p-5 sm:p-6 space-y-4">

                {{-- QR Code Center --}}
                <div class="text-center">
                    <div class="inline-block p-3 rounded-2xl bg-white border border-slate-200 shadow-xs">
                        @if (!empty($qrCodeDataUri))
                            <img src="{{ $qrCodeDataUri }}" alt="QR Code" class="w-36 h-36 mx-auto object-contain">
                        @else
                            <div class="w-36 h-36 bg-slate-100 flex items-center justify-center text-xs text-slate-400">
                                QR Code
                            </div>
                        @endif
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">Tunjukkan QR ini ke petugas arena saat tiba</p>
                </div>

                {{-- Booking Details Summary --}}
                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 space-y-2.5 text-xs">
                    <div class="flex items-start justify-between gap-2 border-b border-slate-200/60 pb-2">
                        <span class="text-slate-500">Venue</span>
                        <span class="font-bold text-slate-800 text-right">{{ $venue->nama_venue ?? 'Sport Arena' }}</span>
                    </div>

                    <div class="flex items-start justify-between gap-2 border-b border-slate-200/60 pb-2">
                        <span class="text-slate-500">Lapangan</span>
                        <span class="font-bold text-[#0A7660] text-right">{{ $field->nama_lapangan ?? 'Lapangan' }}</span>
                    </div>

                    @php
                        $firstDetail = $booking->details->first();
                        $tglMain = $firstDetail ? \Carbon\Carbon::parse($firstDetail->tanggal_sewa)->translatedFormat('l, d F Y') : '-';
                    @endphp
                    <div class="flex items-start justify-between gap-2 border-b border-slate-200/60 pb-2">
                        <span class="text-slate-500">Tanggal Main</span>
                        <span class="font-bold text-slate-800 text-right">{{ $tglMain }}</span>
                    </div>

                    <div class="flex items-start justify-between gap-2 border-b border-slate-200/60 pb-2">
                        <span class="text-slate-500">Sesi Jam</span>
                        <div class="text-right font-bold text-slate-800">
                            @foreach ($booking->details as $d)
                                <div>{{ substr($d->jam_mulai, 0, 5) }} - {{ substr($d->jam_selesai, 0, 5) }} WIB</div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-start justify-between gap-2 border-b border-slate-200/60 pb-2">
                        <span class="text-slate-500">Penyewa</span>
                        <span class="font-bold text-slate-800 text-right">{{ $booking->user->name ?? 'Penyewa' }}</span>
                    </div>

                    <div class="flex items-start justify-between gap-2">
                        <span class="text-slate-500">Total Biaya</span>
                        <span class="font-extrabold text-[#0B7B69] text-sm tabular-nums">Rp {{ number_format($booking->total_tagihan, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Check-In Action Section --}}
                @if ($isCheckedIn)
                    <div class="p-3.5 rounded-xl bg-slate-100 border border-slate-200 text-center space-y-0.5">
                        <span class="text-[11px] font-bold text-slate-700 block uppercase tracking-wider">✓ Tiket Sudah Di-Check-In</span>
                        <span class="text-[11px] text-slate-500 block">
                            Diverifikasi pada: <strong>{{ \Carbon\Carbon::parse($booking->checked_in_at)->translatedFormat('d M Y, H:i') }} WIB</strong>
                        </span>
                    </div>
                @elseif ($isPaid)
                    <form action="{{ route('booking.checkin', ['code' => $booking->kode_booking ?? $booking->id]) }}" method="POST" onsubmit="return confirm('Konfirmasi check-in untuk penyewa ini?')">
                        @csrf
                        <button
                            type="submit"
                            class="w-full py-3 px-4 rounded-xl bg-[#0A7660] hover:bg-[#086354] active:scale-[0.98] text-white font-extrabold text-xs sm:text-sm shadow-md transition flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Konfirmasi Check-In Sekarang</span>
                        </button>
                    </form>
                @else
                    <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-center text-xs text-amber-800">
                        Tiket belum dapat digunakan karena pembayaran belum diselesaikan.
                    </div>
                @endif

                {{-- Action Links --}}
                <div class="pt-2 flex items-center justify-between gap-2 text-xs">
                    <a
                        href="{{ route('penyewa.booking.invoice', ['id' => $booking->kode_booking ?? $booking->id]) }}"
                        target="_blank"
                        class="text-[#0A7660] hover:underline font-bold inline-flex items-center gap-1"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Unduh Nota (PDF)</span>
                    </a>

                    @auth
                        <a href="{{ route('penyewa.booking.detail', ['id' => $booking->kode_booking ?? $booking->id]) }}" class="text-slate-500 hover:text-slate-800 font-semibold">
                            Halaman Booking &rarr;
                        </a>
                    @endauth
                </div>

            </div>
        </div>

    </div>

</body>
</html>
