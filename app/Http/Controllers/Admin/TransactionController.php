<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\TransactionMonitor;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function __construct(private TransactionMonitor $monitor)
    {
    }

    public function index(): View
    {
        Gate::authorize('view-transactions');

        return view('admin.transaksi.index');
    }

    public function show(int $id): View
    {
        Gate::authorize('view-transactions');

        $trx = $this->monitor->find($id);
        abort_if(! $trx, 404);

        $payments = $this->monitor->payments($id);
        $refunds  = $this->monitor->refunds($id);

        return view('admin.transaksi.show', [
            'trx'      => $trx,
            'items'    => $this->monitor->items($id),
            'payments' => $payments,
            'refunds'  => $refunds,
            'flags'    => $this->monitor->flagsFor($trx),
            'ledger'   => $this->monitor->ledger($trx, $payments, $refunds),
            'state'    => $this->monitor->paymentState($trx),
        ]);
    }
}