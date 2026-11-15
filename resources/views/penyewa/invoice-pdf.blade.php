<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Nota Pembayaran - {{ $booking->kode_booking ?? 'SP-' . $booking->id }}</title>
    <style>
        @page {
            size: a5 landscape;
            margin: 7mm 9mm 6mm 9mm;
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
            font-size: 9.5px;
            line-height: 1.35;
        }
        .w-full {
            width: 100%;
        }
        /* Header */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0a7660;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }
        .brand-title {
            font-size: 16px;
            font-weight: 900;
            color: #06231d;
            letter-spacing: -0.3px;
        }
        .brand-title span {
            color: #0a7660;
        }
        .brand-sub {
            font-size: 8px;
            color: #5a6c5b;
            margin-top: 1px;
        }
        .header-right {
            text-align: right;
            vertical-align: top;
        }
        .invoice-title {
            font-size: 13px;
            font-weight: 900;
            color: #0a7660;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-status {
            display: inline-block;
            background: #dcfce7;
            color: #15803d;
            font-weight: 800;
            font-size: 8.5px;
            padding: 1.5px 6px;
            border-radius: 4px;
            border: 1px solid #86efac;
            margin-top: 2px;
            text-transform: uppercase;
        }
        .badge-status.unpaid {
            background: #fef3c7;
            color: #b45309;
            border-color: #fcd34d;
        }
        /* Two Column Info */
        .info-table {
            width: 100%;
            margin-bottom: 6px;
        }
        .info-col {
            width: 50%;
            vertical-align: top;
        }
        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 5px 8px;
            margin-right: 4px;
        }
        .info-col:last-child .info-box {
            margin-right: 0;
            margin-left: 4px;
        }
        .box-title {
            font-size: 8px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 2px;
        }
        .box-row {
            margin-bottom: 1.5px;
            font-size: 8.5px;
        }
        .box-label {
            color: #64748b;
            width: 80px;
            display: inline-block;
        }
        .box-val {
            font-weight: 700;
            color: #0f172a;
        }
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .items-table th {
            background: #06231d;
            color: #ffffff;
            font-size: 8px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 4px 6px;
            letter-spacing: 0.3px;
        }
        .items-table td {
            padding: 4px 6px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 8.5px;
            vertical-align: middle;
        }
        .items-table tr:nth-child(even) td {
            background: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        /* Bottom: QR Code + Cost Summary */
        .bottom-table {
            width: 100%;
            margin-top: 2px;
        }
        .qr-td {
            width: 48%;
            vertical-align: top;
            padding-right: 8px;
        }
        .qr-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 6px;
        }
        .qr-inner-table {
            width: 100%;
        }
        .qr-img {
            width: 62px;
            height: 62px;
            display: block;
        }
        .qr-desc {
            font-size: 7.5px;
            color: #166534;
            line-height: 1.3;
            padding-left: 6px;
            vertical-align: middle;
        }
        .qr-code-text {
            font-family: monospace;
            font-size: 11px;
            font-weight: 900;
            color: #0a7660;
            display: block;
            margin-top: 1px;
        }
        .sum-td {
            width: 52%;
            vertical-align: top;
            padding-left: 8px;
        }
        .sum-table {
            width: 100%;
            border-collapse: collapse;
        }
        .sum-table td {
            padding: 2.5px 4px;
            font-size: 8.5px;
        }
        .sum-label {
            color: #64748b;
        }
        .sum-val {
            font-weight: 700;
            color: #1e293b;
            text-align: right;
        }
        .total-row td {
            border-top: 1.5px solid #0a7660;
            padding-top: 4px;
            font-size: 11px;
            font-weight: 900;
            color: #0a7660;
        }
        /* Footer */
        .footer {
            margin-top: 6px;
            padding-top: 4px;
            border-top: 1px solid #e2e8f0;
            font-size: 7px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

@php
    $firstDetail = $booking->details->first();
    $field = $firstDetail?->field;
    $venue = $field?->venue;
    $city = $venue?->city?->nama_kota ?? 'Denpasar';
    $customer = $booking->user;

    $isPaid = in_array(strtolower($booking->status), ['payment', 'completed', 'complated']);
    $statusText = $isPaid ? 'LUNAS (PAID)' : 'MENUNGGU PEMBAYARAN';

    $paymentMethod = $booking->payment?->metode_pembayaran ?? 'Midtrans Payment Gateway';
    $tanggalPesan = \Carbon\Carbon::parse($booking->created_at)->translatedFormat('d F Y, H:i') . ' WIB';

    $serviceFee = 2500;
    $subtotalLapangan = 0;
    foreach ($booking->details as $d) {
        $subtotalLapangan += (float) $d->harga_slot;
    }
    if ($subtotalLapangan == 0) {
        $subtotalLapangan = max(0, (float) $booking->total_tagihan - $serviceFee);
    }
@endphp

<div class="container">
    {{-- 1. Header Nota --}}
    <table class="header-table">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                <div class="brand-title">Sport<span>Place</span></div>
                <div class="brand-sub">Platform Penyewaan Lapangan Olahraga Resmi • sportplace.id</div>
            </td>
            <td class="header-right" style="width: 45%;">
                <div class="invoice-title">NOTA PEMBAYARAN</div>
                <div>
                    <span class="badge-status {{ $isPaid ? '' : 'unpaid' }}">{{ $statusText }}</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- 2. Identitas Pemesan & Venue --}}
    <table class="info-table">
        <tr>
            <td class="info-col">
                <div class="info-box">
                    <div class="box-title">INFORMASI PEMESANAN</div>
                    <div class="box-row">
                        <span class="box-label">Kode Booking</span>:
                        <span class="box-val" style="color: #0a7660; font-family: monospace;">{{ $booking->kode_booking ?? ('SP-' . $booking->id) }}</span>
                    </div>
                    <div class="box-row">
                        <span class="box-label">Nama Pemesan</span>:
                        <span class="box-val">{{ $customer->name ?? 'Penyewa' }}</span>
                    </div>
                    <div class="box-row">
                        <span class="box-label">No. HP / Email</span>:
                        <span class="box-val">{{ $customer->no_hp ?? '-' }} / {{ $customer->email ?? '-' }}</span>
                    </div>
                    <div class="box-row">
                        <span class="box-label">Waktu Transaksi</span>:
                        <span class="box-val">{{ $tanggalPesan }}</span>
                    </div>
                </div>
            </td>
            <td class="info-col">
                <div class="info-box">
                    <div class="box-title">INFORMASI VENUE & LAPANGAN</div>
                    <div class="box-row">
                        <span class="box-label">Nama Venue</span>:
                        <span class="box-val">{{ $venue->nama_venue ?? 'Sport Arena' }}</span>
                    </div>
                    <div class="box-row">
                        <span class="box-label">Lapangan</span>:
                        <span class="box-val">{{ $field->nama_lapangan ?? 'Lapangan' }} ({{ strtoupper($field->sport?->nama_olahraga ?? 'Olahraga') }})</span>
                    </div>
                    <div class="box-row">
                        <span class="box-label">Lokasi / Alamat</span>:
                        <span class="box-val">{{ $venue->alamat_lengkap ?? '-' }}, {{ $city }}</span>
                    </div>
                    <div class="box-row">
                        <span class="box-label">Metode Bayar</span>:
                        <span class="box-val">{{ $paymentMethod }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- 3. Rincian Slot Jam Bermain --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 25%;">Tanggal Sewa</th>
                <th style="width: 25%;">Sesi Jam Main</th>
                <th style="width: 25%;">Nama Lapangan</th>
                <th style="width: 20%;" class="text-right">Harga Slot</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($booking->details as $index => $detail)
                @php
                    $tglDetail = \Carbon\Carbon::parse($detail->tanggal_sewa)->translatedFormat('l, d M Y');
                    $jamM = substr($detail->jam_mulai, 0, 5);
                    $jamS = substr($detail->jam_selesai, 0, 5);
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $tglDetail }}</strong></td>
                    <td>{{ $jamM }} - {{ $jamS }} WIB</td>
                    <td>{{ $detail->field?->nama_lapangan ?? $field?->nama_lapangan }}</td>
                    <td class="text-right">Rp {{ number_format($detail->harga_slot, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td class="text-center">1</td>
                    <td>{{ \Carbon\Carbon::parse($booking->created_at)->translatedFormat('d M Y') }}</td>
                    <td>Sesi Sewa Lapangan</td>
                    <td>{{ $field->nama_lapangan ?? 'Lapangan' }}</td>
                    <td class="text-right">Rp {{ number_format($booking->total_tagihan, 0, ',', '.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 4. Bagian Bawah: QR Code Check-in & Rincian Total --}}
    <table class="bottom-table">
        <tr>
            <td class="qr-td">
                <div class="qr-box">
                    <table class="qr-inner-table">
                        <tr>
                            @if (!empty($qrCodeDataUri))
                                <td style="width: 68px; vertical-align: middle;">
                                    <img src="{{ $qrCodeDataUri }}" alt="QR Code" class="qr-img">
                                </td>
                            @endif
                            <td class="qr-desc">
                                <strong>E-TIKET CHECK-IN ARENA</strong>
                                <span class="qr-code-text">{{ $booking->kode_booking ?? ('SP-' . $booking->id) }}</span>
                                Scan QR ini dengan kamera ponsel saat tiba di resepsionis arena untuk verifikasi tiket langsung.
                            </td>
                        </tr>
                    </table>
                </div>
            </td>

            <td class="sum-td">
                <table class="sum-table">
                    <tr>
                        <td class="sum-label">Subtotal Sewa Lapangan</td>
                        <td class="sum-val">Rp {{ number_format($subtotalLapangan, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="sum-label">Biaya Layanan Platform</td>
                        <td class="sum-val">Rp {{ number_format($serviceFee, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>TOTAL DIBAYAR</td>
                        <td class="text-right">Rp {{ number_format($booking->total_tagihan, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- 5. Footer Catatan --}}
    <div class="footer">
        Nota ini dicetak secara otomatis dan merupakan bukti transaksi pemesanan yang sah dari SportPlace.
        Harap hadir di lokasi arena minimal 15 menit sebelum jam mulai sewa. Bantuan pelanggan: support@sportplace.id
    </div>
</div>

</body>
</html>

