<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Models\BookingDetail;
use App\Models\BookingHeader;
use App\Models\Field;
use App\Models\Payment;
use App\Models\User;
use App\Services\MidtransService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Tampilkan halaman Review & Konfirmasi Pembayaran (Checkout).
     * Pengguna memeriksa detail jadwal, kontak pemesan, metode bayar, dan voucher sebelum mengklik "Bayar Sekarang".
     */
    public function review(?int $id = null, ?Request $request = null): View|RedirectResponse
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
            $tz = config('app.timezone', 'Asia/Makassar');
            $now = Carbon::now($tz);

            foreach ($parsedSlots as $slot) {
                // Cegah jika jam mulai slot sudah lewat waktu saat ini
                $slotStartDateTime = Carbon::parse("{$tanggalSewaRaw} {$slot['start']}", $tz);
                if ($slotStartDateTime->lte($now)) {
                    return redirect()->route('penyewa.kalender', ['id' => $field->id, 'date' => $tanggalSewaRaw])
                        ->with('warning', "Slot jam {$slot['start']} sudah lewat jam mulai dan tidak dapat dipesan. Silakan pilih jadwal lain.");
                }

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
        $promoDiscount = 0;
        $totalBayar = max(0, $subtotal + $serviceFee);
        $slotsPayload = json_encode($parsedSlots);

        // Batas waktu pembayaran 15 menit (persisten dihitung dari timestamp booking / checkout session)
        $tz = config('app.timezone', 'Asia/Makassar');
        $now = Carbon::now($tz);
        $expiresAt = null;

        // 1. Cek apakah ada booking pending aktif milik user untuk lapangan ini
        if (!$booking && $user) {
            $pendingCandidate = BookingHeader::where('penyewa_id', $user->id)
                ->where('status', 'pending')
                ->where(function ($q) use ($now) {
                    $q->where('expires_at', '>', $now)
                      ->orWhere(function ($sub) use ($now) {
                          $sub->whereNull('expires_at')
                              ->where('created_at', '>', $now->copy()->subMinutes(15));
                      });
                })
                ->whereHas('details', function ($q) use ($field) {
                    $q->where('field_id', $field->id);
                })
                ->latest('id')
                ->first();

            if ($pendingCandidate) {
                $booking = $pendingCandidate;
            }
        }

        if ($booking && $booking->status === 'pending') {
            $rawExpires = $booking->getRawOriginal('expires_at');
            $rawCreated = $booking->getRawOriginal('created_at');
            $expiresAt = $rawExpires
                ? Carbon::parse($rawExpires, $tz)
                : ($rawCreated ? Carbon::parse($rawCreated, $tz)->addMinutes(15) : null);
        }

        // 2. Jika belum ada booking di database, gunakan anchor session checkout agar tidak berulang reset saat refresh
        $sessionKey = 'checkout_expires_at_' . $field->id;
        if (!$expiresAt) {
            if (session()->has($sessionKey)) {
                $storedExpires = (int) session($sessionKey);
                if ($storedExpires > $now->timestamp) {
                    $expiresAt = Carbon::createFromTimestamp($storedExpires, $tz);
                }
            } elseif (session()->has('booking_expires_at')) {
                $storedExpires = (int) session('booking_expires_at');
                if ($storedExpires > $now->timestamp) {
                    $expiresAt = Carbon::createFromTimestamp($storedExpires, $tz);
                }
            }

            // Jika belum ada atau sudah kedaluwarsa, tentukan anchor 15 menit baru
            if (!$expiresAt) {
                $expiresAt = $now->copy()->addMinutes(15);
                session([$sessionKey => $expiresAt->timestamp, 'booking_expires_at' => $expiresAt->timestamp]);
            }
        }

        $expiresAtTimestamp = $expiresAt->timestamp;
        $remainingSeconds = max(0, (int) $expiresAtTimestamp - $now->timestamp);

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
            'expiresAtTimestamp'  => $expiresAtTimestamp,
            'remainingSeconds'    => $remainingSeconds,
        ]);
    }

    /**
     * Proses submit pembayaran dari halaman Review Pembayaran.
     * Mengubah status pesanan menjadi lunas/payment dan mengalihkan ke Halaman Tiket Arena & Invoice.
     */
    /**
     * Proses submit pembayaran dari halaman Review Pembayaran.
     * Mengunci slot dengan status 'pending', membuat transaksi Midtrans Snapbox,
     * dan mengembalikan Snap token untuk memicu popup pembayaran di frontend.
     */
    public function processPayment(?int $id = null, ?Request $request = null): RedirectResponse|JsonResponse
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

        $paymentMethod = $request?->input('metode_pembayaran', 'Midtrans');
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
        $promoDiscount = 0;
        $calculatedTotal = max(0, $subtotal + $serviceFee);

        $totalBayar = (float) ($request?->input('total_bayar', 0));
        if ($totalBayar <= 0) {
            $totalBayar = $calculatedTotal;
        }

        // Validasi ketersediaan setiap slot sebelum pemesanan
        $tz = config('app.timezone', 'Asia/Makassar');
        $now = Carbon::now($tz);
        foreach ($parsedSlots as $slot) {
            $jamMulai = strlen($slot['start']) === 5 ? $slot['start'] . ':00' : $slot['start'];
            $jamSelesai = strlen($slot['end']) === 5 ? $slot['end'] . ':00' : $slot['end'];
            $startStr = substr($jamMulai, 0, 5);

            // Cek apakah slot sudah lewat jam mulai (langsung dikunci begitu lewat jam_mulai)
            $slotStartDateTime = Carbon::parse("{$tanggalSewa} {$startStr}", $tz);
            if ($slotStartDateTime->lte($now)) {
                $errorMsg = "Slot jam {$startStr} sudah lewat jam mulai dan tidak dapat dipesan.";
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $errorMsg], 422);
                }
                return redirect()->route('penyewa.kalender', ['id' => $field->id, 'date' => $tanggalSewa])
                    ->with('warning', $errorMsg);
            }

            // Cek apakah slot sudah dibooking lunas atau sedang ditahan oleh pending pengguna lain yang belum kedaluwarsa 15 menit
            $conflictBooking = BookingDetail::where('field_id', $field->id)
                ->whereDate('tanggal_sewa', $tanggalSewa)
                ->where(function ($q) use ($jamMulai, $jamSelesai) {
                    $q->where('jam_mulai', '<', $jamSelesai)
                      ->where('jam_selesai', '>', $jamMulai);
                })
                ->whereHas('header', function ($q) use ($now, $user) {
                    $q->where(function ($sub) use ($now, $user) {
                        $sub->whereIn('status', ['payment', 'completed'])
                            ->orWhere(function ($pendingQ) use ($now, $user) {
                                $pendingQ->where('status', 'pending')
                                         ->where('penyewa_id', '!=', $user->id)
                                         ->where(function ($exp) use ($now) {
                                             $exp->where('expires_at', '>', $now)
                                                 ->orWhere(function ($coalesce) use ($now) {
                                                     $coalesce->whereNull('expires_at')
                                                              ->where('created_at', '>', $now->copy()->subMinutes(15));
                                                 });
                                         });
                            });
                    });
                })
                ->first();

            if ($conflictBooking) {
                $errorMsg = "Slot jam {$startStr} sudah dipesan atau sedang dalam proses pembayaran oleh pengguna lain.";
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $errorMsg], 422);
                }
                return redirect()->route('penyewa.kalender', ['id' => $field->id, 'date' => $tanggalSewa])
                    ->with('warning', $errorMsg);
            }
        }

        // Cek apakah pengguna sudah memiliki booking pending aktif untuk slot yang sama (jangan reset 15 menit jika sudah ada)
        $existingBooking = BookingHeader::where('penyewa_id', $user->id)
            ->where('status', 'pending')
            ->where(function ($q) use ($now) {
                $q->where('expires_at', '>', $now)
                  ->orWhere(function ($sub) use ($now) {
                      $sub->whereNull('expires_at')
                          ->where('created_at', '>', $now->copy()->subMinutes(15));
                  });
            })
            ->whereHas('details', function ($q) use ($field, $tanggalSewa) {
                $q->where('field_id', $field->id)
                  ->whereDate('tanggal_sewa', $tanggalSewa);
            })
            ->latest('id')
            ->first();

        if ($existingBooking) {
            $booking = $existingBooking;
            $rawExpires = $booking->getRawOriginal('expires_at');
            $rawCreated = $booking->getRawOriginal('created_at');
            $expiresAt = $rawExpires
                ? Carbon::parse($rawExpires, $tz)
                : ($rawCreated ? Carbon::parse($rawCreated, $tz)->addMinutes(15) : $now->copy()->addMinutes(15));
        } else {
            // Kunci slot selama tepat 15 menit sejak pertama kali menekan Bayar Sekarang
            $sessionKey = 'checkout_expires_at_' . $field->id;
            if (session()->has($sessionKey) && (int) session($sessionKey) > $now->timestamp) {
                $expiresAt = Carbon::createFromTimestamp((int) session($sessionKey), $tz);
            } else {
                $expiresAt = $now->copy()->addMinutes(15);
            }

            // Buat record booking baru dengan status 'pending' (slot ditahan sementara hingga batas expires_at)
            $booking = BookingHeader::create([
                'penyewa_id'    => $user->id,
                'total_tagihan' => $totalBayar,
                'status'        => 'pending',
                'expires_at'    => $expiresAt,
                'created_at'    => $now,
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
                    'created_at'   => $now,
                ]);
            }
        }

        // Siapkan order_id unik untuk transaksi Midtrans
        $orderId = 'SP-' . $booking->id . '-' . time();

        // Rincian item untuk parameter Snap Midtrans
        $itemDetails = [];
        foreach ($parsedSlots as $slot) {
            $itemDetails[] = [
                'id'       => 'SLOT-' . $field->id . '-' . str_replace(':', '', $slot['start']),
                'price'    => (int) $slot['price'],
                'quantity' => 1,
                'name'     => substr("Sewa {$field->nama_lapangan} ({$slot['time']})", 0, 50),
            ];
        }
        $itemDetails[] = [
            'id'       => 'FEE-PLATFORM',
            'price'    => (int) $serviceFee,
            'quantity' => 1,
            'name'     => 'Biaya Layanan Platform',
        ];

        // Buat Snap Transaction token dari Midtrans API
        $snapToken = null;
        $midtransService = app(MidtransService::class);
        if ($midtransService->isConfigured()) {
            try {
                $midtransPayload = [
                    'transaction_details' => [
                        'order_id'     => $orderId,
                        'gross_amount' => (int) $totalBayar,
                    ],
                    'customer_details' => [
                        'first_name' => $user->name ?? 'Customer Budi',
                        'email'      => $user->email ?? 'customer@sportplace.com',
                        'phone'      => $user->no_hp ?? '081234567890',
                    ],
                    'item_details' => $itemDetails,
                    'callbacks'    => [
                        'finish' => route('penyewa.booking.detail', ['id' => $booking->id]) . '?paid=1',
                    ],
                ];
                $snapToken = $midtransService->createSnapToken($midtransPayload);
            } catch (\Throwable $e) {
                Log::error('Gagal generate Midtrans Snap token: ' . $e->getMessage());
            }
        }

        // Catat di tabel payments dengan status 'pending'
        Payment::create([
            'header_id'         => $booking->id,
            'pg_transaction_id' => $orderId,
            'metode_pembayaran' => 'Midtrans',
            'nominal_bayar'     => $totalBayar,
            'status_pembayaran' => 'pending',
            'created_at'        => $now,
        ]);

        // Simpan field ID dan state ke session
        session([
            'selected_field_id'  => $field->id,
            'last_field_id'      => $field->id,
            'selected_date'      => $tanggalSewa,
            'selected_slots'     => $parsedSlots,
            'latest_snap_token'  => $snapToken,
            'booking_expires_at' => $expiresAt->timestamp,
            'pending_booking_id' => $booking->id,
        ]);

        $remainingSeconds = max(0, (int) $expiresAt->timestamp - $now->timestamp);

        // Jika request via AJAX / Fetch (dari Snap popup handler)
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'              => true,
                'snap_token'           => $snapToken,
                'booking_id'           => $booking->id,
                'expires_at_timestamp' => $expiresAt->timestamp,
                'remaining_seconds'    => $remainingSeconds,
                'redirect_url'         => route('penyewa.booking.detail', ['id' => $booking->id]),
            ]);
        }

        return redirect()->route('penyewa.booking.detail', ['id' => $booking->id])
            ->with('info', 'Pesanan berhasil dibuat. Silakan selesaikan pembayaran via Midtrans.');
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
        $promoDiscount = 0;
        $totalBayar = (float) $booking->total_tagihan;

        // Auto-sinkronisasi status pembayaran dengan Midtrans jika ada query ?paid=1 atau status masih pending
        if ($booking->payment) {
            $isPaidQuery = $request?->query('paid') == '1';
            $isPendingStatus = !in_array(strtolower($booking->status), ['payment', 'complated', 'completed', 'canceled', 'cancelled']);

            if ($isPaidQuery) {
                $booking->payment->update([
                    'status_pembayaran' => 'succes',
                    'paid_at'           => now(),
                ]);
                $booking->update(['status' => 'payment']);
                $booking->refresh();
            } elseif ($isPendingStatus) {
                $midtransService = app(MidtransService::class);
                if ($midtransService->isConfigured()) {
                    $orderId = $booking->payment->pg_transaction_id;
                    $midtransStatus = $midtransService->getStatus($orderId);
                    if ($midtransStatus) {
                        $trxStatus = $midtransStatus['transaction_status'] ?? '';
                        $fraudStatus = $midtransStatus['fraud_status'] ?? '';
                        $paymentType = $midtransStatus['payment_type'] ?? 'Midtrans';

                        if ($trxStatus === 'settlement' || ($trxStatus === 'capture' && $fraudStatus === 'accept')) {
                            $booking->payment->update([
                                'status_pembayaran' => 'succes',
                                'paid_at'           => now(),
                                'metode_pembayaran' => "Midtrans ({$paymentType})",
                            ]);
                            $booking->update(['status' => 'payment']);
                            $booking->refresh();
                        } elseif (in_array($trxStatus, ['cancel', 'deny', 'expire'])) {
                            $booking->payment->update(['status_pembayaran' => 'failed']);
                            $booking->update(['status' => 'canceled']);
                            $booking->refresh();
                        }
                    }
                }
            }
        }

        // Hitung batas waktu bayar 15 menit (persisten dihitung dari expires_at database)
        $tz = config('app.timezone', 'Asia/Makassar');
        $now = Carbon::now($tz);
        $rawExpires = $booking->getRawOriginal('expires_at');
        $rawCreated = $booking->getRawOriginal('created_at');

        if ($rawExpires) {
            $expiresAt = Carbon::parse($rawExpires, $tz);
        } elseif ($rawCreated) {
            $expiresAt = Carbon::parse($rawCreated, $tz)->addMinutes(15);
        } else {
            $expiresAt = $now->copy()->addMinutes(15);
        }

        $expiresAtTimestamp = $expiresAt->timestamp;
        $remainingSeconds = max(0, (int) $expiresAtTimestamp - $now->timestamp);

        // Jika booking masih pending dan batas waktu 15 menit sudah lewat, ubah status menjadi expired
        if ($booking->status === 'pending' && $remainingSeconds <= 0) {
            $booking->update(['status' => 'expired']);
            $booking->refresh();
        }

        // Payment Method & Status
        $paymentMethod = $booking->payment?->metode_pembayaran ?? 'Midtrans Payment Gateway';
        $isPaid = in_array(strtolower($booking->status), ['payment', 'complated', 'completed']);
        $isCancelled = in_array(strtolower($booking->status), ['cancelled', 'canceled', 'expired']);
        $snapToken = session('latest_snap_token');

        $statusLabel = match(strtolower($booking->status)) {
            'payment', 'complated', 'completed' => 'Terkonfirmasi',
            'pending'               => 'Menunggu Pembayaran',
            'expired'               => 'Kedaluwarsa (15 Menit Habis)',
            'cancelled', 'canceled' => 'Dibatalkan',
            default                 => 'Terkonfirmasi',
        };

        return view('penyewa.pembayaran', [
            'booking'             => $booking,
            'customer'            => $customer,
            'field'               => $field,
            'venue'               => $venue,
            'sport'               => $sport,
            'bookingCode'         => $bookingCode,
            'ticketCode'          => $ticketCode,
            'trxId'               => $trxId,
            'dipesanPada'         => $dipesanPada,
            'tanggalMain'         => $tanggalMain,
            'sesiDurasi'          => $sesiDurasi,
            'berlakuHingga'       => $berlakuHingga,
            'courtPhoto'          => $courtPhoto,
            'facilities'          => $facilities,
            'lineItems'           => $lineItems,
            'serviceFee'          => $serviceFee,
            'promoDiscount'       => $promoDiscount,
            'totalBayar'          => $totalBayar,
            'paymentMethod'       => $paymentMethod,
            'isPaid'              => $isPaid,
            'isCancelled'         => $isCancelled,
            'statusLabel'         => $statusLabel,
            'snapToken'           => $snapToken,
            'remainingSeconds'    => $remainingSeconds,
            'expiresAtTimestamp'  => $expiresAtTimestamp,
        ]);
    }

    /**
     * Endpoint API untuk mengecek status pembayaran booking secara realtime (auto-polling frontend).
     */
    public function checkPaymentStatus(int $id, Request $request): JsonResponse
    {
        $booking = BookingHeader::with('payment')->find($id);

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        // 1. Cek jika sudah lunas
        if (in_array(strtolower($booking->status), ['payment', 'completed', 'complated'])) {
            return response()->json([
                'success'  => true,
                'status'   => 'payment',
                'is_paid'  => true,
                'redirect' => route('penyewa.booking.detail', ['id' => $booking->id]) . '?paid=1',
            ]);
        }

        // 2. Jika pending, tanyakan langsung status terbaru ke Midtrans API
        if ($booking->payment && $booking->status === 'pending') {
            $midtransService = app(MidtransService::class);
            if ($midtransService->isConfigured()) {
                $orderId = $booking->payment->pg_transaction_id;
                $midtransStatus = $midtransService->getStatus($orderId);
                if ($midtransStatus) {
                    $trxStatus = $midtransStatus['transaction_status'] ?? '';
                    $fraudStatus = $midtransStatus['fraud_status'] ?? '';
                    $paymentType = $midtransStatus['payment_type'] ?? 'Midtrans';

                    if ($trxStatus === 'settlement' || ($trxStatus === 'capture' && $fraudStatus === 'accept')) {
                        $booking->payment->update([
                            'status_pembayaran' => 'succes',
                            'paid_at'           => now(),
                            'metode_pembayaran' => "Midtrans ({$paymentType})",
                        ]);
                        $booking->update(['status' => 'payment']);
                        $booking->refresh();

                        return response()->json([
                            'success'  => true,
                            'status'   => 'payment',
                            'is_paid'  => true,
                            'redirect' => route('penyewa.booking.detail', ['id' => $booking->id]) . '?paid=1',
                        ]);
                    } elseif (in_array($trxStatus, ['cancel', 'deny', 'expire'])) {
                        $booking->payment->update(['status_pembayaran' => 'failed']);
                        $booking->update(['status' => 'canceled']);
                        return response()->json([
                            'success' => true,
                            'status'  => 'canceled',
                            'is_paid' => false,
                        ]);
                    }
                }
            }
        }

        // 3. Cek batas waktu 15 menit
        $tz = config('app.timezone', 'Asia/Makassar');
        $now = Carbon::now($tz);
        $expiresAt = $booking->expires_at
            ? Carbon::parse($booking->expires_at, $tz)
            : Carbon::parse($booking->created_at, $tz)->addMinutes(15);
        $remainingSeconds = max(0, (int) $now->diffInSeconds($expiresAt, false));

        if ($booking->status === 'pending' && $remainingSeconds <= 0) {
            $booking->update(['status' => 'expired']);
            return response()->json([
                'success'           => true,
                'status'            => 'expired',
                'is_paid'           => false,
                'is_expired'        => true,
                'remaining_seconds' => 0,
            ]);
        }

        return response()->json([
            'success'              => true,
            'status'               => $booking->status,
            'is_paid'              => false,
            'remaining_seconds'    => $remainingSeconds,
            'expires_at_timestamp' => $expiresAt->timestamp,
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

    /**
     * Webhook HTTP Notification dari Midtrans Gateway.
     * Dipanggil secara asinkron oleh server Midtrans untuk sinkronisasi status pembayaran realtime.
     */
    public function handleNotification(Request $request): JsonResponse
    {
        $payload = $request->all();
        $orderId = $payload['order_id'] ?? null;
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');

        $midtransService = app(MidtransService::class);

        // Verifikasi keaslian webhook notification
        if (!$midtransService->verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            Log::warning('Midtrans Notification: Signature tidak valid', $payload);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $payment = Payment::where('pg_transaction_id', $orderId)->first();
        if (!$payment) {
            Log::warning("Midtrans Notification: Transaksi {$orderId} tidak ditemukan");
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? '';
        $paymentType = $payload['payment_type'] ?? 'Midtrans';

        $booking = $payment->header;

        if ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $fraudStatus === 'accept')) {
            $payment->update([
                'status_pembayaran' => 'succes',
                'paid_at'           => now(),
                'metode_pembayaran' => "Midtrans ({$paymentType})",
            ]);
            $booking?->update(['status' => 'payment']);
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $payment->update(['status_pembayaran' => 'failed']);
            $booking?->update(['status' => 'canceled']);
        } elseif ($transactionStatus === 'pending') {
            $payment->update(['status_pembayaran' => 'pending']);
            $booking?->update(['status' => 'pending']);
        }

        return response()->json(['status' => 'success']);
    }
}
