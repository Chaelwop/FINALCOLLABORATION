# PortfolioCraft

A portfolio builder with account-based sign-in, social login providers and three
crafted portfolio templates.

## Run locally

Requirements: PHP 8.3 or later, Composer, and Node.js/npm (Node 20.19+ or 22+ for the Vite 8 build).

```bash
composer run setup
php artisan serve
```

Open <http://127.0.0.1:8000>. The setup command installs PHP and frontend
dependencies, creates the local environment and SQLite database if they do not
exist, generates the application key, and runs the database migrations.

## Social sign-in

Social login is powered by [Laravel Socialite](https://laravel.com/docs/socialite).
A provider only appears on the sign-in and registration screens once its client
id and client secret are present in `.env`; the rest are shown as "not
configured" so the interface never breaks.

| Provider  | Environment variables                     | Callback URL                            |
|-----------|-------------------------------------------|-----------------------------------------|
| Google    | `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET` | `{APP_URL}/auth/google/callback`        |
| GitHub    | `GITHUB_CLIENT_ID`, `GITHUB_CLIENT_SECRET` | `{APP_URL}/auth/github/callback`        |
| Facebook  | `FACEBOOK_CLIENT_ID`, `FACEBOOK_CLIENT_SECRET` | `{APP_URL}/auth/facebook/callback` |
| X         | `X_CLIENT_ID`, `X_CLIENT_SECRET`           | `{APP_URL}/auth/x/callback`             |
| LinkedIn  | `LINKEDIN_CLIENT_ID`, `LINKEDIN_CLIENT_SECRET` | `{APP_URL}/auth/linkedin/callback`  |
| GitLab    | `GITLAB_CLIENT_ID`, `GITLAB_CLIENT_SECRET` | `{APP_URL}/auth/gitlab/callback`        |
| Bitbucket | `BITBUCKET_CLIENT_ID`, `BITBUCKET_CLIENT_SECRET` | `{APP_URL}/auth/bitbucket/callback` |
| Twitch    | `TWITCH_CLIENT_ID`, `TWITCH_CLIENT_SECRET` | `{APP_URL}/auth/twitch/callback`        |

1. Create an OAuth app with the provider and copy the client id and secret into `.env`.
2. Register the callback URL above with the provider.
3. Clear the config cache: `php artisan config:clear`.

Signing in with a provider creates the account on first use, links to an
existing account when the e-mail address matches, and lets signed-in users
connect additional providers from **Account settings**.

## Tests

```bash
php artisan test
```

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
# FINALCOLLABORATION
