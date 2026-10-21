<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the "forgot password" screen.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Email a password reset link to the given address.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            $message = config('mail.default') === 'log'
                ? 'Your reset link was generated, but email delivery is set to log. Find the message in storage/logs/laravel.log, or configure SMTP to send it to your inbox.'
                : __($status);

            return back()->with('status', $message);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
