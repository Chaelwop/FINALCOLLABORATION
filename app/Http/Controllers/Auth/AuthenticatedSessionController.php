<?php

namespace App\Http\Controllers\Auth;

use App\Enums\TwoFactorPurpose;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function __construct(private readonly TwoFactorService $twoFactor) {}

    /**
     * Display the sign-in screen.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming sign-in request.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($credentials['email']).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "Too many sign-in attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        $provider = Auth::getProvider();
        $user = $provider->retrieveByCredentials($credentials);

        if (! $user || ! $provider->validateCredentials($user, $credentials)) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        if (config('security.two_factor.enabled') && $user->hasTwoFactorEnabled()) {
            return $this->startTwoFactorChallenge($request, $user);
        }

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Sign the current user out of the application.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
     * Hold the sign-in until the code sent to the phone number is confirmed.
     */
    private function startTwoFactorChallenge(Request $request, User $user): RedirectResponse
    {
        $request->session()->regenerate();

        $request->session()->put([
            'two_factor.user_id' => $user->getKey(),
            'two_factor.remember' => $request->boolean('remember'),
            'two_factor.intended' => $this->intendedPath($request->session()->pull('url.intended', '')),
        ]);

        $this->twoFactor->sendCode($user, (string) $user->phone, TwoFactorPurpose::Login);

        return redirect()->route('two-factor.login');
    }

    /**
     * Turn the stored destination into a local path that is safe to redirect to.
     */
    private function intendedPath(string $url): string
    {
        if ($url === '') {
            return '';
        }

        $parsed = parse_url($url);
        $host = $parsed['host'] ?? null;

        if ($host !== null && $host !== parse_url((string) config('app.url'), PHP_URL_HOST)) {
            return '';
        }

        $path = $parsed['path'] ?? '/';

        if (isset($parsed['query'])) {
            $path .= '?'.$parsed['query'];
        }

        return $path;
    }
}
