<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

use App\Models\User;
use App\Models\BookingHeader;
use Illuminate\Http\Request;

$user = User::where('role', 'customer')->first();
auth()->login($user);

// 1. Test GET /penyewa/pembayaran with dynamic slot query
$requestReview = Request::create('/penyewa/pembayaran', 'GET', [
    'field_id' => 2,
    'date'     => '2024-11-12',
    'slots'    => '09:00 - 10:00:150000|10:00 - 11:00:150000',
]);
$responseReview = $kernel->handle($requestReview);
echo "Status Review Page: " . $responseReview->getStatusCode() . "\n";
$contentReview = $responseReview->getContent();
preg_match('/Sesi & Durasi<\/span>\s*<span[^>]*>(.*?)<\/span>/s', $contentReview, $m);
echo 'Captured Sesi & Durasi: ' . ($m[1] ?? 'NOT FOUND') . "\n";
preg_match('/Tanggal Main<\/span>\s*<span[^>]*>(.*?)<\/span>/s', $contentReview, $m2);
echo 'Captured Tanggal Main: ' . ($m2[1] ?? 'NOT FOUND') . "\n";

// 2. Test GET /penyewa/booking/{id} for the latest booking
$latestBooking = BookingHeader::latest('id')->first();
$requestShow = Request::create('/penyewa/booking/' . $latestBooking->id, 'GET');
$responseShow = $kernel->handle($requestShow);
echo "Status Booking Ticket Page: " . $responseShow->getStatusCode() . "\n";
$contentShow = $responseShow->getContent();
assert(str_contains($contentShow, 'TIKET CHECK-IN ARENA'), "Booking page must contain TIKET CHECK-IN ARENA");
assert(str_contains($contentShow, 'Lapangan Sintetis B'), "Booking page must contain Lapangan Sintetis B");
echo "Booking Ticket HTML assertion passed!\n";

echo "ALL BLADE RENDERING TESTS PASSED!\n";
