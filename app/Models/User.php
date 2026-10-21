<?php

namespace App\Models;

use App\Enums\TwoFactorStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'avatar',
    'phone',
    'two_factor_enabled',
    'two_factor_confirmed_at',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<SocialAccount, $this>
     */
    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * @return HasMany<Portfolio, $this>
     */
    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }

    /**
     * Determine whether the user signed up through a social provider.
     */
    public function hasSocialAccount(): bool
    {
        return $this->socialAccounts()->exists();
    }

    /**
     * @return HasMany<TwoFactorCode, $this>
     */
    public function twoFactorCodes(): HasMany
    {
        return $this->hasMany(TwoFactorCode::class);
    }

    /**
     * Determine whether two factor authentication is active for the account.
     */
    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_enabled && filled($this->phone);
    }

    /**
     * The current state of the two factor authentication on the account.
     */
    public function twoFactorStatus(): TwoFactorStatus
    {
        if ($this->hasTwoFactorEnabled()) {
            return TwoFactorStatus::Enabled;
        }

        return filled($this->phone)
            ? TwoFactorStatus::Pending
            : TwoFactorStatus::Disabled;
    }

    /**
     * The phone number with all but the last four digits hidden.
     */
    public function maskedPhone(): string
    {
        if (blank($this->phone)) {
            return '';
        }

        $digits = preg_replace('/\D+/', '', $this->phone) ?? '';

        if (strlen($digits) <= 4) {
            return str_repeat('*', strlen($digits));
        }

        return str_repeat('*', strlen($digits) - 4).substr($digits, -4);
    }
}
