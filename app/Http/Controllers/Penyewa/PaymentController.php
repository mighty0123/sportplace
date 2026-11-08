<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Models\BookingDetail;
use App\Models\BookingHeader;
use App\Models\Field;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Tampilkan halaman Review & Konfirmasi Pembayaran (Checkout).
     * Pengguna memeriksa detail jadwal, kontak pemesan, metode bayar, dan voucher sebelum mengklik "Bayar Sekarang".
     */
    public function review(?int $id = null, ?Request $request = null): View
    {
        Carbon::setLocale('id');
        $request = $request ?? request();

        $user = auth()->user() ?? User::where('role', 'customer')->first();
        $booking = null;
        $field = null;

        // 1. Ambil field_id dari parameter query / request / route ID
        $requestedFieldId = (int) (
            $request?->input('field_id') ??
            $request?->query('field_id') ??
            $request?->query('field') ??
            0
        );

        // Jika ada route parameter $id, periksa apakah $id adalah ID Field yang valid
        if ($id > 0) {
            $fieldCandidate = Field::with(['venue.city', 'sport', 'facilities', 'photos'])->find($id);
            if ($fieldCandidate) {
                $field = $fieldCandidate;
            } else {
                // Jika bukan ID Field, cari sebagai BookingHeader
                $bookingCandidate = BookingHeader::with([
                    'details.field.venue.city',
                    'details.field.sport',
                    'details.field.facilities',
                    'details.field.photos',
                    'user'
                ])->find($id);

                if ($bookingCandidate && $bookingCandidate->details->isNotEmpty()) {
                    $booking = $bookingCandidate;
                    $field = $bookingCandidate->details->first()->field;
                }
            }
        }

        // 2. Jika field belum ditentukan tetapi ada query 'field_id'
        if (!$field && $requestedFieldId > 0) {
            $field = Field::with(['venue.city', 'sport', 'facilities', 'photos'])->find($requestedFieldId);
        }

        // 3. Jika field belum ditentukan, ambil dari session (lapangan yang baru saja dipilih user di listing/kalender)
        if (!$field && (session()->has('selected_field_id') || session()->has('last_field_id'))) {
            $sessionFieldId = (int) (session('selected_field_id') ?? session('last_field_id'));
            if ($sessionFieldId > 0) {
                $field = Field::with(['venue.city', 'sport', 'facilities', 'photos'])->find($sessionFieldId);
            }
        }

        // 4. Jika masih belum ada, defaultkan ke Lapangan Futsal aktif pertama (Field ID 1 atau 2)
        if (!$field) {
            $field = Field::with(['venue.city', 'sport', 'facilities', 'photos'])
                ->where('status_lapangan', 'aktif')
                ->whereHas('sport', function ($q) {
                    $q->where('slug', 'futsal')->orWhere('nama_olahraga', 'LIKE', '%futsal%');
                })
                ->first();
        }

        if (!$field) {
            $field = Field::with(['venue.city', 'sport', 'facilities', 'photos'])->firstOrFail();
        }

        // Simpan field terpilih ke session agar tetap konsisten
        session(['selected_field_id' => $field->id, 'last_field_id' => $field->id]);

        $venue = $field?->venue;
        $sport = $field?->sport;
        $customer = $booking?->user ?? $user;

        // Tanggal dan Sesi Main
        if ($booking && $booking->details->isNotEmpty()) {
            $detailFirst = $booking->details->first();
            $tanggalSewaObj = Carbon::parse($detailFirst->tanggal_sewa ?? now()->addDay());
            $tanggalSewaRaw = $tanggalSewaObj->format('Y-m-d');
            $tanggalMain = $tanggalSewaObj->translatedFormat('l, d F Y');

            $minJamMulai = $booking->details->min('jam_mulai') ?? '08:00:00';
            $maxJamSelesai = $booking->details->max('jam_selesai') ?? '09:00:00';
            $jamMulaiFormatted = substr($minJamMulai, 0, 5);
            $jamSelesaiFormatted = substr($maxJamSelesai, 0, 5);
            $totalJam = $booking->details->count() > 0 ? $booking->details->count() : 1;

            $lineItems = [];
            $parsedSlots = [];
            foreach ($booking->details as $item) {
                $startSlot = substr($item->jam_mulai, 0, 5);
                $endSlot = substr($item->jam_selesai, 0, 5);
                $itemPrice = (float) $item->harga_slot;
                $lineItems[] = [
                    'label' => "Sewa {$field->nama_lapangan} ({$startSlot} - {$endSlot} WIB)",
                    'price' => $itemPrice,
                ];
                $parsedSlots[] = [
                    'start' => $startSlot,
                    'end'   => $endSlot,
                    'time'  => "{$startSlot} - {$endSlot}",
                    'price' => $itemPrice,
                ];
            }
            $subtotal = (float) $booking->details->sum('harga_slot');
            if ($subtotal <= 0) {
                $subtotal = (float) ($field->harga_perjam ?? 150000) * $totalJam;
            }
        } else {
            // Ambil tanggal dari query / input request / session
            $dateQuery = $request?->input('date') ?? $request?->query('date') ?? session('selected_date') ?? now()->addDay()->format('Y-m-d');
            try {
                $tanggalSewaObj = Carbon::parse($dateQuery);
            } catch (\Exception $e) {
                $tanggalSewaObj = Carbon::today()->addDay();
            }
            $tanggalSewaRaw = $tanggalSewaObj->format('Y-m-d');
            $tanggalMain = $tanggalSewaObj->translatedFormat('l, d F Y');

            // Ambil jadwal slot yang dipilih di halaman kalender sebelumnya
            $slotsInput = $request?->input('slots') ?? $request?->query('slots') ?? session('selected_slots');
            $defaultPrice = (float) ($field->harga_perjam ?? 150000);
            $parsedSlots = $this->parseSlots($slotsInput, $defaultPrice);

            // Jika belum ada slot dipilih sama sekali (direct link), defaultkan ke 1 jam operasional pertama
            if (empty($parsedSlots)) {
                $jamBuka = $field->venue?->jam_buka ? (int) substr($field->venue->jam_buka, 0, 2) : 8;
                $startSlot = sprintf('%02d:00', $jamBuka);
                $endSlot = sprintf('%02d:00', $jamBuka + 1);
                $parsedSlots = [
                    [
                        'start' => $startSlot,
                        'end'   => $endSlot,
                        'time'  => "{$startSlot} - {$endSlot}",
                        'price' => $defaultPrice,
                    ],
                ];
            }

            $minJamMulai = $parsedSlots[0]['start'];
            $maxJamSelesai = end($parsedSlots)['end'];
            $jamMulaiFormatted = $minJamMulai;
            $jamSelesaiFormatted = $maxJamSelesai;
            $totalJam = count($parsedSlots);

            $lineItems = [];
            $subtotal = 0;
            foreach ($parsedSlots as $slot) {
                $lineItems[] = [
                    'label' => "Sewa {$field->nama_lapangan} ({$slot['start']} - {$slot['end']} WIB)",
                    'price' => (float) $slot['price'],
                ];
                $subtotal += (float) $slot['price'];
            }
        }

        // Simpan sesi pilihan saat ini agar konsisten saat bayar / kembali
        session([
            'selected_field_id' => $field->id,
            'last_field_id'     => $field->id,
            'selected_date'     => $tanggalSewaRaw,
            'selected_slots'    => $parsedSlots,
        ]);

        $sesiDurasi = "{$jamMulaiFormatted} - {$jamSelesaiFormatted} WIB ({$totalJam} Jam)";

        // Foto Lapangan
        $sportSlug = strtolower($sport?->slug ?? 'futsal');
        $defaultImages = [
            'futsal'      => asset('images/courts/futsal.jpg'),
            'badminton'   => asset('images/courts/badminton.jpg'),
            'tenis'       => asset('images/courts/tennis.jpg'),
            'basket'      => asset('images/courts/basketball.jpg'),
            'mini-soccer' => asset('images/courts/futsal.jpg'),
        ];
        $primaryPhoto = $field?->photos?->firstWhere('is_primary', 1) ?? $field?->photos?->first();
        $courtPhoto = $primaryPhoto ? asset($primaryPhoto->foto_url) : ($defaultImages[$sportSlug] ?? asset('images/courts/futsal.jpg'));

        // Fasilitas
        $facilities = $field?->facilities?->pluck('nama')->toArray() ?? [];
        if (empty($facilities)) {
            $facilities = ['Ruang Ganti & Shower', 'Free Parkir Mobil/Motor', 'Lighting Standar Turnamen'];
        }

        // Biaya & Diskon
        $serviceFee = 2500;
        $promoDiscount = 20000;
        $totalBayar = max(0, $subtotal + $serviceFee - $promoDiscount);
        $slotsPayload = json_encode($parsedSlots);

        return view('penyewa.review-pembayaran', [
            'booking'             => $booking,
            'customer'            => $customer,
            'field'               => $field,
            'venue'               => $venue,
            'sport'               => $sport,
            'courtPhoto'          => $courtPhoto,
            'facilities'          => $facilities,
            'tanggalMain'         => $tanggalMain,
            'tanggalSewaRaw'      => $tanggalSewaRaw,
            'sesiDurasi'          => $sesiDurasi,
            'jamMulaiFormatted'   => $jamMulaiFormatted,
            'jamSelesaiFormatted' => $jamSelesaiFormatted,
            'totalJam'            => $totalJam,
            'lineItems'           => $lineItems,
            'subtotal'            => $subtotal,
            'serviceFee'          => $serviceFee,
            'promoDiscount'       => $promoDiscount,
            'totalBayar'          => $totalBayar,
            'slotsPayload'        => $slotsPayload,
        ]);
    }

    /**
     * Proses submit pembayaran dari halaman Review Pembayaran.
     * Mengubah status pesanan menjadi lunas/payment dan mengalihkan ke Halaman Tiket Arena & Invoice.
     */
    public function processPayment(?int $id = null, ?Request $request = null): RedirectResponse
    {
        $request = $request ?? request();
        $user = auth()->user() ?? User::where('role', 'customer')->first();

        // Ambil field_id yang dipilih dari request, parameter rute, atau session
        $fieldId = (int) (
            $request?->input('field_id') ??
            $request?->query('field_id') ??
            $id ??
            session('selected_field_id') ??
            session('last_field_id') ??
            1
        );
        $field = Field::with(['venue.city', 'sport'])->find($fieldId) ?? Field::first();

        $paymentMethod = $request?->input('metode_pembayaran', 'BCA Virtual Account');
        $tanggalSewa = $request?->input('tanggal_sewa') ?? session('selected_date') ?? now()->addDay()->format('Y-m-d');

        // Ambil slot jadwal yang dipilih
        $slotsInput = $request?->input('slots') ?? session('selected_slots');
        $defaultPrice = (float) ($field->harga_perjam ?? 150000);
        $parsedSlots = $this->parseSlots($slotsInput, $defaultPrice);

        if (empty($parsedSlots)) {
            $jamBuka = $field->venue?->jam_buka ? (int) substr($field->venue->jam_buka, 0, 2) : 8;
            $startSlot = sprintf('%02d:00', $jamBuka);
            $endSlot = sprintf('%02d:00', $jamBuka + 1);
            $parsedSlots = [
                [
                    'start' => $startSlot,
                    'end'   => $endSlot,
                    'time'  => "{$startSlot} - {$endSlot}",
                    'price' => $defaultPrice,
                ],
            ];
        }

        // Hitung total tagihan dari slot yang sebenarnya
        $subtotal = array_sum(array_column($parsedSlots, 'price'));
        $serviceFee = 2500;
        $promoDiscount = 20000;
        $calculatedTotal = max(0, $subtotal + $serviceFee - $promoDiscount);

        $totalBayar = (float) ($request?->input('total_bayar', 0));
        if ($totalBayar <= 0) {
            $totalBayar = $calculatedTotal;
        }

        // Buat record booking baru yang terhubung ke lapangan yang DIPILIH pengguna
        $booking = BookingHeader::create([
            'penyewa_id'    => $user->id,
            'total_tagihan' => $totalBayar,
            'status'        => 'payment',
            'created_at'    => now(),
        ]);

        // Buat detail slot sesuai jadwal yang DIPILIH pengguna
        foreach ($parsedSlots as $slot) {
            $jamMulai = strlen($slot['start']) === 5 ? $slot['start'] . ':00' : $slot['start'];
            $jamSelesai = strlen($slot['end']) === 5 ? $slot['end'] . ':00' : $slot['end'];

            BookingDetail::create([
                'header_id'    => $booking->id,
                'field_id'     => $field->id,
                'tanggal_sewa' => $tanggalSewa,
                'jam_mulai'    => $jamMulai,
                'jam_selesai'  => $jamSelesai,
                'harga_slot'   => (float) $slot['price'],
                'status_item'  => 'ongoing',
                'created_at'   => now(),
            ]);
        }

        Payment::create([
            'header_id'         => $booking->id,
            'pg_transaction_id' => '954' . rand(1000, 9999),
            'metode_pembayaran' => $paymentMethod,
            'nominal_bayar'     => $totalBayar,
            'status_pembayaran' => 'succes',
            'paid_at'           => now(),
            'created_at'        => now(),
        ]);

        // Simpan field ID dan state ke session
        session([
            'selected_field_id' => $field->id,
            'last_field_id'     => $field->id,
            'selected_date'     => $tanggalSewa,
            'selected_slots'    => $parsedSlots,
        ]);

        return redirect()->route('penyewa.booking.detail', ['id' => $booking->id])
            ->with('success', 'Pembayaran berhasil dikonfirmasi! Tiket check-in arena Anda sudah aktif dan siap digunakan.');
    }

    /**
     * Tampilkan halaman Detail Booking, Invoice Pembayaran, dan Tiket Check-in Arena (Setelah Bayar).
     */
    public function show(?int $id = null, ?Request $request = null): View
    {
        Carbon::setLocale('id');
        $request = $request ?? request();

        $user = auth()->user() ?? User::where('role', 'customer')->first();

        // 1. Ambil data BookingHeader berdasarkan ID, atau fallback ke booking terbaru milik user
        $booking = null;

        if ($id) {
            $booking = BookingHeader::with([
                'details.field.venue.city',
                'details.field.sport',
                'details.field.facilities',
                'details.field.photos',
                'payment',
                'user'
            ])->find($id);

            // Jika ID yang dimasukkan adalah ID BookingDetail, cari BookingHeader induknya
            if (!$booking) {
                $detailItem = BookingDetail::find($id);
                if ($detailItem) {
                    $booking = BookingHeader::with([
                        'details.field.venue.city',
                        'details.field.sport',
                        'details.field.facilities',
                        'details.field.photos',
                        'payment',
                        'user'
                    ])->find($detailItem->header_id);
                }
            }

            // Jika $id adalah ID Field, cari booking terkait field tersebut
            if (!$booking) {
                $fieldBooking = BookingHeader::with([
                    'details.field.venue.city',
                    'details.field.sport',
                    'details.field.facilities',
                    'details.field.photos',
                    'payment',
                    'user'
                ])->whereHas('details', function ($q) use ($id) {
                    $q->where('field_id', $id);
                })->latest('id')->first();

                if ($fieldBooking) {
                    $booking = $fieldBooking;
                }
            }
        }

        // Cek booking milik user yang cocok dengan session selected_field_id
        if (!$booking && session()->has('selected_field_id')) {
            $selectedFieldId = session('selected_field_id');
            $booking = BookingHeader::with([
                'details.field.venue.city',
                'details.field.sport',
                'details.field.facilities',
                'details.field.photos',
                'payment',
                'user'
            ])->whereHas('details', function ($q) use ($selectedFieldId) {
                $q->where('field_id', $selectedFieldId);
            })->latest('id')->first();
        }

        // Jika tidak ada ID atau tidak ditemukan, ambil booking terbaru milik user saat ini
        if (!$booking && $user) {
            $booking = BookingHeader::with([
                'details.field.venue.city',
                'details.field.sport',
                'details.field.facilities',
                'details.field.photos',
                'payment',
                'user'
            ])->where('penyewa_id', $user->id)->latest('id')->first();
        }

        // Fallback jika belum ada booking: cari booking Futsal (Bukan Tenis)
        if (!$booking) {
            $booking = BookingHeader::with([
                'details.field.venue.city',
                'details.field.sport',
                'details.field.facilities',
                'details.field.photos',
                'payment',
                'user'
            ])->whereHas('details.field.sport', function ($q) {
                $q->where('slug', 'futsal')->orWhere('nama_olahraga', 'LIKE', '%futsal%');
            })->latest('id')->first();
        }

        if (!$booking) {
            $booking = BookingHeader::with([
                'details.field.venue.city',
                'details.field.sport',
                'details.field.facilities',
                'details.field.photos',
                'payment',
                'user'
            ])->firstOrFail();
        }

        // 2. Format Data Header & Metadata Transaksi
        $detailFirst = $booking->details->first();
        $field = $detailFirst?->field;
        $venue = $field?->venue;
        $sport = $field?->sport;
        $customer = $booking->user ?? $user;

        $bookingCode = 'SP-' . Carbon::parse($booking->created_at)->format('Ymd') . '-' . str_pad($booking->id, 3, '0', STR_PAD_LEFT);
        $cleanVenueCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $venue?->nama_venue ?? 'ARENA'), 0, 4));
        $ticketCode = 'SP-' . strtoupper(substr(md5($booking->id), 0, 4)) . '-' . $cleanVenueCode;
        $trxId = '#TRX-' . ($booking->payment?->pg_transaction_id ?? ($booking->id * 739182));

        $dipesanPada = Carbon::parse($booking->created_at)->translatedFormat('d M Y, H:i') . ' WIB';

        // 3. Format Waktu & Sesi Main
        $tanggalSewaObj = Carbon::parse($detailFirst?->tanggal_sewa ?? $booking->created_at);
        $tanggalMain = $tanggalSewaObj->translatedFormat('l, d F Y');

        $defaultJamBuka = $field?->venue?->jam_buka ? substr($field->venue->jam_buka, 0, 8) : '08:00:00';
        $defaultJamTutup = $field?->venue?->jam_tutup ? substr($field->venue->jam_tutup, 0, 8) : '09:00:00';
        $minJamMulai = $booking->details->min('jam_mulai') ?? $defaultJamBuka;
        $maxJamSelesai = $booking->details->max('jam_selesai') ?? $defaultJamTutup;
        $jamMulaiFormatted = substr($minJamMulai, 0, 5);
        $jamSelesaiFormatted = substr($maxJamSelesai, 0, 5);

        $totalJam = $booking->details->count() > 0 ? $booking->details->count() : 1;
        $sesiDurasi = "{$jamMulaiFormatted} - {$jamSelesaiFormatted} WIB ({$totalJam} Jam)";
        $berlakuHingga = $tanggalSewaObj->translatedFormat('d M Y') . ', ' . $jamSelesaiFormatted . ' WIB';

        // 4. Foto Lapangan & Fallback Olahraga
        $sportSlug = strtolower($sport?->slug ?? 'futsal');
        $defaultImages = [
            'futsal'      => asset('images/courts/futsal.jpg'),
            'badminton'   => asset('images/courts/badminton.jpg'),
            'tenis'       => asset('images/courts/tennis.jpg'),
            'basket'      => asset('images/courts/basketball.jpg'),
            'mini-soccer' => asset('images/courts/futsal.jpg'),
        ];

        $primaryPhoto = $field?->photos?->firstWhere('is_primary', 1) ?? $field?->photos?->first();
        $courtPhoto = $primaryPhoto ? asset($primaryPhoto->foto_url) : ($defaultImages[$sportSlug] ?? asset('images/courts/futsal.jpg'));

        // 5. Fasilitas Lapangan
        $facilities = $field?->facilities?->pluck('nama')->toArray() ?? [];
        if (empty($facilities)) {
            $facilities = ['Ruang Ganti & Shower', 'Free Parkir Mobil/Motor', 'Lighting Standar Turnamen'];
        }

        // 6. Rincian Item Biaya (Per Jam / Sesi)
        $lineItems = [];
        foreach ($booking->details as $item) {
            $startSlot = substr($item->jam_mulai, 0, 5);
            $endSlot = substr($item->jam_selesai, 0, 5);
            $lineItems[] = [
                'label' => "Sewa {$field->nama_lapangan} ({$startSlot} - {$endSlot} WIB)",
                'price' => (float) $item->harga_slot,
            ];
        }

        if (empty($lineItems)) {
            $lineItems[] = [
                'label' => "Sewa {$field->nama_lapangan} ({$jamMulaiFormatted} - {$jamSelesaiFormatted} WIB)",
                'price' => (float) $booking->total_tagihan,
            ];
        }

        $serviceFee = 2500;
        $promoDiscount = 20000;
        $totalBayar = (float) $booking->total_tagihan;

        // Payment Method & Status
        $paymentMethod = $booking->payment?->metode_pembayaran ?? 'BCA Virtual Account';
        $isPaid = in_array(strtolower($booking->status), ['payment', 'complated', 'completed']);
        $isCancelled = in_array(strtolower($booking->status), ['cancelled', 'canceled']);

        $statusLabel = match(strtolower($booking->status)) {
            'payment', 'complated', 'completed' => 'Terkonfirmasi',
            'pending'               => 'Menunggu Pembayaran',
            'cancelled', 'canceled' => 'Dibatalkan',
            default                 => 'Terkonfirmasi',
        };

        return view('penyewa.pembayaran', [
            'booking'         => $booking,
            'customer'        => $customer,
            'field'           => $field,
            'venue'           => $venue,
            'sport'           => $sport,
            'bookingCode'     => $bookingCode,
            'ticketCode'      => $ticketCode,
            'trxId'           => $trxId,
            'dipesanPada'     => $dipesanPada,
            'tanggalMain'     => $tanggalMain,
            'sesiDurasi'      => $sesiDurasi,
            'berlakuHingga'   => $berlakuHingga,
            'courtPhoto'      => $courtPhoto,
            'facilities'      => $facilities,
            'lineItems'       => $lineItems,
            'serviceFee'      => $serviceFee,
            'promoDiscount'   => $promoDiscount,
            'totalBayar'      => $totalBayar,
            'paymentMethod'   => $paymentMethod,
            'isPaid'          => $isPaid,
            'isCancelled'     => $isCancelled,
            'statusLabel'     => $statusLabel,
        ]);
    }

    /**
     * Batalkan pemesanan booking.
     */
    public function cancel(int $id): RedirectResponse
    {
        $user = auth()->user() ?? User::where('role', 'customer')->first();

        $booking = BookingHeader::find($id);
        if ($booking) {
            $booking->update(['status' => 'canceled']);
            BookingDetail::where('header_id', $id)->update(['status_item' => 'no_show']);
        }

        return redirect()->route('penyewa.booking.detail', ['id' => $id])
            ->with('success', 'Pemesanan berhasil dibatalkan.');
    }

    /**
     * Unduh atau cetak invoice PDF pemesanan.
     */
    public function invoice(int $id)
    {
        $booking = BookingHeader::with([
            'details.field.venue.city',
            'details.field.sport',
            'payment',
            'user'
        ])->findOrFail($id);

        // Jika package DomPDF terpasang, render PDF
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('penyewa.invoice-pdf', ['booking' => $booking]);
            return $pdf->download("Invoice-Booking-{$booking->id}.pdf");
        }

        return redirect()->route('penyewa.booking.detail', ['id' => $id]);
    }

    /**
     * Parse string / array / JSON input slot jam menjadi array terstruktur.
     */
    protected function parseSlots($slotsInput, float $defaultPrice = 150000): array
    {
        if (empty($slotsInput)) {
            return [];
        }

        $rawItems = [];

        if (is_array($slotsInput)) {
            $rawItems = $slotsInput;
        } elseif (is_string($slotsInput)) {
            $trimmed = trim($slotsInput);
            $decoded = json_decode($trimmed, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $rawItems = $decoded;
            } elseif (str_contains($trimmed, '|')) {
                $rawItems = explode('|', $trimmed);
            } elseif (str_contains($trimmed, ',') && (str_contains($trimmed, ':') || str_contains($trimmed, '-'))) {
                $rawItems = explode(',', $trimmed);
            } else {
                $rawItems = [$trimmed];
            }
        }

        $slots = [];
        foreach ($rawItems as $item) {
            $start = null;
            $end = null;
            $price = $defaultPrice;

            if (is_array($item)) {
                $start = $item['start'] ?? $item['jam_mulai'] ?? null;
                $end = $item['end'] ?? $item['jam_selesai'] ?? null;
                $price = isset($item['price']) ? (float) $item['price'] : (isset($item['harga_slot']) ? (float) $item['harga_slot'] : $defaultPrice);

                if (!$start && isset($item['time'])) {
                    if (preg_match('/(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})/', $item['time'], $m)) {
                        $start = $m[1];
                        $end = $m[2];
                    }
                }
            } elseif (is_string($item)) {
                if (preg_match('/(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})(?::(\d+))?/', $item, $matches)) {
                    $start = $matches[1];
                    $end = $matches[2];
                    if (!empty($matches[3])) {
                        $price = (float) $matches[3];
                    }
                }
            }

            if ($start && $end) {
                $startParts = explode(':', $start);
                $endParts = explode(':', $end);
                $startNorm = sprintf('%02d:%02d', (int) $startParts[0], (int) ($startParts[1] ?? 0));
                $endNorm = sprintf('%02d:%02d', (int) $endParts[0], (int) ($endParts[1] ?? 0));

                $slots[] = [
                    'start' => $startNorm,
                    'end'   => $endNorm,
                    'time'  => "{$startNorm} - {$endNorm}",
                    'price' => $price > 0 ? $price : $defaultPrice,
                ];
            }
        }

        // Urutkan slot berdasarkan jam mulai secara kronologis
        usort($slots, function ($a, $b) {
            return strcmp($a['start'], $b['start']);
        });

        return $slots;
    }
}
