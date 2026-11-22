<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\Admin\AccountActionNotAllowedException;
use App\Exceptions\Admin\AccountAlreadyChangedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAccountStatusRequest;
use App\Services\Admin\UserAccountService as Accounts;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function __construct(private Accounts $accounts)
    {
    }

    public function index(): View
    {
        Gate::authorize('manage-users');

        return view('admin.pengguna.index');
    }

    public function show(int $id): View
    {
        Gate::authorize('manage-users');

        $user = $this->accounts->find($id);
        abort_if(! $user, 404);

        return view('admin.pengguna.show', [
            'user'     => $user,
            'overview' => $this->accounts->overview($user),
            'history'  => $this->accounts->history($id),
            'canAct'   => $this->accounts->canAct(auth()->user(), $user),
        ]);
    }

    public function updateStatus(UpdateAccountStatusRequest $request, int $id): RedirectResponse
    {
        $status = $request->validated('status');

        try {
            $before = $this->accounts->changeStatus($request->user(), $id, $status, $request->validated('alasan'));
        } catch (AccountActionNotAllowedException | AccountAlreadyChangedException $e) {
            return redirect()->route('admin.pengguna.show', $id)->with('error', $e->getMessage());
        }

        $verb = ['aktif' => 'diaktifkan kembali', 'suspend' => 'ditangguhkan', 'banned' => 'diblokir'][$status];

        return redirect()->route('admin.pengguna.show', $id)->with('success', "Akun {$before->name} {$verb}.");
    }
}