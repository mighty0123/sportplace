<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use App\Models\BookingDetail;
use App\Models\BookingHeader;
use App\Models\Field;
use App\Models\Payment;
use App\Models\PaymentWebhookEvent;
use App\Models\Refund;
use App\Models\User;
use App\Services\MidtransService;
use App\Support\BookingCode;
use App\Services\Payment\PaymentSettlementService;
use App\Services\Payment\SettlementResult;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Exceptions\Booking\SlotUnavailableException;
use App\Services\Booking\SlotReservationService;

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

        $user = auth()->user();
        abort_if(! $user, 401);
        $booking = null;
        $field = null;

        // 1. Ambil field_id dari parameter query / request / route ID
        $requestedFieldId = (int) (
            $request->input('field_id') ??
            $request->query('field_id') ??
            $request->query('field') ??
            0
        );

        // Jika ada route parameter $id, periksa apakah $id adalah ID Field yang valid
        if ($id > 0) {
            $fieldCandidate = Field::with(['venue.city', 'sport', 'facilities', 'photos'])->find($id);
            if ($fieldCandidate) {
                $field = $fieldCandidate;
            } else {
                // Jika bukan ID Field, cari sebagai BookingHeader MILIK PENGGUNA ini
                $bookingCandidate = BookingHeader::with([
                    'details.field.venue.city',
                    'details.field.sport',
                    'details.field.facilities',
                    'details.field.photos',
                    'user'
                ])->where('id', $id)->where('penyewa_id', $user->id)->first();

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

        // 3. Jika field belum ditentukan, ambil dari session (lapangan yang baru saja dipilih pengguna ini)
        if (!$field && (session()->has('selected_field_id') || session()->has('last_field_id'))) {
            $sessionFieldId = (int) (session('selected_field_id') ?? session('last_field_id'));
            if ($sessionFieldId > 0) {
                $field = Field::with(['venue.city', 'sport', 'facilities', 'photos'])->find($sessionFieldId);
            }
        }

        // 4. Tidak ada lapangan cadangan: hanya lapangan yang layak dipesan yang boleh diteruskan.
        if (!$field || ! $this->isBookable($field->id)) {
            $msg = 'Lapangan tidak tersedia untuk dipesan.';
            return redirect()->route('penyewa.lapangan')->with('warning', $msg);
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

        // Biaya
        $serviceFee = (int) config('booking.service_fee', 2500);
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

        $paymentWindowMinutes = (int) config('booking.payment_window_minutes', 15);

        if ($booking && $booking->status === 'pending') {
            $rawExpires = $booking->getRawOriginal('expires_at');
            $rawCreated = $booking->getRawOriginal('created_at');
            $expiresAt = $rawExpires
                ? Carbon::parse($rawExpires, $tz)
                : ($rawCreated ? Carbon::parse($rawCreated, $tz)->addMinutes($paymentWindowMinutes) : null);
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

            // Jika belum ada atau sudah kedaluwarsa, tentukan anchor batas bayar baru
            if (!$expiresAt) {
                $expiresAt = $now->copy()->addMinutes($paymentWindowMinutes);
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
            'totalBayar'          => $totalBayar,
            'slotsPayload'        => $slotsPayload,
            'expiresAtTimestamp'  => $expiresAtTimestamp,
            'remainingSeconds'    => $remainingSeconds,
        ]);
    }

    public function processPayment(?int $id = null, ?Request $request = null): RedirectResponse|JsonResponse
    {
        $request = $request ?? request();
        $user = auth()->user();
        abort_if(! $user, 401);

        // Ambil field_id yang dipilih dari request, parameter rute, atau session
        $fieldId = (int) ($request->input('field_id') ?? $request->query('field_id') ?? $id ?? 0);
        if ($fieldId <= 0 || ! $this->isBookable($fieldId)) {
            $msg = 'Lapangan tidak tersedia untuk dipesan.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 404);
            }
            return redirect()->route('penyewa.lapangan')->with('warning', $msg);
        }
        $field = Field::with(['venue.city', 'sport'])->findOrFail($fieldId);

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
        $serviceFee = (int) config('booking.service_fee', 2500);
        $calculatedTotal = max(0, $subtotal + $serviceFee);

        $totalBayar = $calculatedTotal; // total selalu dihitung server; total_bayar dari formulir diabaikan

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

        $paymentWindowMinutes = (int) config('booking.payment_window_minutes', 15);

        if ($existingBooking) {
            $booking = $existingBooking;
            if (empty($booking->kode_booking)) {
                $booking->update(['kode_booking' => BookingCode::unique()]);
            }
            $rawExpires = $booking->getRawOriginal('expires_at');
            $rawCreated = $booking->getRawOriginal('created_at');
            $expiresAt = $rawExpires
                ? Carbon::parse($rawExpires, $tz)
                : ($rawCreated ? Carbon::parse($rawCreated, $tz)->addMinutes($paymentWindowMinutes) : $now->copy()->addMinutes($paymentWindowMinutes));
        } else {
            $serviceFee = (int) config('booking.service_fee', 2500);

            try {
                $booking = DB::transaction(function () use ($user, $field, $parsedSlots, $tanggalSewa, $subtotal, $serviceFee, $totalBayar, $now, $paymentWindowMinutes) {
                    $booking = BookingHeader::create([
                        'penyewa_id'    => $user->id,
                        'kode_booking'  => BookingCode::unique(),
                        'subtotal_slot' => $subtotal,
                        'biaya_layanan' => $serviceFee,
                        'total_tagihan' => $totalBayar,
                        'status'        => 'pending',
                        'expires_at'    => $now->copy()->addMinutes($paymentWindowMinutes),
                        'created_at'    => $now,
                    ]);

                    foreach ($parsedSlots as $slot) {
                        BookingDetail::create([
                            'header_id'    => $booking->id,
                            'field_id'     => $field->id,
                            'tanggal_sewa' => $tanggalSewa,
                            'jam_mulai'    => strlen($slot['start']) === 5 ? $slot['start'] . ':00' : $slot['start'],
                            'jam_selesai'  => strlen($slot['end']) === 5 ? $slot['end'] . ':00' : $slot['end'],
                            'harga_slot'   => (float) $slot['price'],
                            'status_item'  => 'ongoing',
                            'created_at'   => $now,
                        ]);
                    }

                    // Indeks unik di database yang memutuskan siapa yang dapat. Gagal berarti seluruh transaksi dibatalkan.
                    app(SlotReservationService::class)->reserve($booking->id);

                    return $booking;
                }, 3);
            } catch (SlotUnavailableException $e) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
                }

                return redirect()->route('penyewa.kalender', ['id' => $field->id, 'date' => $tanggalSewa])->with('warning', $e->getMessage());
            }

            $expiresAt = Carbon::parse($booking->getRawOriginal('expires_at'), $tz);
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
    public function show($id = null, ?Request $request = null): View
    {
        Carbon::setLocale('id');
        $request = $request ?? request();

        $user = auth()->user();
        abort_if(! $user, 401);

        // 1. Ambil booking MILIK PENGGUNA yang sedang masuk. Milik orang lain dijawab 404.
        $relations = [
            'details.field.venue.city',
            'details.field.sport',
            'details.field.facilities',
            'details.field.photos',
            'payment',
            'user',
        ];

        if ($id) {
            abort_unless(is_numeric($id), 404);
            $booking = $this->ownedBooking((int) $id, $relations);
        } else {
            // Tanpa ID: booking terbaru milik pengguna ini saja.
            $booking = BookingHeader::with($relations)
                ->where('penyewa_id', $user->id)
                ->latest('id')
                ->firstOrFail();
        }

        // 2. Format Data Header & Metadata Transaksi
        $detailFirst = $booking->details->first();
        $field = $detailFirst?->field;
        $venue = $field?->venue;
        $sport = $field?->sport;
        $customer = $booking->user ?? $user;

        $bookingCode = !empty($booking->kode_booking)
            ? $booking->kode_booking
            : ('SP-' . Carbon::parse($booking->created_at)->format('Ymd') . '-' . str_pad($booking->id, 3, '0', STR_PAD_LEFT));
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

        $serviceFee = (int) config('booking.service_fee', 2500);
        $totalBayar = (float) $booking->total_tagihan;

        // Halaman detail hanya membaca. Status pembayaran ditulis oleh webhook dan checkPaymentStatus
        // (yang memverifikasi ke Midtrans), bukan oleh query string seperti ?paid=1.

        // Hitung batas waktu bayar 15 menit (persisten dihitung dari expires_at database)
        $tz = config('app.timezone', 'Asia/Makassar');
        $now = Carbon::now($tz);
        $rawExpires = $booking->getRawOriginal('expires_at');
        $rawCreated = $booking->getRawOriginal('created_at');

        $paymentWindowMinutes = (int) config('booking.payment_window_minutes', 15);

        if ($rawExpires) {
            $expiresAt = Carbon::parse($rawExpires, $tz);
        } elseif ($rawCreated) {
            $expiresAt = Carbon::parse($rawCreated, $tz)->addMinutes($paymentWindowMinutes);
        } else {
            $expiresAt = $now->copy()->addMinutes($paymentWindowMinutes);
        }

        $expiresAtTimestamp = $expiresAt->timestamp;
        $remainingSeconds = max(0, (int) $expiresAtTimestamp - $now->timestamp);

        // Jika booking masih pending dan batas waktu pembayaran sudah lewat, ubah status menjadi expired
        if ($booking->status === 'pending' && $remainingSeconds <= 0) {
            $booking->update(['status' => 'expired']);
            $booking->refresh();
        }

        // Hitung kelayakan pembatalan sesuai config('booking.cancellation_min_hours')
        $minCancellationHours = (float) config('booking.cancellation_min_hours', 24);
        $refundPercent = (float) config('booking.refund_percent', 100);

        $earliestStartDateTime = null;
        if ($booking->details->isNotEmpty()) {
            foreach ($booking->details as $d) {
                $rDate = substr($d->getRawOriginal('tanggal_sewa') ?? (string) $d->tanggal_sewa, 0, 10);
                $rTime = substr($d->getRawOriginal('jam_mulai') ?? (string) $d->jam_mulai, 0, 8);
                $sDt = Carbon::parse("{$rDate} {$rTime}", $tz);
                if (!$earliestStartDateTime || $sDt->lt($earliestStartDateTime)) {
                    $earliestStartDateTime = $sDt;
                }
            }
        }

        $isPaid = in_array(strtolower($booking->status), ['payment', 'complated', 'completed']);
        $isCancelled = in_array(strtolower($booking->status), ['cancelled', 'canceled', 'expired']);
        $canCancel = false;
        if (!$isCancelled && $earliestStartDateTime) {
            $canCancel = $now->floatDiffInHours($earliestStartDateTime, false) >= $minCancellationHours;
        }

        // Payment Method & Status
        $paymentMethod = $booking->payment?->metode_pembayaran ?? 'Midtrans Payment Gateway';
        $snapToken = session('latest_snap_token');

        $statusLabel = match(strtolower($booking->status)) {
            'payment', 'complated', 'completed' => 'Terkonfirmasi',
            'pending'               => 'Menunggu Pembayaran',
            'expired'               => "Kedaluwarsa ({$paymentWindowMinutes} Menit Habis)",
            'cancelled', 'canceled' => 'Dibatalkan',
            default                 => 'Terkonfirmasi',
        };

        // Buat QR Code asli untuk tiket yang langsung membuka URL verifikasi tiket saat discan
        $qrTargetUrl = route('booking.verify', ['code' => $booking->kode_booking ?? $booking->id]);
        $qrCodeDataUri = null;
        try {
            $qrCodeDataUri = (new \chillerlan\QRCode\QRCode)->render($qrTargetUrl);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal render QR tiket: ' . $e->getMessage());
        }

        return view('penyewa.pembayaran', [
            'booking'              => $booking,
            'customer'             => $customer,
            'field'                => $field,
            'venue'                => $venue,
            'sport'                => $sport,
            'bookingCode'          => $bookingCode,
            'ticketCode'           => $ticketCode,
            'trxId'                => $trxId,
            'dipesanPada'          => $dipesanPada,
            'tanggalMain'          => $tanggalMain,
            'sesiDurasi'           => $sesiDurasi,
            'berlakuHingga'        => $berlakuHingga,
            'courtPhoto'           => $courtPhoto,
            'facilities'           => $facilities,
            'lineItems'            => $lineItems,
            'serviceFee'           => $serviceFee,
            'totalBayar'           => $totalBayar,
            'paymentMethod'        => $paymentMethod,
            'isPaid'               => $isPaid,
            'isCancelled'          => $isCancelled,
            'statusLabel'          => $statusLabel,
            'snapToken'            => $snapToken,
            'remainingSeconds'     => $remainingSeconds,
            'qrCodeDataUri'        => $qrCodeDataUri,
            'qrTargetUrl'          => $qrTargetUrl,
            'expiresAtTimestamp'   => $expiresAtTimestamp,
            'canCancel'            => $canCancel,
            'minCancellationHours' => $minCancellationHours,
            'refundPercent'        => $refundPercent,
        ]);
    }

    /**
     * Endpoint API untuk mengecek status pembayaran booking secara realtime (auto-polling frontend).
     */
    public function checkPaymentStatus(int $id, PaymentSettlementService $settlement): JsonResponse
    {
        $booking = $this->ownedBooking($id); // 404 bila bukan milik pengguna

        // Cadangan bila webhook terlambat; dibatasi sekali per interval per pesanan dan bisa dimatikan lewat konfigurasi.
        if ($booking->status === 'pending') {
            $settlement->reconcileHeader($booking->id);
        }

        $snap = $settlement->snapshot($booking->id);

        return response()->json([
            'success'              => true,
            'status'               => $snap['status'],
            'is_paid'              => $snap['is_paid'],
            'is_expired'           => $snap['is_expired'],
            'remaining_seconds'    => $snap['remaining_seconds'],
            'expires_at_timestamp' => $snap['expires_at_timestamp'],
            'redirect'             => $snap['is_paid'] ? route('penyewa.booking.detail', ['id' => $booking->id]) : null,
        ]);
    }

    /**
     * Batalkan pemesanan booking.
     * Sesuai ketentuan: pembatalan hanya boleh bila jam mulai slot masih lebih dari config('booking.cancellation_min_hours') jam lagi.
     * Pengembalian dana (refund) diproses sebesar config('booking.refund_percent') persen.
     */
    public function cancel(int $id): RedirectResponse|JsonResponse
    {
        $user = auth()->user();
        abort_if(! $user, 401);

        $booking = BookingHeader::with(['details', 'payment', 'user'])->find($id);
        if (!$booking) {
            $msg = 'Pemesanan tidak ditemukan.';
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 404);
            }
            return redirect()->route('penyewa.riwayat')->with('error', $msg);
        }

        if ((int) $booking->penyewa_id !== (int) $user->id) {
            $msg = 'Anda tidak memiliki hak untuk membatalkan pesanan ini.';
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return redirect()->route('penyewa.booking.detail', ['id' => $id])->with('error', $msg);
        }

        if (in_array(strtolower($booking->status), ['canceled', 'cancelled', 'expired'])) {
            $msg = 'Pemesanan ini sudah dibatalkan atau kedaluwarsa.';
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->route('penyewa.booking.detail', ['id' => $id])->with('warning', $msg);
        }

        $tz = config('app.timezone', 'Asia/Makassar');
        $now = Carbon::now($tz);

        // Ambil jam mulai slot paling awal dari booking
        $earliestStart = null;
        foreach ($booking->details as $detail) {
            $rawDate = substr($detail->getRawOriginal('tanggal_sewa') ?? (string) $detail->tanggal_sewa, 0, 10);
            $rawTime = substr($detail->getRawOriginal('jam_mulai') ?? (string) $detail->jam_mulai, 0, 8);
            $startDateTime = Carbon::parse("{$rawDate} {$rawTime}", $tz);
            if (!$earliestStart || $startDateTime->lt($earliestStart)) {
                $earliestStart = $startDateTime;
            }
        }

        $minHours = (float) config('booking.cancellation_min_hours', 24);

        if ($earliestStart) {
            $hoursLeft = $now->floatDiffInHours($earliestStart, false);
            if ($hoursLeft < $minHours) {
                $msg = "Pembatalan hanya boleh bila jam mulai slot masih lebih dari {$minHours} jam lagi.";
                if (request()->expectsJson() || request()->ajax()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return redirect()->route('penyewa.booking.detail', ['id' => $id])->with('error', $msg);
            }
        }

        // Ubah status booking menjadi canceled
        $booking->update(['status' => 'canceled']);
        app(SlotReservationService::class)->release($booking->id);
        BookingDetail::where('header_id', $id)->update(['status_item' => 'no_show']);

        // Refund sebesar config('booking.refund_percent') persen bila pesanan sudah dibayar
        $refundPercent = (float) config('booking.refund_percent', 100);
        if ($booking->isPaid() && $refundPercent > 0) {
            $totalTagihan = (float) $booking->total_tagihan;
            $nominalRefund = round(($totalTagihan * $refundPercent) / 100, 2);

            $payment = $booking->payment;
            $detail = $booking->details->first();

            Refund::create([
                'header_id'        => $booking->id,
                'detail_id'        => $detail?->id,
                'payment_id'       => $payment?->id,
                'alasan_refund'    => "Pembatalan pemesanan oleh penyewa (Refund {$refundPercent}%)",
                'nominal_refund'   => $nominalRefund,
                'metode_refund'    => $payment?->metode_pembayaran ?? 'Midtrans',
                'rekening_tujuan'  => $booking->user?->email ?? $booking->user?->no_hp ?? 'Akun Penyewa',
                'status_refund'    => 'pending',
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);

            $successMsg = "Pemesanan berhasil dibatalkan. Pengajuan refund sebesar {$refundPercent}% (Rp " . number_format($nominalRefund, 0, ',', '.') . ") telah diajukan.";
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json(['success' => true, 'message' => $successMsg]);
            }
            return redirect()->route('penyewa.booking.detail', ['id' => $id])->with('success', $successMsg);
        }

        $successMsg = 'Pemesanan berhasil dibatalkan.';
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => $successMsg]);
        }
        return redirect()->route('penyewa.booking.detail', ['id' => $id])->with('success', $successMsg);
    }

    /**
     * Unduh atau cetak invoice PDF pemesanan (Nota 1 Halaman Landscape).
     */
    public function invoice($id)
    {
        Carbon::setLocale('id');

        abort_unless(is_numeric($id), 404);
        $booking = $this->ownedBooking((int) $id, [
            'details.field.venue.city',
            'details.field.sport',
            'payment',
            'user',
        ]);

        // Generate QR Code untuk disematkan di nota
        $qrCodeDataUri = null;
        try {
            $qrUrl = route('booking.verify', ['code' => $booking->kode_booking ?? $booking->id]);
            $qrCodeDataUri = (new \chillerlan\QRCode\QRCode)->render($qrUrl);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal render QR invoice: ' . $e->getMessage());
        }

        // Render PDF satu halaman landscape A5
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('penyewa.invoice-pdf', [
                'booking'       => $booking,
                'qrCodeDataUri' => $qrCodeDataUri,
            ])->setPaper('a5', 'landscape');

            $filename = 'Nota-Booking-' . ($booking->kode_booking ?? $booking->id) . '.pdf';

            // Jika ada query ?download=1 maka download, default: stream di tab browser
            if (request()->query('download') == '1') {
                return $pdf->download($filename);
            }

            return $pdf->stream($filename);
        }

        return redirect()->route('penyewa.booking.detail', ['id' => $booking->id]);
    }

    /**
     * Halaman Verifikasi Tiket Publik (hasil scan QR Code).
     * Dapat dibuka langsung oleh siapapun yang memindai QR Code di kamera HP.
     */
    public function verifyTicket($code)
    {
        Carbon::setLocale('id');

        $booking = BookingHeader::with([
            'details.field.venue.city',
            'details.field.sport',
            'details.field.facilities',
            'payment',
            'user'
        ])->where('kode_booking', $code)
          ->orWhere('id', $code)
          ->firstOrFail();

        $qrCodeDataUri = null;
        try {
            $qrUrl = route('booking.verify', ['code' => $booking->kode_booking ?? $booking->id]);
            $qrCodeDataUri = (new \chillerlan\QRCode\QRCode)->render($qrUrl);
        } catch (\Throwable $e) {
            // ignore
        }

        $detailFirst = $booking->details->first();
        $field = $detailFirst?->field;
        $venue = $field?->venue;

        $isPaid = in_array(strtolower($booking->status), ['payment', 'completed', 'complated']);
        $isCheckedIn = !empty($booking->checked_in_at);

        return view('penyewa.tiket-verifikasi', [
            'booking'        => $booking,
            'field'          => $field,
            'venue'          => $venue,
            'isPaid'         => $isPaid,
            'isCheckedIn'    => $isCheckedIn,
            'qrCodeDataUri'  => $qrCodeDataUri,
        ]);
    }

    /**
     * Aksi Konfirmasi Check-in Tiket oleh Resepsionis / Petugas Venue.
     */
    public function checkInTicket(Request $request, $code)
    {
        Carbon::setLocale('id');

        $booking = BookingHeader::where('kode_booking', $code)
            ->orWhere('id', $code)
            ->firstOrFail();

        $isPaid = in_array(strtolower($booking->status), ['payment', 'completed', 'complated']);
        if (!$isPaid) {
            $msg = 'Gagal: Tiket belum dibayar / belum lunas.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        if ($booking->checked_in_at) {
            $timeFormatted = Carbon::parse($booking->checked_in_at)->translatedFormat('d M Y, H:i');
            $msg = "Perhatian: Tiket ini sudah pernah di-check-in sebelumnya pada {$timeFormatted} WIB.";
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 409);
            }
            return back()->with('warning', $msg);
        }

        $booking->update([
            'checked_in_at' => now(),
        ]);

        $timeNow = now()->translatedFormat('d M Y, H:i');
        $successMsg = "Berhasil! Check-in diverifikasi pada {$timeNow} WIB. Selamat bermain!";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'       => true,
                'message'       => $successMsg,
                'checked_in_at' => $booking->checked_in_at,
            ]);
        }

        return back()->with('success', $successMsg);
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

            if (is_array($item)) {
                $start = $item['start'] ?? $item['jam_mulai'] ?? null;
                $end = $item['end'] ?? $item['jam_selesai'] ?? null;

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
                }
            }

            if ($start && $end) {
                $startParts = explode(':', $start);
                $endParts = explode(':', $end);
                $startNorm = sprintf('%02d:%02d', (int) $startParts[0], (int) ($startParts[1] ?? 0));
                $endNorm = sprintf('%02d:%02d', (int) $endParts[0], (int) ($endParts[1] ?? 0));

                $hours = max(1, (strtotime($endNorm) - strtotime($startNorm)) / 3600);

                $slots[] = [
                    'start' => $startNorm,
                    'end'   => $endNorm,
                    'time'  => "{$startNorm} - {$endNorm}",
                    'price' => $defaultPrice * $hours,   // harga dari lapangan di database; masukan pengguna diabaikan
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
     * Alur:
     * 1. Simpan kiriman mentah ke payment_webhook_events (unik per provider + event_id).
     *    Kiriman ganda berakhir tanpa efek.
     * 2. Verifikasi tanda tangan gateway.
     * 3. Ambil ulang status transaksi dari API gateway Midtrans.
     * 4. Ubah status payments dan booking_headers.
     *    Bila pembayaran tiba setelah booking kedaluwarsa:
     *    - Jika slot masih kosong: booking dihidupkan kembali (status: payment).
     *    - Jika slot sudah dipesan orang lain: buat refund penuh (100%).
     */
    public function handleNotification(Request $request, PaymentSettlementService $settlement, MidtransService $midtrans): JsonResponse
    {
        $payload   = $request->all();
        $orderId   = (string) ($payload['order_id'] ?? '');
        $trxStatus = (string) ($payload['transaction_status'] ?? '');

        $eventId = (string) ($request->input('event_id') ?? $request->input('id') ?? $request->header('X-Event-Id') ?? '');
        if ($eventId === '') {
            $transactionId = (string) ($payload['transaction_id'] ?? '');
            $eventId = $transactionId !== '' ? "{$transactionId}:{$trxStatus}" : "{$orderId}:{$trxStatus}";
        }

        // Kiriman yang sudah selesai diproses diabaikan. Yang gagal diproses sebelumnya boleh dicoba lagi.
        $event = PaymentWebhookEvent::where('provider', 'midtrans')->where('event_id', $eventId)->first();
        if ($event && $event->process_status === 'processed') {
            return response()->json(['status' => 'ignored', 'message' => 'Duplicate webhook event ignored without effect']);
        }
        if (! $event) {
            try {
                $event = PaymentWebhookEvent::create([
                    'provider' => 'midtrans', 'event_id' => $eventId, 'pg_transaction_id' => $orderId ?: null,
                    'event_type' => $trxStatus ?: null, 'signature_valid' => false, 'payload' => $payload,
                    'process_status' => 'received', 'received_at' => now(),
                ]);
            } catch (UniqueConstraintViolationException|QueryException) {
                return response()->json(['status' => 'ignored', 'message' => 'Duplicate webhook event ignored without effect']);
            }
        }

        $valid = $midtrans->verifySignature(
            $orderId, (string) ($payload['status_code'] ?? ''), (string) ($payload['gross_amount'] ?? ''), (string) ($payload['signature_key'] ?? '')
        );
        if (! $valid) {
            Log::warning("Midtrans Notification: tanda tangan tidak valid untuk event {$eventId}");
            $event->update(['signature_valid' => false, 'process_status' => 'failed', 'error' => 'Invalid signature']);

            return response()->json(['message' => 'Invalid signature'], 403);
        }
        $event->update(['signature_valid' => true]);

        // Status TIDAK dibaca dari isi kiriman: tanda tangan tidak mencakupnya. Layanan menanyakan ulang ke API Midtrans.
        $result = $settlement->settleFromGateway($orderId, 'webhook');

        $event->update([
            'payment_id'     => DB::table('payments')->where('pg_transaction_id', $orderId)->value('id'),
            'process_status' => match ($result->outcome) {
                SettlementResult::UNKNOWN, SettlementResult::IGNORED => 'ignored',
                SettlementResult::DEFERRED, SettlementResult::MISMATCH, SettlementResult::REVIEW => 'failed',
                default => 'processed',
            },
            'error'          => $result->needsAttention() ? $result->message : null,
            'processed_at'   => now(),
        ]);

        // Midtrans mengulang kiriman bila balasan bukan 2xx. Hanya "belum bisa diverifikasi" yang pantas diulang.
        return response()->json(['status' => $result->outcome, 'message' => $result->message], $result->shouldRetry() ? 503 : 200);
    }

    // ---- helpers

    /** Booking milik penyewa yang sedang masuk. Milik orang lain dijawab 404 supaya nomor booking tidak bisa ditebak. */
    private function ownedBooking(int $id, array $with = []): BookingHeader
    {
        $user = auth()->user();
        abort_if(! $user, 401);

        return BookingHeader::with($with)->where('id', $id)->where('penyewa_id', $user->id)->firstOrFail();
    }

    /** Bisa dipesan hanya bila lapangan, venue, dan mitra aktif, dan akun mitra tidak disanksi. */
    private function isBookable(int $fieldId): bool
    {
        return DB::table('fields as f')
            ->join('venues as v', 'v.id', '=', 'f.venue_id')
            ->join('mitra_profiles as m', 'm.id', '=', 'v.mitra_id')
            ->join('users as u', 'u.id', '=', 'm.user_id')
            ->where('f.id', $fieldId)->where('f.status_lapangan', 'aktif')->where('v.status_vanue', 'aktif')
            ->where('m.status', 'approved')->where('u.status_akun', 'aktif')
            ->exists();
    }
}