<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * MTR-12: Profil & Dokumen Legalitas Mitra.
     */
    public function index(): View
    {
        return view('mitra.profile.index');
    }
}
