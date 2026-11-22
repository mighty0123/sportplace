<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\Commission\CommissionRuleException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCommissionRateRequest;
use App\Services\Commission\CommissionService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CommissionController extends Controller
{
    public function __construct(private CommissionService $commission)
    {
    }

    public function index(Request $request): View
    {
        Gate::authorize('manage-commission');

        return view('admin.komisi.index', [
            'global'         => $this->commission->currentGlobal(),
            'nextGlobal'     => $this->commission->nextGlobal(),
            'overrides'      => $this->commission->activeOverrides(),
            'scheduledCount' => $this->commission->scheduledCount(),
            'mitraOptions'   => $this->commission->mitraOptions(),
            'prefillMitra'   => (int) $request->query('mitra'),
            'maxPercent'     => $this->commission->maxPercent(),
            'serviceFee'     => (int) config('booking.service_fee', 2500),
        ]);
    }

    public function store(StoreCommissionRateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $isMitra = $data['scope'] === 'mitra';
        $from = ! empty($data['berlaku_mulai'])
            ? CarbonImmutable::createFromFormat('Y-m-d\TH:i', $data['berlaku_mulai'], config('app.timezone'))
            : null;

        try {
            $this->commission->schedule($request->user(), $isMitra ? (int) $data['mitra_id'] : null, $data['persen'] ?? null, $from, $data['catatan']);
        } catch (CommissionRuleException $e) {
            return back()->withInput()->withErrors([$e->field => $e->getMessage()]);
        }

        $future = $from && $from->gt(now());

        return redirect()->route('admin.komisi.index')
            ->with('success', $future ? 'Tarif dijadwalkan dan akan berlaku pada waktu yang dipilih.' : 'Tarif baru berlaku sekarang. Booking yang sudah dinilai tidak berubah.');
    }
}