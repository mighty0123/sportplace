@props([
    'active' => null,
])

{{-- 
    Navbar Publik SportPlace 
    Didelegasikan ke komponen Navbar terpadu untuk memastikan tampilan dan fungsionalitas identik di semua halaman:
    Landing Page, Tentang Kami, Bantuan, Dashboard User, Explore Lapangan/Event, dan Detail Lapangan/Event.
--}}
<x-penyewa.navbar :active="$active" />
