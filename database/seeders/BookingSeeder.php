<?php

namespace Database\Seeders;

use App\Models\BookingDetail;
use App\Models\BookingHeader;
use App\Models\Field;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('role', 'customer')->first();
        if (! $customer) {
            $customer = User::create([
                'name' => 'Budi Santoso',
                'email' => 'customer@sportplace.com',
                'password' => bcrypt('password123'),
                'no_hp' => '081234567890',
                'role' => 'customer',
                'status_akun' => 'aktif',
                'email_verified_at' => now(),
            ]);
        }

        $fields = Field::all();
        if ($fields->isEmpty()) {
            return;
        }

        $field1 = $fields->first();
        $field2 = $fields->count() > 1 ? $fields->get(1) : $field1;

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $tomorrow = Carbon::tomorrow();

        $seedData = [
            // 1. Booking kemarin: sudah selesai (completed)
            [
                'field_id' => $field1->id,
                'tanggal_sewa' => $yesterday->toDateString(),
                'jam_mulai' => '16:00:00',
                'jam_selesai' => '17:00:00',
                'harga_slot' => 120000,
                'status_item' => 'completed',
                'header_status' => 'payment',
                'payment_status' => 'succes',
            ],
            // 2. Booking hari ini pagi: sudah selesai jamnya, status ongoing (Siap Diselesaikan / Ready to Complete)
            [
                'field_id' => $field1->id,
                'tanggal_sewa' => $today->toDateString(),
                'jam_mulai' => '07:00:00',
                'jam_selesai' => '08:00:00',
                'harga_slot' => 100000,
                'status_item' => 'ongoing',
                'header_status' => 'payment',
                'payment_status' => 'succes',
            ],
            // 3. Booking hari ini pagi: sudah selesai jamnya (Siap Diselesaikan / Bulk Completion test)
            [
                'field_id' => $field2->id,
                'tanggal_sewa' => $today->toDateString(),
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '09:00:00',
                'harga_slot' => 120000,
                'status_item' => 'ongoing',
                'header_status' => 'payment',
                'payment_status' => 'succes',
            ],
            // 4. Booking hari ini malam: jamnya belum selesai (Ongoing, belum bisa diselesaikan)
            [
                'field_id' => $field1->id,
                'tanggal_sewa' => $today->toDateString(),
                'jam_mulai' => '19:00:00',
                'jam_selesai' => '21:00:00',
                'harga_slot' => 240000,
                'status_item' => 'ongoing',
                'header_status' => 'payment',
                'payment_status' => 'succes',
            ],
            // 5. Booking kemarin: penyewa tidak hadir (no_show)
            [
                'field_id' => $field2->id,
                'tanggal_sewa' => $yesterday->toDateString(),
                'jam_mulai' => '14:00:00',
                'jam_selesai' => '15:00:00',
                'harga_slot' => 100000,
                'status_item' => 'no_show',
                'header_status' => 'payment',
                'payment_status' => 'succes',
            ],
            // 6. Booking besok: masih pending pembayaran
            [
                'field_id' => $field1->id,
                'tanggal_sewa' => $tomorrow->toDateString(),
                'jam_mulai' => '10:00:00',
                'jam_selesai' => '11:00:00',
                'harga_slot' => 100000,
                'status_item' => 'ongoing',
                'header_status' => 'pending',
                'payment_status' => 'pending',
            ],
        ];

        foreach ($seedData as $data) {
            $header = BookingHeader::create([
                'penyewa_id' => $customer->id,
                'total_tagihan' => $data['harga_slot'],
                'status' => $data['header_status'],
                'created_at' => Carbon::parse($data['tanggal_sewa'].' 06:00:00'),
            ]);

            BookingDetail::create([
                'header_id' => $header->id,
                'field_id' => $data['field_id'],
                'tanggal_sewa' => $data['tanggal_sewa'],
                'jam_mulai' => $data['jam_mulai'],
                'jam_selesai' => $data['jam_selesai'],
                'harga_slot' => $data['harga_slot'],
                'status_item' => $data['status_item'],
                'created_at' => $header->created_at,
            ]);

            Payment::create([
                'header_id' => $header->id,
                'pg_transaction_id' => 'TRX-'.Str::upper(Str::random(12)),
                'metode_pembayaran' => 'midtrans_qris',
                'nominal_bayar' => $data['harga_slot'],
                'status_pembayaran' => $data['payment_status'],
                'paid_at' => $data['payment_status'] === 'succes' ? $header->created_at->addMinutes(5) : null,
                'created_at' => $header->created_at,
            ]);
        }
    }
}

