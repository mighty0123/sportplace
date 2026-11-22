<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LogoutOtherSessionsRequest;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use App\Http\Requests\Admin\UpdateProfileRequest;
use App\Services\Admin\AdminProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProfileController extends Controller
{
    public function __construct(private AdminProfileService $profile)
    {
    }

    public function show(Request $request): Response
    {
        $user = $request->user();

        // Halaman berisi formulir kata sandi: jangan disimpan di cache browser
        return response()->view('admin.profile.show', [
            'user'     => $user,
            'sessions' => $this->profile->sessions($user, $request->session()->getId()),
            'activity' => $this->profile->recentActivity($user),
            'sessionsSupported' => config('session.driver') === 'database',
        ])->header('Cache-Control', 'private, no-store');
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $changed = $this->profile->updateProfile($request->user(), $request->validated('name'), $request->validated('no_hp'));

        return redirect()->route('admin.profile')->with('success', $changed ? 'Profil diperbarui.' : 'Tidak ada perubahan.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $out = $this->profile->changePassword($request->user(), $request->validated('password'), $request->session()->getId());

        return redirect()->route('admin.profile')
            ->with('success', $out ? "Kata sandi diganti, dan {$out} perangkat lain dikeluarkan." : 'Kata sandi diganti.');
    }

    public function logoutOthers(LogoutOtherSessionsRequest $request): RedirectResponse
    {
        $out = $this->profile->logoutOthers($request->user(), $request->session()->getId());

        return redirect()->route('admin.profile')->with('success', $out ? "{$out} perangkat lain dikeluarkan." : 'Tidak ada perangkat lain yang masuk.');
    }
}