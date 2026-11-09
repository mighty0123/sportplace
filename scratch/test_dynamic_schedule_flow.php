<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

use App\Models\User;
use App\Models\Field;
use App\Models\BookingHeader;
use App\Models\BookingDetail;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Penyewa\PaymentController;

echo "=== START VERIFICATION: DYNAMIC SCHEDULE & SLOTS IN PAYMENT ===\n\n";

$user = User::where('role', 'customer')->first();
auth()->login($user);

$field = Field::with(['venue', 'sport'])->find(2); // Futsal Lapangan Sintetis B
echo "Field: #{$field->id} {$field->nama_lapangan} ({$field->sport->nama_olahraga})\n";

$controller = new PaymentController();

// TEST 1: 1 Slot Selection (e.g. 10:00 - 11:00 on 2024-11-05)
echo "\n--- TEST 1: Review 1 Slot (10:00 - 11:00) ---\n";
$req1 = Request::create('/penyewa/pembayaran', 'GET', [
    'field_id' => 2,
    'date'     => '2024-11-05',
    'slots'    => '10:00 - 11:00:150000',
]);

$view1 = $controller->review(null, $req1);
$data1 = $view1->getData();

echo "Tanggal Main : " . $data1['tanggalMain'] . "\n";
echo "Sesi Durasi  : " . $data1['sesiDurasi'] . "\n";
echo "Total Jam    : " . $data1['totalJam'] . " Jam\n";
echo "Subtotal     : Rp " . number_format($data1['subtotal'], 0, ',', '.') . "\n";
echo "Total Bayar  : Rp " . number_format($data1['totalBayar'], 0, ',', '.') . "\n";
echo "Line Items   : " . json_encode($data1['lineItems'], JSON_PRETTY_PRINT) . "\n";

assert(str_contains($data1['sesiDurasi'], '10:00 - 11:00'), "Sesi durasi must be 10:00 - 11:00");
assert($data1['totalJam'] === 1, "Total jam must be 1");
assert(count($data1['lineItems']) === 1, "Must have 1 line item");
echo "-> TEST 1 PASSED!\n";

// TEST 2: Process Payment for Test 1
echo "\n--- TEST 2: Process Payment for 1 Slot ---\n";
$reqPay1 = Request::create('/penyewa/bayar', 'POST', [
    'field_id'          => 2,
    'tanggal_sewa'      => '2024-11-05',
    'slots'             => $data1['slotsPayload'],
    'total_bayar'       => $data1['totalBayar'],
    'metode_pembayaran' => 'BCA Virtual Account',
]);

$redirect1 = $controller->processPayment(null, $reqPay1);
echo "Redirect Target: " . $redirect1->getTargetUrl() . "\n";

// Ambil booking yang baru dibuat
$newBooking1 = BookingHeader::with(['details', 'payment'])->latest('id')->first();
echo "Created Booking ID: #{$newBooking1->id}\n";
echo "Booking Total Tagihan: Rp " . number_format($newBooking1->total_tagihan, 0, ',', '.') . "\n";
echo "Booking Details Count: " . $newBooking1->details->count() . "\n";
foreach ($newBooking1->details as $d) {
    echo "  - Detail #{$d->id}: Tanggal={$d->tanggal_sewa}, Jam={$d->jam_mulai} to {$d->jam_selesai}, Harga={$d->harga_slot}\n";
}

assert($newBooking1->details->first()->jam_mulai === '10:00:00', "Jam mulai must be 10:00:00");
assert($newBooking1->details->first()->jam_selesai === '11:00:00', "Jam selesai must be 11:00:00");
assert(str_starts_with($newBooking1->details->first()->tanggal_sewa, '2024-11-05'), "Tanggal sewa must start with 2024-11-05");
echo "-> TEST 2 PASSED!\n";

// TEST 3: Show (Halaman Pembayaran / Tiket) for newly created booking
echo "\n--- TEST 3: Show Confirmed Booking / Ticket Page ---\n";
$viewShow = $controller->show($newBooking1->id, Request::create('/penyewa/booking/' . $newBooking1->id, 'GET'));
$dataShow = $viewShow->getData();

echo "Tiket Sport       : " . $dataShow['sport']->nama_olahraga . "\n";
echo "Tiket Lapangan    : " . $dataShow['field']->nama_lapangan . "\n";
echo "Tiket Tanggal     : " . $dataShow['tanggalMain'] . "\n";
echo "Tiket Sesi Durasi : " . $dataShow['sesiDurasi'] . "\n";
echo "Berlaku Hingga    : " . $dataShow['berlakuHingga'] . "\n";

assert(str_contains($dataShow['sesiDurasi'], '10:00 - 11:00'), "Ticket duration must be 10:00 - 11:00");
assert($dataShow['sport']->slug === 'futsal', "Sport must stay futsal");
echo "-> TEST 3 PASSED!\n";

// TEST 4: Multiple Slots (e.g. 14:00 - 15:00 and 15:00 - 16:00 on 2024-11-08)
echo "\n--- TEST 4: Multiple Slots (14:00 - 15:00 and 15:00 - 16:00) ---\n";
$reqMulti = Request::create('/penyewa/pembayaran', 'GET', [
    'field_id' => 2,
    'date'     => '2024-11-08',
    'slots'    => '14:00 - 15:00:150000|15:00 - 16:00:150000',
]);

$viewMulti = $controller->review(null, $reqMulti);
$dataMulti = $viewMulti->getData();

echo "Tanggal Main : " . $dataMulti['tanggalMain'] . "\n";
echo "Sesi Durasi  : " . $dataMulti['sesiDurasi'] . "\n";
echo "Total Jam    : " . $dataMulti['totalJam'] . " Jam\n";
echo "Subtotal     : Rp " . number_format($dataMulti['subtotal'], 0, ',', '.') . "\n";
echo "Total Bayar  : Rp " . number_format($dataMulti['totalBayar'], 0, ',', '.') . "\n";
echo "Line Items Count: " . count($dataMulti['lineItems']) . "\n";

assert(str_contains($dataMulti['sesiDurasi'], '14:00 - 16:00'), "Multi slots duration must be 14:00 - 16:00");
assert($dataMulti['totalJam'] === 2, "Total jam must be 2");
assert(count($dataMulti['lineItems']) === 2, "Must have 2 line items");
assert($dataMulti['subtotal'] == 300000, "Subtotal must be 300000");

// Process payment for multi slots
$reqPayMulti = Request::create('/penyewa/bayar', 'POST', [
    'field_id'          => 2,
    'tanggal_sewa'      => '2024-11-08',
    'slots'             => $dataMulti['slotsPayload'],
    'total_bayar'       => $dataMulti['totalBayar'],
    'metode_pembayaran' => 'Mandiri Virtual Account',
]);
$redirectMulti = $controller->processPayment(null, $reqPayMulti);
$newBookingMulti = BookingHeader::with(['details', 'payment'])->latest('id')->first();

echo "Created Multi Booking ID: #{$newBookingMulti->id}\n";
echo "Details count: " . $newBookingMulti->details->count() . "\n";
foreach ($newBookingMulti->details as $d) {
    echo "  - Detail #{$d->id}: Tanggal={$d->tanggal_sewa}, Jam={$d->jam_mulai} to {$d->jam_selesai}\n";
}

assert($newBookingMulti->details->count() === 2, "Multi booking must have 2 detail records");
echo "-> TEST 4 PASSED!\n";

echo "\n=== ALL TESTS COMPLETED SUCCESSFULLY! ===\n";
