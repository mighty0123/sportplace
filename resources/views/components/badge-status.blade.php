@props(['status'])

@php
    $normalized = strtolower(trim($status ?? ''));
    
    $classes = match($normalized) {
        'approved', 'aktif', 'active', 'completed', 'buka' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'pending', 'pending_review', 'pending_payment' => 'bg-amber-50 text-amber-700 border-amber-200',
        'rejected', 'banned', 'no_show', 'failed' => 'bg-rose-50 text-rose-700 border-rose-200',
        'draft', 'nonaktif', 'tutup', 'cancelled', 'expired' => 'bg-slate-100 text-slate-700 border-slate-300',
        'ongoing', 'processing' => 'bg-blue-50 text-blue-700 border-blue-200',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };

    $label = match($normalized) {
        'approved' => 'Disetujui',
        'aktif' => 'Aktif',
        'pending' => 'Menunggu Verifikasi',
        'pending_review' => 'Menunggu Peninjauan',
        'rejected' => 'Ditolak',
        'draft' => 'Draft',
        'nonaktif' => 'Nonaktif',
        'ongoing' => 'Berlangsung',
        'completed' => 'Selesai',
        'no_show' => 'Tidak Hadir',
        'cancelled' => 'Dibatalkan',
        'expired' => 'Kedaluwarsa',
        'buka' => 'Buka',
        'tutup' => 'Tutup',
        default => ucfirst($status),
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {$classes}"]) }}>
    {{ $label }}
</span>

