<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class HelpController extends Controller
{
    /**
     * Tampilkan halaman Pusat Bantuan & Tanya Jawab Komprehensif (PRD PUB-07, PUB-08, FR-CAT-004).
     */
    public function index(Request $request): View
    {
        return view('user-public.bantuan');
    }
}
