<?php

namespace App\Http\Controllers;

use App\Enums\SocialProvider;
use App\Models\SocialAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Show the account settings screen.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('settings', [
            'user' => $user,
            'connected' => $user->socialAccounts()->get(),
            'available' => SocialProvider::cases(),
            'twoFactorStatus' => $user->twoFactorStatus(),
            'twoFactorAvailable' => (bool) config('security.two_factor.enabled'),
        ]);
    }

    /**
     * Disconnect a social provider from the account.
     */
    public function destroySocialAccount(Request $request, SocialAccount $socialAccount): RedirectResponse
    {
        abort_unless($socialAccount->user_id === $request->user()->getKey(), 404);

        $socialAccount->delete();

        return redirect()->route('settings')->with('status', 'That provider has been disconnected.');
    }
}
