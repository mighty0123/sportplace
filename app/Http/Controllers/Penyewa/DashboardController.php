<?php

namespace App\Http\Controllers\Penyewa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan Dashboard Penyewa.
     * Data akan diisi dari model Booking milik user yang sedang login.
     * Saat ini view menggunakan data dummy hardcoded untuk preview.
     */
    public function index(Request $request): View
    {
        // TODO: Tambahkan query Booking aktif, mendatang, dan statistik bulanan di sini.
        return view('penyewa.dashboard');
    }
}
