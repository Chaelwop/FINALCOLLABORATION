<?php

namespace App\Enums;

/**
 * The purpose a two factor verification code was issued for.
 */
enum TwoFactorPurpose: string
{
    case Login = 'login';
    case Enrollment = 'enrollment';

    /**
     * The action the code is used for, used in the delivered message.
     */
    public function action(): string
    {
        return match ($this) {
            self::Login => 'sign in',
            self::Enrollment => 'secure your account',
        };
    }
}
