<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;

echo "Memulai render PDF...\n";

$pdf = Pdf::loadView('pdf.panduan-penyewa')
    ->setPaper('a4', 'portrait')
    ->setOption(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

$output = $pdf->output();

$destPublic = public_path('Panduan_Lengkap_Modul_Penyewa_SportPlace.pdf');
file_put_contents($destPublic, $output);

$destBrain = 'C:\\Users\\User\\.gemini\\antigravity\\brain\\67413a2d-7d0a-46df-a9b5-599d91c87734\\Panduan_Lengkap_Modul_Penyewa_SportPlace.pdf';
@copy($destPublic, $destBrain);

echo "BERHASIL!\n";
echo "File tersimpan di:\n";
echo "1. " . $destPublic . " (" . filesize($destPublic) . " bytes)\n";
echo "2. " . $destBrain . " (" . (file_exists($destBrain) ? filesize($destBrain) : 0) . " bytes)\n";
