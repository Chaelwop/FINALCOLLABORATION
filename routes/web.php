<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\TwoFactorChallengeController;
use App\Http\Controllers\PortfolioApiController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PortfolioController::class, 'home'])->name('home');
Route::get('/p/{portfolio}', [PortfolioController::class, 'show'])->name('portfolios.show');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Two factor authentication
|--------------------------------------------------------------------------
|
| The second step of the sign-in flow for accounts that have the SMS based
| two factor authentication turned on.
|
*/
Route::middleware('guest')->group(function (): void {
    Route::get('/login/two-factor', [TwoFactorChallengeController::class, 'create'])->name('two-factor.login');
    Route::post('/login/two-factor', [TwoFactorChallengeController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('two-factor.verify');
    Route::post('/login/two-factor/resend', [TwoFactorChallengeController::class, 'resend'])
        ->middleware('throttle:10,1')
        ->name('two-factor.resend');
});

/*
|--------------------------------------------------------------------------
| Social sign-in
|--------------------------------------------------------------------------
|
| These routes are reachable by guests signing in and by signed-in users
| connecting an additional provider to their existing account.
|
*/
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated application
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [PortfolioController::class, 'dashboard'])->name('dashboard');
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings');
    Route::delete('/settings/social-accounts/{socialAccount}', [SettingsController::class, 'destroySocialAccount'])->name('settings.social.destroy');

    Route::post('/settings/two-factor', [TwoFactorController::class, 'store'])->name('settings.two-factor.store');
    Route::post('/settings/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('settings.two-factor.confirm');
    Route::delete('/settings/two-factor', [TwoFactorController::class, 'destroy'])->name('settings.two-factor.destroy');

    Route::get('/portfolios/create', [PortfolioController::class, 'create'])->name('portfolios.create');
    Route::get('/portfolios/{portfolio}/edit', [PortfolioController::class, 'edit'])->name('portfolios.edit');
    Route::get('/portfolios/{portfolio}/preview', [PortfolioController::class, 'preview'])->name('portfolios.preview');
    Route::post('/portfolios/preview', [PortfolioController::class, 'fragment'])->name('portfolios.fragment');

    Route::get('/api/portfolios', [PortfolioApiController::class, 'index'])->name('api.portfolios.index');
    Route::post('/api/portfolios', [PortfolioApiController::class, 'store'])->name('api.portfolios.store');
    Route::put('/api/portfolios/{portfolio}', [PortfolioApiController::class, 'update'])->name('api.portfolios.update');
    Route::delete('/api/portfolios/{portfolio}', [PortfolioApiController::class, 'destroy'])->name('api.portfolios.destroy');
});
