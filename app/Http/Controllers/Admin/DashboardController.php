<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardStats;

class DashboardController extends Controller
{
    public function __invoke(DashboardStats $stats)
    {
        return view('admin.dashboard', [
            'kpis' => $stats->kpis(),
            'queues' => $stats->queues(),
            'chart' => $stats->chart(),
            'activity' => $stats->activity(),
        ]);
    }
}