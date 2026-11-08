<?php

namespace App\Http\Requests\Mitra;

use App\Models\BookingDetail;
use App\Models\Field;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

class StoreBlockRequest extends FormRequest
{
    public function authorize(): bool
    {
        $route = $this->route();
        $field = $route instanceof Route ? $route->parameter('field') : $route;

        if (! $field instanceof Field && is_numeric($field)) {
            $field = Field::find($field);
        }

        if (! $field) {
            return false;
        }

        $mitraId = $this->user()?->mitraProfile?->id;

        return $mitraId && $field->venue && $field->venue->mitra_id === $mitraId;
    }

    public function rules(): array
    {
        return [
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'alasan_pemblokiran' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $route = $this->route();
            $field = $route instanceof Route ? $route->parameter('field') : $route;

            if (! $field instanceof Field && is_numeric($field)) {
                $field = Field::find($field);
            }

            if (! $field) {
                return;
            }

            $start = $this->input('tanggal_mulai');
            $end = $this->input('tanggal_selesai');
            $jamMulai = $this->input('jam_mulai');
            $jamSelesai = $this->input('jam_selesai');

            if (! $start || ! $end || ! $jamMulai || ! $jamSelesai) {
                return;
            }

            // T6.4 Proteksi Konflik Blokir vs Booking aktif / confirmed (OQ-013)
            // Cek apakah ada booking aktif dalam rentang tanggal dan jam yang diajukan
            $conflictingBookings = BookingDetail::with('header.user')
                ->where('field_id', $field->id)
                ->whereBetween(DB::raw('DATE(tanggal_sewa)'), [$start, $end])
                ->where(function ($q) use ($jamMulai, $jamSelesai) {
                    $q->where('jam_mulai', '<', $jamSelesai)
                        ->where('jam_selesai', '>', $jamMulai);
                })
                ->whereHas('header', function ($q) {
                    $q->whereNotIn('status', ['canceled', 'expired']);
                })
                ->get();

            if ($conflictingBookings->isNotEmpty()) {
                $count = $conflictingBookings->count();
                $first = $conflictingBookings->first();
                $tgl = Carbon::parse($first->tanggal_sewa)->translatedFormat('d M Y');
                $jam = substr($first->jam_mulai, 0, 5).'–'.substr($first->jam_selesai, 0, 5);
                $nama = $first->header?->user?->name ?? 'Penyewa';

                $validator->errors()->add(
                    'jam_mulai',
                    "Tidak dapat memblokir jadwal ini karena terdapat {$count} reservasi aktif yang bentrok (Contoh: {$nama} pada {$tgl} jam {$jam}). Silakan selesaikan atau jadwalkan ulang reservasi terlebih dahulu."
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'tanggal_mulai.required' => 'Tanggal mulai blokir wajib diisi.',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai blokir tidak boleh tanggal lampau.',
            'tanggal_selesai.required' => 'Tanggal selesai blokir wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'jam_mulai.required' => 'Jam mulai blokir wajib diisi.',
            'jam_mulai.date_format' => 'Format jam mulai harus JJ:MM (contoh: 08:00).',
            'jam_selesai.required' => 'Jam selesai blokir wajib diisi.',
            'jam_selesai.date_format' => 'Format jam selesai harus JJ:MM (contoh: 12:00).',
            'jam_selesai.after' => 'Jam selesai harus lebih akhir daripada jam mulai.',
            'alasan_pemblokiran.max' => 'Alasan pemblokiran maksimal 255 karakter.',
        ];
    }
}
