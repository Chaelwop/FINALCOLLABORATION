<?php

namespace App\Http\Controllers\Auth;

use App\Enums\TwoFactorPurpose;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * The second step of the sign-in flow for accounts with two factor
 * authentication turned on. The session is only created once the code
 * that was sent to the phone number has been confirmed.
 */
class TwoFactorChallengeController extends Controller
{
    public function __construct(private readonly TwoFactorService $twoFactor) {}

    /**
     * Display the code challenge screen.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $user = $this->pendingUser($request);

        if (! $user) {
            $request->session()->forget('two_factor.user_id');

            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge', [
            'user' => $user,
            'resendIn' => $this->twoFactor->secondsUntilResend($user, TwoFactorPurpose::Login),
        ]);
    }

    /**
     * Confirm the code that was sent to the phone number.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'digits:'.config('security.two_factor.code_length')],
        ]);

        $user = $this->pendingUser($request);

        if (! $user) {
            $request->session()->forget('two_factor.user_id');

            return redirect()->route('login');
        }

        $this->twoFactor->verifyCode($user, $validated['code'], TwoFactorPurpose::Login);

        $remember = (bool) $request->session()->pull('two_factor.remember', false);
        $intended = $this->intendedUrl($request);

        $request->session()->forget('two_factor.user_id');

        Auth::login($user, $remember);

        $request->session()->regenerate();

        return redirect()->to($intended);
    }

    /**
     * Send a fresh code to the phone number on the account.
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = $this->pendingUser($request);

        if (! $user) {
            $request->session()->forget('two_factor.user_id');

            return redirect()->route('login');
        }

        if ($this->twoFactor->resendAvailableAt($user, TwoFactorPurpose::Login)) {
            return redirect()->route('two-factor.login')->withErrors([
                'code' => 'Please wait a moment before requesting another code.',
            ]);
        }

        $this->twoFactor->sendCode($user, (string) $user->phone, TwoFactorPurpose::Login);

        return redirect()->route('two-factor.login')->with('status', 'A new code is on its way to '.$user->maskedPhone().'.');
    }

    /**
     * The user that is waiting for a code, if the sign-in is still pending.
     */
    private function pendingUser(Request $request): ?User
    {
        $userId = $request->session()->get('two_factor.user_id');

        if (! $userId) {
            return null;
        }

        $user = User::find($userId);

        if (! $user || ! $user->hasTwoFactorEnabled()) {
            return null;
        }

        return $user;
    }

    /**
     * The page the visitor was heading to before the sign-in started.
     */
    private function intendedUrl(Request $request): string
    {
        $intended = (string) $request->session()->pull('two_factor.intended', '');

        if ($intended === '' || ! str_starts_with($intended, '/') || str_starts_with($intended, '//')) {
            return route('dashboard');
        }

        return $intended;
    }
}
