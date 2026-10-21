<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration screen.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Register a new account and sign the visitor in.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('status', 'Welcome to PortfolioCraft! Your account is ready.');
    }

    /**
     * Suggest a display name derived from the supplied email address.
     */
    public static function suggestName(string $email): string
    {
        $localPart = Str::before($email, '@');

        return Str::of($localPart)
            ->replace(['.', '_', '-', '+'], ' ')
            ->title()
            ->squish()
            ->limit(100, '')
            ->toString() ?: 'New Member';
    }
}
