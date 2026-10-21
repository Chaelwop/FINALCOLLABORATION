<?php

namespace Tests\Feature;

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_settings_screen_requires_authentication(): void
    {
        $this->get('/settings')->assertRedirect('/login');
    }

    public function test_the_settings_screen_lists_the_providers(): void
    {
        $user = User::factory()->create();
        $user->socialAccounts()->create([
            'provider' => 'github',
            'provider_id' => '4242',
            'token' => 'token',
        ]);

        $this->actingAs($user)
            ->get('/settings')
            ->assertOk()
            ->assertSee('Connected providers')
            ->assertSee('GitHub')
            ->assertSee('Connected');
    }

    public function test_a_connected_provider_can_be_disconnected(): void
    {
        $user = User::factory()->create();
        $account = $user->socialAccounts()->create([
            'provider' => 'github',
            'provider_id' => '4242',
            'token' => 'token',
        ]);

        $this->actingAs($user)
            ->delete("/settings/social-accounts/{$account->id}")
            ->assertRedirect('/settings');

        $this->assertDatabaseMissing('social_accounts', ['id' => $account->id]);
    }

    public function test_a_provider_belonging_to_another_user_cannot_be_disconnected(): void
    {
        $account = SocialAccount::create([
            'user_id' => User::factory()->create()->id,
            'provider' => 'github',
            'provider_id' => '4242',
            'token' => 'token',
        ]);

        $this->actingAs(User::factory()->create())
            ->delete("/settings/social-accounts/{$account->id}")
            ->assertNotFound();
    }
}
