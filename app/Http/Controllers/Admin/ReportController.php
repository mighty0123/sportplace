<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExportReportRequest;
use App\Models\AuditLog;
use App\Services\Admin\Reports\ReportExporter;
use App\Services\Admin\Reports\ReportService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        Gate::authorize('view-reports');

        return view('admin.laporan.index');
    }

    public function download(ExportReportRequest $request, ReportService $reports, ReportExporter $exporter)
    {
        $type   = $request->validated('type');
        $format = $request->validated('format');
        $f      = $reports->normalize($type, $request->validated());
        $max    = (int) config("reports.max_rows.{$format}");

        $data = $reports->data($type, $f, 1); // hanya untuk memeriksa galat periode dan jumlah baris
        if ($data->rangeError) {
            return back()->with('error', $data->rangeError);
        }
        if ($data->total === 0) {
            return back()->with('error', 'Tidak ada data untuk laporan ini. Ubah filter lalu coba lagi.');
        }
        if ($data->total > $max) {
            return back()->with('error', 'Laporan memuat ' . number_format($data->total, 0, ',', '.') . ' baris, melebihi batas '
                . strtoupper($format) . ' sebesar ' . number_format($max, 0, ',', '.') . ' baris. Persempit periode atau filter.');
        }

        $full = $reports->data($type, $f, $max);

        // Setiap ekspor dicatat: siapa, kapan, laporan apa, filter apa, dan berapa baris
        AuditLog::record($request->user(), 'Laporan', 0, 'ekspor', null, null,
            $full->title . ' (' . strtoupper($format) . '), ' . $full->period . ', ' . $full->total . ' baris. ' . implode('; ', $full->filterLines));

        return $format === 'pdf' ? $exporter->pdf($full, $request->user()) : $exporter->xlsx($full, $request->user());
    }
}