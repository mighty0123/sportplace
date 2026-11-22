<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Panduan Lengkap Modul Penyewa SportPlace</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 18mm 16mm 18mm 16mm;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            background: #ffffff;
            font-size: 9pt;
            line-height: 1.45;
        }
        .page-break {
            page-break-after: always;
        }
        .no-break {
            page-break-inside: avoid;
        }

        /* Cover Page */
        .cover {
            text-align: center;
            padding-top: 60px;
            padding-bottom: 40px;
        }
        .cover-badge {
            display: inline-block;
            background: #e6f4ea;
            color: #0a7660;
            font-weight: bold;
            font-size: 9pt;
            padding: 4px 14px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 25px;
            border: 1px solid #a3d9c9;
        }
        .cover-title {
            font-size: 26pt;
            font-weight: 900;
            color: #06231d;
            line-height: 1.2;
            margin-bottom: 15px;
            letter-spacing: -0.5px;
        }
        .cover-title span {
            color: #0a7660;
        }
        .cover-subtitle {
            font-size: 12pt;
            color: #475569;
            max-width: 500px;
            margin: 0 auto 35px auto;
            line-height: 1.5;
        }
        .cover-card {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px 25px;
            text-align: left;
            margin: 30px auto;
            max-width: 480px;
        }
        .cover-card-row {
            margin-bottom: 8px;
            font-size: 9.5pt;
        }
        .cover-card-label {
            display: inline-block;
            width: 130px;
            color: #64748b;
            font-weight: bold;
        }
        .cover-card-val {
            color: #0f172a;
            font-weight: bold;
        }

        /* Typography & Layout */
        h1.section-title {
            font-size: 15pt;
            font-weight: 800;
            color: #06231d;
            border-bottom: 2.5px solid #0a7660;
            padding-bottom: 6px;
            margin-top: 15px;
            margin-bottom: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        h2.sub-title {
            font-size: 11pt;
            font-weight: 800;
            color: #0a7660;
            margin-top: 14px;
            margin-bottom: 6px;
        }
        h3.part-title {
            font-size: 9.5pt;
            font-weight: 700;
            color: #0f172a;
            margin-top: 10px;
            margin-bottom: 4px;
        }
        p {
            margin-bottom: 8px;
            color: #334155;
            text-align: justify;
        }

        /* Tables */
        table.custom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 14px;
            font-size: 8pt;
        }
        table.custom-table th {
            background: #06231d;
            color: #ffffff;
            font-weight: 800;
            text-align: left;
            padding: 6px 8px;
            border: 1px solid #06231d;
            text-transform: uppercase;
            font-size: 7.5pt;
            letter-spacing: 0.3px;
        }
        table.custom-table td {
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
            line-height: 1.35;
        }
        table.custom-table tr:nth-child(even) td {
            background: #f8fafc;
        }

        /* Badges & Highlights */
        .badge {
            display: inline-block;
            font-size: 7.5pt;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .badge-green { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .badge-blue { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .badge-amber { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .badge-purple { background: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff; }

        .callout {
            background: #f0fdf4;
            border-left: 4px solid #0a7660;
            padding: 10px 12px;
            border-radius: 0 6px 6px 0;
            margin: 10px 0;
            font-size: 8.5pt;
        }
        .callout-title {
            font-weight: bold;
            color: #06231d;
            margin-bottom: 3px;
        }
        .callout-warning {
            background: #fffbeb;
            border-left-color: #d97706;
        }
        .callout-warning .callout-title {
            color: #92400e;
        }

        /* Code snippets */
        code {
            font-family: Consolas, 'Courier New', monospace;
            background: #f1f5f9;
            color: #0f172a;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 8pt;
            border: 1px solid #e2e8f0;
        }

        .header-doc {
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            margin-bottom: 12px;
            font-size: 7.5pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-doc .left { float: left; font-weight: bold; }
        .header-doc .right { float: right; }
        .clear { clear: both; }

        .footer-doc {
            position: fixed;
            bottom: -10mm;
            left: 0;
            right: 0;
            height: 8mm;
            border-top: 1px solid #e2e8f0;
            font-size: 7.5pt;
            color: #94a3b8;
            padding-top: 2px;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- ==================== COVER PAGE ==================== --}}
    <div class="cover">
        <div class="cover-badge">SportPlace Developer & Presentation Guide</div>
        <div class="cover-title">PANDUAN LENGKAP & CHEAT SHEET<br>MODUL PENYEWA (<span>CUSTOMER</span>)</div>
        <div class="cover-subtitle">
            Referensi Komprehensif Arsitektur Kode, Alur Bisnis, Interaksi Tombol, Integrasi Midtrans, Logika Database, dan Skrip Live Coding.
        </div>

        <div class="cover-card">
            <div class="cover-card-row">
                <span class="cover-card-label">Modul:</span>
                <span class="cover-card-val">Penyewa (Customer Booking & Payment)</span>
            </div>
            <div class="cover-card-row">
                <span class="cover-card-label">Framework:</span>
                <span class="cover-card-val">Laravel 12 / 13 & Tailwind CSS v4</span>
            </div>
            <div class="cover-card-row">
                <span class="cover-card-label">Payment Gateway:</span>
                <span class="cover-card-val">Midtrans Snap (Snapbox & Webhook HTTP)</span>
            </div>
            <div class="cover-card-row">
                <span class="cover-card-label">Fitur Kunci:</span>
                <span class="cover-card-val">Hold 15 Mnt, QR Tiket, Nota Landscape, Refund H-24</span>
            </div>
            <div class="cover-card-row">
                <span class="cover-card-label">Tujuan Dokumen:</span>
                <span class="cover-card-val">Persiapan Presentasi & Pengujian Live Coding</span>
            </div>
            <div class="cover-card-row" style="margin-bottom: 0;">
                <span class="cover-card-label">Status Tes:</span>
                <span class="cover-card-val" style="color: #0a7660;">17/17 Unit & Feature Tests Passed (100%)</span>
            </div>
        </div>
    </div>

    <div class="page-break"></div>

    {{-- ==================== BAGIAN 1: PETA ARSITEKTUR FILE ==================== --}}
    <div class="header-doc">
        <div class="left">SportPlace Developer Guide &bull; Modul Penyewa</div>
        <div class="right">Bagian 1: Peta Arsitektur File</div>
        <div class="clear"></div>
    </div>

    <h1 class="section-title">1. Peta Arsitektur File Modul Penyewa</h1>
    <p>
        Modul penyewa dibangun dengan pola <strong>MVC (Model-View-Controller)</strong> standar Laravel yang diperkuat dengan layer Service (Midtrans) dan Helper Support. Jika dosen/penguji meminta Anda membuka file tertentu, tabel berikut adalah peta acuan cepatnya:
    </p>

    <h2 class="sub-title">A. Controller (app/Http/Controllers/Penyewa/)</h2>
    <table class="custom-table">
        <thead>
            <tr>
                <th style="width: 28%;">Nama Controller & Method</th>
                <th style="width: 27%;">Route URL & Name</th>
                <th style="width: 45%;">Tanggung Jawab & Logika Bisnis Utama</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>CourtListingController</code><br>&bull; <code>index()</code><br>&bull; <code>show($id)</code></td>
                <td><code>GET /penyewa/lapangan</code><br><code>GET /penyewa/lapangan/{id}</code><br><em>(Publik)</em></td>
                <td>
                    Menampilkan katalog lapangan olahraga dengan filter cabang olahraga, kota, rentang harga, search keyword, dan detail fasilitas lapangan lengkap dengan galeri foto.
                </td>
            </tr>
            <tr>
                <td><code>CalendarController</code><br>&bull; <code>index()</code></td>
                <td><code>GET /penyewa/kalender/{id?}</code><br><code>name('penyewa.kalender')</code><br><em>(Publik / JSON sync)</em></td>
                <td>
                    Mengatur kalender jadwal slot jam. Menghitung status tiap jam (Available, Dipilih, Booked), mengunci jam_mulai yang sudah lewat, melepaskan slot hold pending yang expired, dan melayani sinkronisasi status via AJAX JSON.
                </td>
            </tr>
            <tr>
                <td><code>PaymentController</code><br>&bull; <code>review()</code></td>
                <td><code>GET /penyewa/review-pembayaran</code><br><code>name('penyewa.pembayaran.review')</code></td>
                <td>
                    Halaman konfirmasi sewa sebelum bayar. Menghitung rincian harga slot + biaya layanan platform Rp 2.500, menghitung sisa batas bayar 15 menit, serta menyiapkan parameter checkout.
                </td>
            </tr>
            <tr>
                <td><code>PaymentController</code><br>&bull; <code>processPayment()</code></td>
                <td><code>POST /penyewa/bayar/{id?}</code><br><code>name('penyewa.pembayaran.process')</code></td>
                <td>
                    <strong>Jantung Pembuatan Transaksi:</strong> Mengunci slot jam ke status <code>pending</code>, membuat <code>BookingHeader</code> dengan kode booking unik <code>BookingCode::unique()</code>, menyetel <code>expires_at = now() + 15 menit</code>, dan memanggil API Midtrans untuk mendapatkan <code>snap_token</code>.
                </td>
            </tr>
            <tr>
                <td><code>PaymentController</code><br>&bull; <code>handleNotification()</code></td>
                <td><code>POST /api/midtrans/notification</code><br><em>(Webhook Publik Midtrans)</em></td>
                <td>
                    <strong>Penerima Webhook Midtrans:</strong> Mencegah duplicate webhook via tabel <code>payment_webhook_events</code>, verifikasi SHA512 signature, mengambil ulang status dari gateway, auto-resurrect jika booking expired tapi slot masih kosong, atau auto-refund jika slot sudah diambil orang lain.
                </td>
            </tr>
            <tr>
                <td><code>PaymentController</code><br>&bull; <code>show()</code></td>
                <td><code>GET /penyewa/booking/{id}</code><br><code>name('penyewa.booking.detail')</code></td>
                <td>
                    Halaman tiket transaksi detail. Menampilkan status Lunas vs Pending, QR Code scannable interaktif, batas waktu 15 menit, dan tombol cetak nota PDF.
                </td>
            </tr>
            <tr>
                <td><code>PaymentController</code><br>&bull; <code>cancel()</code></td>
                <td><code>POST /penyewa/booking/{id}/cancel</code><br><code>name('penyewa.booking.cancel')</code></td>
                <td>
                    Membatalkan pemesanan. Mengecek apakah jam mulai slot masih lebih dari <code>config('booking.cancellation_min_hours')</code> jam lagi (default 24 jam). Jika memenuhi syarat, status diubah ke <code>canceled</code> dan mencatat refund di tabel <code>refunds</code>.
                </td>
            </tr>
            <tr>
                <td><code>PaymentController</code><br>&bull; <code>invoice()</code></td>
                <td><code>GET /penyewa/booking/{id}/invoice</code><br><code>name('penyewa.booking.invoice')</code></td>
                <td>
                    Men-generate nota PDF 1 halaman landscape ukuran A5 menggunakan DomPDF secara dinamis lengkap dengan QR Code validasi.
                </td>
            </tr>
            <tr>
                <td><code>PaymentController</code><br>&bull; <code>verifyTicket()</code><br>&bull; <code>checkInTicket()</code></td>
                <td><code>GET /tiket/{code}</code><br><code>POST /tiket/{code}/checkin</code><br><em>(Publik Scan QR)</em></td>
                <td>
                    Halaman verifikasi tiket publik ketika QR Code di-scan kamera ponsel. Petugas arena dapat memvalidasi dan menekan tombol check-in untuk mengisi <code>checked_in_at = now()</code>.
                </td>
            </tr>
            <tr>
                <td><code>BookingHistoryController</code></td>
                <td><code>GET /penyewa/riwayat</code></td>
                <td>Menampilkan daftar riwayat booking penyewa dengan filter tab: Semua, Menunggu Pembayaran, Mendatang/Aktif, Selesai, dan Dibatalkan.</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    {{-- ==================== BAGIAN 2: PETA VIEW & KOMPONEN BLADE ==================== --}}
    <div class="header-doc">
        <div class="left">SportPlace Developer Guide &bull; Modul Penyewa</div>
        <div class="right">Bagian 2: View & Komponen Blade</div>
        <div class="clear"></div>
    </div>

    <h1 class="section-title">2. Peta View & Komponen Blade (resources/views/penyewa/)</h1>

    <table class="custom-table">
        <thead>
            <tr>
                <th style="width: 30%;">File Blade View</th>
                <th style="width: 25%;">Komponen Terkait</th>
                <th style="width: 45%;">Elemen Tampilan & Interaktivitas UI</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>kalender.blade.php</code></td>
                <td>
                    <code>x-penyewa.calendar.slot-card</code><br>
                    <code>x-penyewa.calendar.booking-summary</code>
                </td>
                <td>
                    &bull; Strip Carousel 10 Hari ke Depan.<br>
                    &bull; Legenda 3 Indikator: <strong>Available</strong>, <strong>Dipilih</strong>, <strong>Booked</strong>.<br>
                    &bull; Grid Slot Jam: Klik kartu hijau untuk memilih jam (berubah jadi teal "Dipilih").<br>
                    &bull; Sticky Booking Summary di sisi kanan, kalkulasi otomatis total jam dan nominal.<br>
                    &bull; Script JS: <code>lockPastSlots()</code> (mengunci jam mulai yang lewat) & <code>syncSlotStatuses()</code> (polling 15 detik).
                </td>
            </tr>
            <tr>
                <td><code>review-pembayaran.blade.php</code></td>
                <td>
                    Form <code>#checkout-form</code><br>
                    Tombol <code>#btn-pay-now</code>
                </td>
                <td>
                    &bull; Countdown Timer 15 menit persisten via <code>localStorage</code>.<br>
                    &bull; Rincian slot yang dipesan + Biaya Layanan Rp 2.500.<br>
                    &bull; Checkbox persetujuan Syarat & Ketentuan sewa.<br>
                    &bull; Submit form dicegah secara default via AJAX untuk memanggil endpoint <code>/penyewa/bayar</code>, lalu memunculkan popup pembayaran Midtrans Snap via <code>window.snap.pay(snapToken)</code>.
                </td>
            </tr>
            <tr>
                <td><code>pembayaran.blade.php</code></td>
                <td>
                    Modal <code>#cancel-modal</code><br>
                    Tombol <code>Unduh Invoice</code>
                </td>
                <td>
                    &bull; Rincian transaksi dan status Lunas / Menunggu Pembayaran.<br>
                    &bull; <strong>Kartu E-Tiket Arena:</strong> Menampilkan <strong>1 QR Code asli (scannable)</strong> yang mengarah ke URL tiket.<br>
                    &bull; Tombol "Unduh Invoice (PDF)" yang membuka nota landscape A5.<br>
                    &bull; Tombol "Batalkan Pesanan" dengan modal konfirmasi dan informasi refund.
                </td>
            </tr>
            <tr>
                <td><code>invoice-pdf.blade.php</code></td>
                <td>Template DomPDF</td>
                <td>
                    &bull; Didesain khusus untuk ukuran <strong>A5 Landscape (210mm x 148mm)</strong> tepat 1 halaman.<br>
                    &bull; Header Nota, Badge Status LUNAS, Data Pemesan, Data Venue/Lapangan, Tabel Rincian Slot Jam, Rincian Total Bayar, serta mini QR Code verifikasi.
                </td>
            </tr>
            <tr>
                <td><code>tiket-verifikasi.blade.php</code></td>
                <td>Mobile-First Layout</td>
                <td>
                    &bull; Halaman publik saat QR Code tiket di-scan oleh kamera ponsel.<br>
                    &bull; Badge besar TIKET VALID & LUNAS / SUDAH CHECK-IN.<br>
                    &bull; Rincian lapangan, jam sewa, dan nama pemesan.<br>
                    &bull; Tombol aksi petugas arena: <strong>"Konfirmasi Check-In Sekarang"</strong>.
                </td>
            </tr>
        </tbody>
    </table>

    <div class="callout">
        <div class="callout-title">💡 Tips Live Coding: Struktur Lokasi Template</div>
        Semua file tampilan penyewa terletak di folder <code>resources/views/penyewa/</code>. Komponen yang dapat dipakai ulang berada di <code>resources/views/components/penyewa/</code>. Jika penguji meminta mengubah teks atau tombol tertentu, Anda bisa langsung menuju ke direktori ini tanpa kebingungan.
    </div>

    <div class="page-break"></div>

    {{-- ==================== BAGIAN 3: BEDAH DETAIL TOMBOL & FITUR ==================== --}}
    <div class="header-doc">
        <div class="left">SportPlace Developer Guide &bull; Modul Penyewa</div>
        <div class="right">Bagian 3: Bedah Tombol & Alur Interaksi</div>
        <div class="clear"></div>
    </div>

    <h1 class="section-title">3. Bedah Tombol & Alur Interaksi (User Journey)</h1>

    <h2 class="sub-title">Alur 1: Memilih Slot di Kalender Jadwal</h2>
    <table class="custom-table">
        <thead>
            <tr>
                <th style="width: 25%;">Nama Elemen / Tombol</th>
                <th style="width: 25%;">Lokasi File & Baris</th>
                <th style="width: 50%;">Apa yang Terjadi Saat Diklik? (Teknis)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Kartu Slot Jam</strong><br><code>.slot-card</code></td>
                <td><code>kalender.blade.php</code><br>baris 309-400</td>
                <td>
                    1. JS mengecek atribut <code>data-status</code>. Jika <code>booked</code>, klik ditolak.<br>
                    2. Jika <code>available</code>, status diubah menjadi <code>selected</code>, warna kartu berubah menjadi hijau tua dengan ring emerald, label berubah menjadi <code>Dipilih</code>.<br>
                    3. Slot dimasukkan ke dalam elemen <code>#selected-slots-container</code> di panel ringkasan kanan.<br>
                    4. Fungsi <code>recalculateSummary()</code> menghitung ulang subtotal sewa + biaya layanan Rp 2.500.<br>
                    5. URL tombol checkout diperbarui secara dinamis membawa query parameter: <code>field_id</code>, <code>date</code>, dan <code>slots=jam_mulai-jam_selesai:harga</code>.
                </td>
            </tr>
            <tr>
                <td><strong>Tombol "Lanjut ke Pembayaran"</strong><br><code>#btn-checkout</code></td>
                <td><code>booking-summary.blade.php</code><br>baris 108-116</td>
                <td>
                    1. Dicek apakah minimal ada 1 slot dipilih (<code>count > 0</code>). Jika 0, muncul alert pencegah.<br>
                    2. Jika valid, browser diarahkan ke rute review: <code>/penyewa/review-pembayaran?field_id=...&date=...&slots=...</code>
                </td>
            </tr>
        </tbody>
    </table>

    <h2 class="sub-title">Alur 2: Checkout & Pembayaran via Midtrans</h2>
    <table class="custom-table">
        <thead>
            <tr>
                <th style="width: 25%;">Nama Elemen / Tombol</th>
                <th style="width: 25%;">Lokasi File & Baris</th>
                <th style="width: 50%;">Apa yang Terjadi Saat Diklik? (Teknis)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Tombol "Bayar Sekarang"</strong><br><code>#btn-pay-now</code></td>
                <td><code>review-pembayaran.blade.php</code><br>baris 410-424 & 508-590</td>
                <td>
                    1. JS mencegat submit form (<code>e.preventDefault()</code>).<br>
                    2. Memvalidasi checkbox persetujuan syarat & ketentuan (<code>#terms-agree</code>).<br>
                    3. Mengirim request <code>POST /penyewa/bayar</code> via <code>fetch()</code>.<br>
                    4. Controller membuat data <code>BookingHeader</code> status <code>pending</code> dan memanggil API Midtrans untuk mendapatkan <code>snap_token</code>.<br>
                    5. Response JSON diterima: <code>{ snap_token: "..." }</code>.<br>
                    6. JS memicu popup resmi Midtrans: <code>window.snap.pay(res.snap_token, { onSuccess, onPending, onError, onClose })</code>.<br>
                    7. Pengguna memilih metode bayar (QRIS, VA BCA, Mandiri, dll.) di dalam popup Midtrans.
                </td>
            </tr>
            <tr>
                <td><strong>Popup Midtrans (Snapbox)</strong><br><em>Iframe Midtrans</em></td>
                <td><code>review-pembayaran.blade.php</code><br>baris 530-580</td>
                <td>
                    &bull; <strong>onSuccess:</strong> Jika bayar lunas, dialihkan ke <code>/penyewa/booking/{id}?paid=1</code>.<br>
                    &bull; <strong>onPending / onClose:</strong> Jika belum selesai bayar atau popup ditutup, dialihkan ke <code>/penyewa/booking/{id}</code> (halaman detail dengan status menunggu pembayaran & timer 15 menit).
                </td>
            </tr>
        </tbody>
    </table>

    <h2 class="sub-title">Alur 3: Tiket, Nota Invoice & Check-In</h2>
    <table class="custom-table">
        <thead>
            <tr>
                <th style="width: 25%;">Nama Elemen / Tombol</th>
                <th style="width: 25%;">Lokasi File & Baris</th>
                <th style="width: 50%;">Apa yang Terjadi Saat Diklik? (Teknis)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Tombol "Unduh Invoice (PDF)"</strong></td>
                <td><code>pembayaran.blade.php</code><br>baris 300-310</td>
                <td>
                    Membuka rute <code>GET /penyewa/booking/{id}/invoice</code> di tab baru. Controller memuat view <code>invoice-pdf.blade.php</code> dan merender dokumen PDF ukuran A5 Landscape untuk langsung diunduh atau dicetak penyewa.
                </td>
            </tr>
            <tr>
                <td><strong>QR Code E-Tiket Arena</strong><br><em>Scannable QR Image</em></td>
                <td><code>pembayaran.blade.php</code><br>baris 377-410</td>
                <td>
                    1. Di-generate secara dinamis via library <code>chillerlan/php-qrcode</code> dari URL <code>/tiket/{kode_booking}</code>.<br>
                    2. Saat petugas arena men-scan QR code ini dengan kamera HP, browser langsung membuka halaman publik <code>/tiket/{kode_booking}</code>.<br>
                    3. Menampilkan status TIKET VALID & LUNAS dan tombol <strong>"Konfirmasi Check-In Sekarang"</strong>.
                </td>
            </tr>
            <tr>
                <td><strong>Tombol "Konfirmasi Check-In"</strong></td>
                <td><code>tiket-verifikasi.blade.php</code><br>baris 135-150</td>
                <td>
                    Mengirimkan <code>POST /tiket/{code}/checkin</code>. Sistem memeriksa status lunas dan mengisi kolom <code>checked_in_at = now()</code> di database. Status tiket berubah menjadi <strong>SUDAH CHECK-IN</strong> sehingga tidak bisa dipakai ulang.
                </td>
            </tr>
            <tr>
                <td><strong>Tombol "Batalkan Pesanan"</strong></td>
                <td><code>pembayaran.blade.php</code><br>baris 180 & modal 700</td>
                <td>
                    Membuka modal konfirmasi pembatalan. Saat dikonfirmasi, mengirim <code>POST /penyewa/booking/{id}/cancel</code>. Controller mengecek aturan H-24 jam. Jika valid, status booking diubah ke <code>canceled</code> dan membuat data pengembalian dana di tabel <code>refunds</code> sebesar 100%.
                </td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    {{-- ==================== BAGIAN 4: 7 LOGIKA BISNIS KRITIS ==================== --}}
    <div class="header-doc">
        <div class="left">SportPlace Developer Guide &bull; Modul Penyewa</div>
        <div class="right">Bagian 4: 7 Logika Bisnis Kritis</div>
        <div class="clear"></div>
    </div>

    <h1 class="section-title">4. Tujuh (7) Logika Bisnis Kritis yang Wajib Dikuasai</h1>
    <p>Bagian ini adalah pertanyaan yang paling sering diuji oleh dosen atau penguji live coding:</p>

    <h2 class="sub-title">1. Penguncian Slot Berdasarkan Jam Mulai (Bukan Jam Selesai)</h2>
    <p>
        <strong>Kebutuhan:</strong> Jika hari ini adalah hari pertandingan dan waktu saat ini sudah melewati jam mulai slot (misal jam 10:05 untuk slot 10:00 - 11:00), slot tersebut harus <strong>langsung dikunci</strong> dan tidak boleh bisa dipesan lagi.
    </p>
    <div class="callout">
        <strong>Kode Acuan:</strong> <code>CalendarController.php</code> baris 140-143:
        <br>
        <code>$slotStartDateTime = Carbon::parse("{$selectedDateStr} {$startStr}", $tz);</code>
        <br>
        <code>$isPast = $selectedCarbon->lt($today) || ($selectedCarbon->isSameDay($today) && $slotStartDateTime->lte($now));</code>
    </div>

    <h2 class="sub-title">2. Masa Tahan 15 Menit Persisten (Anti-Reset saat Refresh)</h2>
    <p>
        <strong>Kebutuhan:</strong> Saat pengguna menekan "Bayar Sekarang", slot ditahan selama 15 menit agar tidak diambil orang lain. Jika halaman di-refresh, hitung mundur tidak boleh mengulang kembali ke 15:00.
    </p>
    <p>
        <strong>Solusi Teknis:</strong> Database tabel <code>booking_headers</code> menyimpan kolom <code>expires_at</code> yang dihitung di backend: <code>now()->addMinutes(config('booking.payment_window_minutes'))</code>. Di frontend Blade, JavaScript membaca timestamp absolut <code>$expiresAtTimestamp</code> dari server dan menyimpannya di <code>localStorage</code>, sehingga saat direfresh waktu yang berkurang tetap konsisten.
    </p>

    <h2 class="sub-title">3. Penyederhanaan Indikator (Hanya Available, Dipilih, Booked) & Auto-Release</h2>
    <p>
        <strong>Kebutuhan:</strong> Indikator kalender hanya boleh ada 3: <strong>Available</strong> (hijau), <strong>Dipilih</strong> (teal), dan <strong>Booked</strong> (abu-abu). Slot yang sedang diproses orang lain harus tampak sebagai <code>Booked</code> bagi pengguna lain. Namun jika masa 15 menitnya habis (lewat), slot tersebut otomatis kembali ke <code>Available</code>.
    </p>
    <div class="callout">
        <strong>Kode Acuan:</strong> <code>CalendarController.php</code> baris 160-185:
        <br>
        Jika ada booking status <code>pending</code> dan <code>bExpires <= now</code>, controller langsung mengupdate database:
        <br>
        <code>$matchingBooking->header->update(['status' => 'expired']);</code>
        <br>
        Sehingga slot kembali berstatus <code>available</code> dan kartu berubah warna menjadi hijau.
    </div>

    <h2 class="sub-title">4. Webhook Midtrans Idempotent & Auto-Resurrect / Full Refund</h2>
    <p>
        <strong>Kebutuhan:</strong> Penerimaan webhook harus aman dari double-request (idempotent), memverifikasi keaslian signature, dan menangani pembayaran yang tiba setelah batas 15 menit lewat.
    </p>
    <div class="callout">
        <strong>Alur di <code>PaymentController@handleNotification</code>:</strong>
        <br>
        1. <strong>Idempotency:</strong> Disimpan ke tabel <code>payment_webhook_events</code> dengan constraint unique <code>[provider, event_id]</code>. Kiriman duplikat diabaikan.<br>
        2. <strong>Verifikasi Signature:</strong> Membandingkan hash SHA512 <code>order_id + status_code + gross_amount + ServerKey</code>.<br>
        3. <strong>Refetch Status:</strong> Memanggil ulang API Midtrans <code>getStatus()</code> untuk memastikan data valid.<br>
        4. <strong>Auto-Resurrect vs Refund:</strong> Jika booking sudah <code>expired</code> saat uang masuk: dicek apakah slot masih kosong. Jika masih kosong, booking dihidupkan kembali (resurrected) menjadi <code>payment</code> (lunas). Jika sudah diambil orang lain, sistem otomatis membuat data pengembalian dana 100% di tabel <code>refunds</code>.
    </div>

    <div class="page-break"></div>

    {{-- ==================== BAGIAN 5: SKEMA DATABASE & LIVE CODING ==================== --}}
    <div class="header-doc">
        <div class="left">SportPlace Developer Guide &bull; Modul Penyewa</div>
        <div class="right">Bagian 5: Skema Database & Live Coding Cheatsheet</div>
        <div class="clear"></div>
    </div>

    <h1 class="section-title">5. Skema Database & Tanya Jawab Live Coding</h1>

    <h2 class="sub-title">A. Skema Tabel Utama yang Terkait</h2>
    <table class="custom-table">
        <thead>
            <tr>
                <th style="width: 25%;">Nama Tabel</th>
                <th style="width: 35%;">Kolom Kunci & Tipe Data</th>
                <th style="width: 40%;">Fungsi & Relasi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>booking_headers</code></td>
                <td>
                    <code>id</code> (PK)<br>
                    <code>kode_booking</code> (string 12, unique)<br>
                    <code>penyewa_id</code> (FK users)<br>
                    <code>total_tagihan</code> (double)<br>
                    <code>status</code> (pending, payment, canceled, expired)<br>
                    <code>expires_at</code> (dateTime, batas 15 mnt)<br>
                    <code>checked_in_at</code> (dateTime, waktu check-in)
                </td>
                <td>
                    Header induk transaksi. 1 transaksi menampung banyak slot sewa. Memiliki relasi <code>hasMany(BookingDetail)</code>, <code>hasOne(Payment)</code>, <code>hasMany(Refund)</code>.
                </td>
            </tr>
            <tr>
                <td><code>booking_details</code></td>
                <td>
                    <code>id</code> (PK)<br>
                    <code>header_id</code> (FK booking_headers)<br>
                    <code>field_id</code> (FK fields)<br>
                    <code>tanggal_sewa</code> (date)<br>
                    <code>jam_mulai</code>, <code>jam_selesai</code> (time)<br>
                    <code>harga_slot</code> (double)<br>
                    <code>status_item</code> (ongoing, completed, no_show)
                </td>
                <td>
                    Menyimpan tiap jam sewa lapangan yang dipesan. Jika user sewa 3 jam, ada 3 baris di tabel ini yang terhubung ke 1 <code>header_id</code>.
                </td>
            </tr>
            <tr>
                <td><code>payments</code></td>
                <td>
                    <code>id</code> (PK), <code>header_id</code> (FK)<br>
                    <code>pg_transaction_id</code> (Midtrans Order ID)<br>
                    <code>metode_pembayaran</code> (QRIS, VA, dll)<br>
                    <code>nominal_bayar</code> (double)<br>
                    <code>status_pembayaran</code> (pending, succes, failed)<br>
                    <code>paid_at</code> (dateTime)
                </td>
                <td>
                    Mencatat data transaksi pembayaran dari payment gateway Midtrans.
                </td>
            </tr>
            <tr>
                <td><code>payment_webhook_events</code></td>
                <td>
                    <code>id</code> (PK), <code>provider</code> (string: midtrans)<br>
                    <code>event_id</code> (string: order_id)<br>
                    <code>payload</code> (json mentah)<br>
                    <code>processed_at</code> (dateTime)
                </td>
                <td>
                    Menjamin idempotensi webhook agar kiriman ganda dari Midtrans tidak memproses transaksi berulang kali.
                </td>
            </tr>
            <tr>
                <td><code>refunds</code></td>
                <td>
                    <code>id</code> (PK), <code>header_id</code>, <code>payment_id</code><br>
                    <code>nominal_refund</code>, <code>status_refund</code><br>
                    <code>alasan_penolakan</code>, <code>gateway_refund_id</code>
                </td>
                <td>
                    Mencatat pengembalian dana saat user membatalkan sewa (H-24) atau saat booking expired diambil orang lain.
                </td>
            </tr>
        </tbody>
    </table>

    <h2 class="sub-title">B. Cheatsheet Pertanyaan Penguji & Jawaban Live Coding</h2>

    <div class="callout">
        <div class="callout-title">Tanya: "Bagaimana alur dari saat user klik tombol bayar sampai popup Midtrans muncul?"</div>
        <strong>Jawab:</strong> Di file <code>review-pembayaran.blade.php</code>, form checkout memiliki listener JavaScript <code>submit</code> yang dicegat dengan <code>e.preventDefault()</code>. JS mengirim <code>fetch()</code> ke <code>PaymentController@processPayment</code>. Controller memvalidasi input, membuat <code>BookingHeader</code> & <code>BookingDetail</code> status <code>pending</code>, lalu memanggil <code>MidtransService::createSnapToken()</code>. Token Snap dikembalikan sebagai JSON, lalu JS memanggil fungsi bawaan SDK Midtrans yaitu <code>window.snap.pay(res.snap_token)</code>.
    </div>

    <div class="callout">
        <div class="callout-title">Tanya: "Kenapa QR Code tiket bisa discan dan langsung membuka kode booking tanpa login?"</div>
        <strong>Jawab:</strong> QR Code dibuat menggunakan library <code>chillerlan/php-qrcode</code> yang menyandikan URL publik <code>route('booking.verify', $booking->kode_booking)</code>. Rute ini ditempatkan di luar middleware <code>auth</code> pada <code>routes/web.php</code>. Saat kamera HP memindai QR, kamera langsung membuka halaman <code>tiket-verifikasi.blade.php</code>. Controller mencari data menggunakan <code>where('kode_booking', $code)</code> dan menampilkan status valid serta tombol check-in untuk resepsionis.
    </div>

    <div class="callout">
        <div class="callout-title">Tanya: "Bagaimana cara membuat nota PDF 1 halaman landscape?"</div>
        <strong>Jawab:</strong> Menggunakan package <code>barryvdh/laravel-dompdf</code>. Di <code>PaymentController@invoice</code>, view <code>penyewa/invoice-pdf.blade.php</code> dimuat dan disetel ukuran kertasnya: <code>$pdf->setPaper('a5', 'landscape')</code>. CSS pada blade menggunakan <code>@page { size: a5 landscape; margin: 7mm 9mm; }</code> dan struktur tabel HTML 2 kolom yang presisi sehingga tidak terjadi page-overflow ke halaman kedua.
    </div>

    <div class="footer-doc">
        SportPlace Indonesia &bull; Dokumen Panduan Arsitektur & Live Coding Penyewa &bull; Disusun untuk Presentasi Pengujian Sistem
    </div>

</body>
</html>
