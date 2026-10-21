<?php

namespace App\Services;

use App\Enums\TwoFactorPurpose;
use App\Models\TwoFactorCode;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Issues and verifies the single use codes used for two factor
 * authentication. Codes are stored hashed, expire after a short while and
 * can only be attempted a limited number of times.
 */
class TwoFactorService
{
    public function __construct(private readonly SmsService $sms) {}

    /**
     * Issue a new code, deliver it and invalidate any outstanding codes.
     */
    public function sendCode(User $user, string $phone, TwoFactorPurpose $purpose, string $errorKey = 'code'): TwoFactorCode
    {
        $code = $this->generateCode();

        $this->invalidateOutstandingCodes($user, $purpose);
        $this->forgetOldCodes($user);

        $record = $user->twoFactorCodes()->create([
            'purpose' => $purpose->value,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addSeconds((int) config('security.two_factor.code_ttl')),
        ]);

        try {
            $this->sms->send($phone, $this->message($code, $purpose));
        } catch (Throwable $exception) {
            report($exception);

            $record->delete();

            throw ValidationException::withMessages([
                $errorKey => 'We could not send a verification code. Please try again in a moment.',
            ]);
        }

        return $record;
    }

    /**
     * Verify a submitted code for the given purpose.
     *
     * @throws ValidationException
     */
    public function verifyCode(User $user, string $code, TwoFactorPurpose $purpose): bool
    {
        $record = $this->latestCode($user, $purpose);

        if (! $record) {
            throw ValidationException::withMessages([
                'code' => 'Request a new code to continue.',
            ]);
        }

        if (! $record->isUsable()) {
            throw ValidationException::withMessages([
                'code' => $record->consumed_at !== null
                    ? 'That code has already been used. Request a new one.'
                    : 'That code has expired. Request a new one.',
            ]);
        }

        if ($record->attempts >= (int) config('security.two_factor.max_attempts')) {
            throw ValidationException::withMessages([
                'code' => 'Too many attempts. Request a new code.',
            ]);
        }

        if (! Hash::check($code, $record->code_hash)) {
            $record->increment('attempts');

            throw ValidationException::withMessages([
                'code' => 'That code is not correct.',
            ]);
        }

        $record->update(['consumed_at' => now()]);

        return true;
    }

    /**
     * The moment another code may be requested, if the cooldown is active.
     */
    public function resendAvailableAt(User $user, TwoFactorPurpose $purpose): ?Carbon
    {
        $record = $this->latestCode($user, $purpose);

        if (! $record) {
            return null;
        }

        $availableAt = $record->created_at->copy()->addSeconds((int) config('security.two_factor.resend_interval'));

        return $availableAt->isFuture() ? $availableAt : null;
    }

    /**
     * The number of seconds left before another code can be requested.
     */
    public function secondsUntilResend(User $user, TwoFactorPurpose $purpose): int
    {
        $availableAt = $this->resendAvailableAt($user, $purpose);

        if (! $availableAt) {
            return 0;
        }

        return max(0, $availableAt->getTimestamp() - now()->getTimestamp());
    }

    /**
     * The most recently issued code for the given purpose.
     */
    private function latestCode(User $user, TwoFactorPurpose $purpose): ?TwoFactorCode
    {
        return $user->twoFactorCodes()
            ->where('purpose', $purpose->value)
            ->latest('id')
            ->first();
    }

    /**
     * Consume every code that is still waiting to be used.
     */
    private function invalidateOutstandingCodes(User $user, TwoFactorPurpose $purpose): void
    {
        $user->twoFactorCodes()
            ->where('purpose', $purpose->value)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);
    }

    /**
     * Remove the codes that are old enough to be of no use anymore.
     */
    private function forgetOldCodes(User $user): void
    {
        $user->twoFactorCodes()
            ->where('created_at', '<', now()->subDay())
            ->delete();
    }

    /**
     * Generate a random numeric code.
     */
    private function generateCode(): string
    {
        $length = max(4, (int) config('security.two_factor.code_length'));
        $highest = (int) str_repeat('9', $length);

        return str_pad((string) random_int(0, $highest), $length, '0', STR_PAD_LEFT);
    }

    /**
     * Build the message that is delivered to the user.
     */
    private function message(string $code, TwoFactorPurpose $purpose): string
    {
        $minutes = max(1, (int) ceil((int) config('security.two_factor.code_ttl') / 60));

        return "PortfolioCraft: {$code} is your code to {$purpose->action()}. It expires in {$minutes} minutes.";
    }
}
