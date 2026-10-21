<?php

namespace App\Http\Controllers\Auth;

use App\Enums\SocialProvider;
use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Throwable;

class SocialAuthController extends Controller
{
    /**
     * Send the visitor to the provider's consent screen.
     */
    public function redirect(string $provider): SymfonyRedirectResponse
    {
        $socialProvider = $this->resolveProvider($provider);

        if (! $socialProvider->isConfigured()) {
            return back()->withErrors([
                'social' => "{$socialProvider->label()} sign-in is not configured yet. Add the client credentials to your .env file.",
            ]);
        }

        Config::set("services.{$socialProvider->value}.redirect", route('social.callback', ['provider' => $socialProvider->value]));

        return Socialite::driver($socialProvider->value)
            ->scopes($socialProvider->scopes())
            ->redirect();
    }

    /**
     * Handle the visitor coming back from the provider.
     */
    public function callback(string $provider, Request $request): RedirectResponse
    {
        $socialProvider = $this->resolveProvider($provider);

        if (! $socialProvider->isConfigured()) {
            return redirect()->route('login')->withErrors([
                'social' => "{$socialProvider->label()} sign-in is not configured yet.",
            ]);
        }

        try {
            $socialiteUser = Socialite::driver($socialProvider->value)->user();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('login')->withErrors([
                'social' => 'We could not complete the sign-in. Please try again.',
            ]);
        }

        if (Auth::check()) {
            return $this->linkToCurrentUser($socialProvider, $socialiteUser);
        }

        $account = SocialAccount::query()
            ->where('provider', $socialProvider->value)
            ->where('provider_id', (string) $socialiteUser->getId())
            ->first();

        if ($account) {
            $this->syncAccount($account, $socialiteUser);
            $account->user->update(['avatar' => $socialiteUser->getAvatar()]);

            Auth::login($account->user, true);
            $request->session()->regenerate();

            return redirect()->route('dashboard')->with('status', "Welcome back, {$account->user->name}!");
        }

        $email = $socialiteUser->getEmail();

        if (! $email) {
            return redirect()->route('login')->withErrors([
                'social' => "{$socialProvider->label()} did not share an email address. Sign in with your email and password first, then connect {$socialProvider->label()} from your profile.",
            ]);
        }

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => $socialiteUser->getName() ?: RegisteredUserController::suggestName($email),
                'email' => $email,
                'password' => Str::password(),
                'avatar' => $socialiteUser->getAvatar(),
                'email_verified_at' => now(),
            ]);
        }

        $this->createAccount($user, $socialProvider, $socialiteUser);

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('status', "Welcome to PortfolioCraft, {$user->name}!");
    }

    /**
     * Attach a provider to the account that is already signed in.
     */
    private function linkToCurrentUser(SocialProvider $provider, SocialiteUser $socialiteUser): RedirectResponse
    {
        $user = Auth::user();

        $alreadyLinked = $user->socialAccounts()
            ->where('provider', $provider->value)
            ->where('provider_id', (string) $socialiteUser->getId())
            ->exists();

        if (! $alreadyLinked) {
            $this->createAccount($user, $provider, $socialiteUser);
        }

        return redirect()
            ->route('settings')
            ->with('status', "{$provider->label()} is now connected to your account.");
    }

    /**
     * Persist a new social account for the given user.
     */
    private function createAccount(User $user, SocialProvider $provider, SocialiteUser $socialiteUser): SocialAccount
    {
        return $user->socialAccounts()->create([
            'provider' => $provider->value,
            'provider_id' => (string) $socialiteUser->getId(),
            'nickname' => $socialiteUser->getNickname(),
            'name' => $socialiteUser->getName(),
            'email' => $socialiteUser->getEmail(),
            'avatar' => $socialiteUser->getAvatar(),
            'token' => $socialiteUser->token ?? '',
            'refresh_token' => $socialiteUser->refreshToken ?? '',
            'expires_at' => isset($socialiteUser->expiresIn) ? now()->addSeconds($socialiteUser->expiresIn) : null,
        ]);
    }

    /**
     * Refresh the stored tokens for an existing social account.
     */
    private function syncAccount(SocialAccount $account, SocialiteUser $socialiteUser): void
    {
        $account->update([
            'nickname' => $socialiteUser->getNickname(),
            'name' => $socialiteUser->getName(),
            'email' => $socialiteUser->getEmail(),
            'avatar' => $socialiteUser->getAvatar(),
            'token' => $socialiteUser->token ?? $account->token,
            'refresh_token' => $socialiteUser->refreshToken ?? $account->refresh_token,
            'expires_at' => isset($socialiteUser->expiresIn) ? now()->addSeconds($socialiteUser->expiresIn) : $account->expires_at,
        ]);
    }

    /**
     * Resolve the requested provider or abort the request.
     */
    private function resolveProvider(string $provider): SocialProvider
    {
        $socialProvider = SocialProvider::tryFrom($provider);

        abort_if($socialProvider === null, 404);

        return $socialProvider;
    }
}
