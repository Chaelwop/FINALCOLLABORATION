<?php

namespace App\Enums;

use Illuminate\Support\Facades\Config;

/**
 * The social identity providers the application can authenticate with.
 */
enum SocialProvider: string
{
    case Google = 'google';
    case Github = 'github';
    case Facebook = 'facebook';
    case X = 'x';
    case LinkedIn = 'linkedin';
    case GitLab = 'gitlab';
    case Bitbucket = 'bitbucket';
    case Twitch = 'twitch';

    /**
     * The display name shown on the sign-in buttons.
     */
    public function label(): string
    {
        return match ($this) {
            self::Google => 'Google',
            self::Github => 'GitHub',
            self::Facebook => 'Facebook',
            self::X => 'X',
            self::LinkedIn => 'LinkedIn',
            self::GitLab => 'GitLab',
            self::Bitbucket => 'Bitbucket',
            self::Twitch => 'Twitch',
        };
    }

    /**
     * The brand colour used for the provider icon and hover state.
     */
    public function color(): string
    {
        return match ($this) {
            self::Google => '#EA4335',
            self::Github => '#24292F',
            self::Facebook => '#1877F2',
            self::X => '#0F1419',
            self::LinkedIn => '#0A66C2',
            self::GitLab => '#FC6D26',
            self::Bitbucket => '#0052CC',
            self::Twitch => '#9146FF',
        };
    }

    /**
     * The icon key rendered by the icon component.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Google => 'google',
            self::Github => 'github',
            self::Facebook => 'facebook',
            self::X => 'x',
            self::LinkedIn => 'linkedin',
            self::GitLab => 'gitlab',
            self::Bitbucket => 'bitbucket',
            self::Twitch => 'twitch',
        };
    }

    /**
     * Extra OAuth scopes requested from the provider.
     *
     * @return array<int, string>
     */
    public function scopes(): array
    {
        return match ($this) {
            self::Google => ['openid', 'profile', 'email'],
            self::Github => ['read:user', 'user:email'],
            self::Facebook => ['email', 'public_profile'],
            self::X => ['read:user', 'users.read', 'tweet.read'],
            self::LinkedIn => ['openid', 'profile', 'email'],
            self::GitLab => ['read_user'],
            self::Bitbucket => ['account', 'email'],
            self::Twitch => ['user:read:email'],
        };
    }

    /**
     * Determine whether the provider has been configured through the environment.
     */
    public function isConfigured(): bool
    {
        $config = Config::get("services.{$this->value}", []);

        return filled($config['client_id'] ?? null) && filled($config['client_secret'] ?? null);
    }

    /**
     * The providers that are ready to be used, in display order.
     *
     * @return array<int, self>
     */
    public static function configured(): array
    {
        return array_values(array_filter(self::cases(), fn (self $provider): bool => $provider->isConfigured()));
    }

    /**
     * The providers that still need credentials, in display order.
     *
     * @return array<int, self>
     */
    public static function unconfigured(): array
    {
        return array_values(array_filter(self::cases(), fn (self $provider): bool => ! $provider->isConfigured()));
    }
}
