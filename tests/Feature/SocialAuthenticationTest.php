<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

class SocialAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.google.client_id' => 'google-client-id',
            'services.google.client_secret' => 'google-client-secret',
            'services.github.client_id' => 'github-client-id',
            'services.github.client_secret' => 'github-client-secret',
        ]);
    }

    public function test_the_sign_in_screen_lists_configured_providers(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Continue with Google')
            ->assertSee('Continue with GitHub');
    }

    public function test_a_visitor_is_redirected_to_the_provider(): void
    {
        $this->get('/auth/github/redirect')
            ->assertRedirectContains('github.com/login/oauth/authorize');
    }

    public function test_an_unknown_provider_returns_a_not_found_response(): void
    {
        $this->get('/auth/myspace/redirect')->assertNotFound();
    }

    public function test_a_provider_without_credentials_reports_a_configuration_error(): void
    {
        config(['services.linkedin.client_id' => null, 'services.linkedin.client_secret' => null]);

        $this->get('/auth/linkedin/redirect')
            ->assertRedirect()
            ->assertSessionHasErrors('social');
    }

    public function test_a_social_account_creates_and_signs_in_a_user(): void
    {
        $this->mockSocialiteUser('github', '4242', 'ada@example.com', 'Ada Lovelace');

        $this->get('/auth/github/callback')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'ada@example.com']);
        $this->assertDatabaseHas('social_accounts', [
            'provider' => 'github',
            'provider_id' => '4242',
        ]);
    }

    public function test_a_social_account_links_to_an_existing_user_by_email(): void
    {
        $user = User::factory()->create(['email' => 'ada@example.com']);

        $this->mockSocialiteUser('google', '998877', 'ada@example.com', 'Ada Lovelace');

        $this->get('/auth/google/callback')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertSame(1, $user->socialAccounts()->count());
    }

    public function test_an_existing_social_account_signs_the_user_in_again(): void
    {
        $user = User::factory()->create();
        $user->socialAccounts()->create([
            'provider' => 'github',
            'provider_id' => '4242',
            'token' => 'old-token',
        ]);

        $this->mockSocialiteUser('github', '4242', 'ada@example.com', 'Ada Lovelace');

        $this->get('/auth/github/callback')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('social_accounts', [
            'provider' => 'github',
            'provider_id' => '4242',
            'token' => 'new-token',
        ]);
    }

    /**
     * Replace the Socialite driver with a stub that returns the given user.
     */
    private function mockSocialiteUser(string $provider, string $id, string $email, string $name): void
    {
        $socialiteUser = (new SocialiteUser)->setRaw([])->map([
            'id' => $id,
            'nickname' => $name,
            'name' => $name,
            'email' => $email,
            'avatar' => 'https://example.com/avatar.png',
        ]);

        $socialiteUser->token = 'new-token';

        Socialite::shouldReceive('driver->scopes->redirect')->andReturn(Socialite::driver($provider));
        Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);
    }
}
