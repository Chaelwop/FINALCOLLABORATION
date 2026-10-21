<?php

namespace App\Http\Controllers;

use App\Enums\TwoFactorPurpose;
use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Lets account holders turn the SMS based two factor authentication on and
 * off from the settings screen.
 */
class TwoFactorController extends Controller
{
    public function __construct(private readonly TwoFactorService $twoFactor) {}

    /**
     * Send a verification code to the phone number that was entered.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! config('security.two_factor.enabled')) {
            return redirect()->route('settings')->withErrors([
                'phone' => 'Two factor authentication is turned off for this application.',
            ]);
        }

        if ($user->hasTwoFactorEnabled()) {
            return redirect()->route('settings')->withErrors([
                'phone' => 'Two factor authentication is already enabled. Turn it off to use a different number.',
            ]);
        }

        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:24', 'regex:/^\+?[0-9][0-9\s\-\(\)\.]{6,22}$/'],
        ]);

        $phone = $this->normalizePhone($validated['phone']);

        $user->update(['phone' => $phone]);

        $this->twoFactor->sendCode($user, $phone, TwoFactorPurpose::Enrollment, 'phone');

        return redirect()->route('settings')->with('status', 'We sent a verification code to '.$user->fresh()->maskedPhone().'. Enter it below to finish.');
    }

    /**
     * Confirm the code and turn two factor authentication on.
     */
    public function confirm(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'code' => ['required', 'string', 'digits:'.config('security.two_factor.code_length')],
        ]);

        if (blank($user->phone)) {
            return redirect()->route('settings')->withErrors([
                'code' => 'Add a phone number before confirming a code.',
            ]);
        }

        $this->twoFactor->verifyCode($user, $validated['code'], TwoFactorPurpose::Enrollment);

        $user->update([
            'two_factor_enabled' => true,
            'two_factor_confirmed_at' => now(),
        ]);

        $user->twoFactorCodes()->delete();

        return redirect()->route('settings')->with('status', 'Two factor authentication is now enabled.');
    }

    /**
     * Turn two factor authentication off.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        $user->update([
            'two_factor_enabled' => false,
            'two_factor_confirmed_at' => null,
            'phone' => null,
        ]);

        $user->twoFactorCodes()->delete();

        return redirect()->route('settings')->with('status', 'Two factor authentication has been turned off.');
    }

    /**
     * Store the phone number in a consistent format.
     */
    private function normalizePhone(string $phone): string
    {
        $trimmed = trim($phone);
        $digits = preg_replace('/\D+/', '', $trimmed) ?? '';

        return str_starts_with($trimmed, '+') ? '+'.$digits : $digits;
    }
}
