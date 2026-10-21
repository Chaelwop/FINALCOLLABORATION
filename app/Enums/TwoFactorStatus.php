<?php

namespace App\Enums;

/**
 * The state of the two factor authentication on a user account.
 */
enum TwoFactorStatus: string
{
    case Disabled = 'disabled';
    case Pending = 'pending';
    case Enabled = 'enabled';

    /**
     * The label shown next to the status badge.
     */
    public function label(): string
    {
        return match ($this) {
            self::Disabled => 'Not enabled',
            self::Pending => 'Awaiting confirmation',
            self::Enabled => 'Enabled',
        };
    }

    /**
     * The badge styling used to render the status.
     */
    public function badge(): string
    {
        return match ($this) {
            self::Disabled => 'badge-neutral',
            self::Pending => 'badge-warning',
            self::Enabled => 'badge-success',
        };
    }
}
