<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class TwoFactorSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_settings_screen_shows_the_two_factor_status(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/settings')
            ->assertOk()
            ->assertSee('Two factor authentication')
            ->assertSee('Not enabled');
    }

    public function test_the_two_factor_section_is_hidden_when_the_feature_is_turned_off(): void
    {
        config(['security.two_factor.enabled' => false]);

        $this->actingAs(User::factory()->create())
            ->get('/settings')
            ->assertOk()
            ->assertDontSee('Two factor authentication');
    }

    public function test_a_user_can_enable_two_factor_authentication(): void
    {
        $user = User::factory()->create();

        $code = $this->requestCode($user, '+1 (555) 010-9999');

        $this->assertDatabaseHas('users', [
            'id' => $user->getKey(),
            'phone' => '+15550109999',
            'two_factor_enabled' => false,
        ]);

        $this->actingAs($user)
            ->post('/settings/two-factor/confirm', ['code' => $code])
            ->assertRedirect(route('settings'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'id' => $user->getKey(),
            'two_factor_enabled' => true,
        ]);
        $this->assertNotNull($user->fresh()->two_factor_confirmed_at);
    }

    public function test_enabling_two_factor_authentication_requires_the_right_code(): void
    {
        $user = User::factory()->create();

        $code = $this->requestCode($user, '+15550109999');
        $wrongCode = $code === '000000' ? '111111' : '000000';

        $this->actingAs($user)
            ->post('/settings/two-factor/confirm', ['code' => $wrongCode])
            ->assertSessionHasErrors('code');

        $this->assertDatabaseHas('users', [
            'id' => $user->getKey(),
            'two_factor_enabled' => false,
        ]);
    }

    public function test_a_user_can_turn_two_factor_authentication_off(): void
    {
        $user = User::factory()->withTwoFactor()->create();

        $this->actingAs($user)
            ->delete('/settings/two-factor')
            ->assertRedirect(route('settings'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'id' => $user->getKey(),
            'two_factor_enabled' => false,
            'phone' => null,
        ]);
    }

    public function test_the_phone_number_is_validated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/settings/two-factor', ['phone' => 'not a phone number'])
            ->assertSessionHasErrors('phone');

        $this->assertDatabaseHas('users', [
            'id' => $user->getKey(),
            'phone' => null,
        ]);
    }

    public function test_a_phone_number_cannot_be_added_while_two_factor_is_enabled(): void
    {
        $user = User::factory()->withTwoFactor()->create();

        $this->actingAs($user)
            ->post('/settings/two-factor', ['phone' => '+15550109999'])
            ->assertSessionHasErrors('phone');

        $this->assertDatabaseHas('users', [
            'id' => $user->getKey(),
            'phone' => $user->phone,
        ]);
    }

    /**
     * Ask for a code and return the code that was delivered.
     */
    private function requestCode(User $user, string $phone): string
    {
        $code = null;

        $this->mock(SmsService::class, function (MockInterface $mock) use (&$code): void {
            $mock->shouldReceive('send')->andReturnUsing(function (string $recipient, string $message) use (&$code): void {
                if (preg_match('/(\d{6})/', $message, $matches)) {
                    $code = $matches[1];
                }
            });
        });

        $this->actingAs($user)
            ->post('/settings/two-factor', ['phone' => $phone])
            ->assertRedirect(route('settings'));

        $this->assertNotNull($code, 'No verification code was delivered.');

        return $code;
    }
}
