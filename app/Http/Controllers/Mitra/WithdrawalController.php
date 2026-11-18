<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class WithdrawalController extends Controller
{
    /**
     * MTR-13 / FR-FIN-003: Menampilkan halaman dompet dan penarikan dana mitra.
     */
    public function index(): View
    {
        return view('mitra.withdrawals.index');
    }
}
