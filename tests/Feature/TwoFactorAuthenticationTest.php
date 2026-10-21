<?php

namespace Tests\Feature;

use App\Models\TwoFactorCode;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Mockery\MockInterface;
use Tests\TestCase;

class TwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_with_two_factor_enabled_is_asked_for_a_code(): void
    {
        $user = User::factory()->withTwoFactor()->create(['email' => 'ada@example.com']);

        $this->mockSms();

        $this->post('/login', [
            'email' => 'ada@example.com',
            'password' => 'password',
        ])->assertRedirect(route('two-factor.login'));

        $this->assertGuest();
        $this->assertDatabaseHas('two_factor_codes', ['user_id' => $user->getKey(), 'purpose' => 'login']);
    }

    public function test_the_code_signs_the_user_in(): void
    {
        $user = User::factory()->withTwoFactor()->create(['email' => 'ada@example.com']);

        $code = $this->signInAndCaptureCode($user);

        $this->post('/login/two-factor', ['code' => $code])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull(
            TwoFactorCode::where('user_id', $user->getKey())->latest('id')->first()->consumed_at
        );
    }

    public function test_an_incorrect_code_is_rejected(): void
    {
        $user = User::factory()->withTwoFactor()->create(['email' => 'ada@example.com']);

        $code = $this->signInAndCaptureCode($user);
        $wrongCode = $code === '000000' ? '111111' : '000000';

        $this->post('/login/two-factor', ['code' => $wrongCode])
            ->assertSessionHasErrors('code');

        $this->assertGuest();
    }

    public function test_an_expired_code_is_rejected(): void
    {
        $user = User::factory()->withTwoFactor()->create(['email' => 'ada@example.com']);

        $code = $this->signInAndCaptureCode($user);

        Carbon::setTestNow(now()->addSeconds((int) config('security.two_factor.code_ttl') + 60));

        $this->post('/login/two-factor', ['code' => $code])
            ->assertSessionHasErrors('code');

        $this->assertGuest();
    }

    public function test_a_code_cannot_be_used_twice(): void
    {
        $user = User::factory()->withTwoFactor()->create(['email' => 'ada@example.com']);

        $usedCode = $this->signInAndCaptureCode($user);

        $this->post('/login/two-factor', ['code' => $usedCode])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->post('/logout');

        $this->post('/login', [
            'email' => 'ada@example.com',
            'password' => 'password',
        ])->assertRedirect(route('two-factor.login'));

        $this->post('/login/two-factor', ['code' => $usedCode])->assertSessionHasErrors('code');

        $this->assertGuest();
    }

    public function test_too_many_attempts_lock_the_code(): void
    {
        $user = User::factory()->withTwoFactor()->create(['email' => 'ada@example.com']);

        $code = $this->signInAndCaptureCode($user);
        $wrongCode = $code === '000000' ? '111111' : '000000';

        for ($attempt = 0; $attempt < (int) config('security.two_factor.max_attempts'); $attempt++) {
            $this->post('/login/two-factor', ['code' => $wrongCode])->assertSessionHasErrors('code');
        }

        $this->post('/login/two-factor', ['code' => $code])->assertSessionHasErrors('code');

        $this->assertGuest();
    }

    public function test_a_new_code_can_be_requested_once_the_cooldown_has_passed(): void
    {
        $user = User::factory()->withTwoFactor()->create(['email' => 'ada@example.com']);

        $code = $this->signInAndCaptureCode($user);

        Carbon::setTestNow(now()->addSeconds((int) config('security.two_factor.resend_interval') + 5));

        $this->post('/login/two-factor/resend')->assertSessionHas('status');

        $this->assertDatabaseCount('two_factor_codes', 2);
        $this->assertSame(
            1,
            TwoFactorCode::where('user_id', $user->getKey())->whereNotNull('consumed_at')->count()
        );
    }

    public function test_requesting_a_code_is_blocked_during_the_cooldown(): void
    {
        $user = User::factory()->withTwoFactor()->create(['email' => 'ada@example.com']);

        $this->signInAndCaptureCode($user);

        $this->post('/login/two-factor/resend')->assertSessionHasErrors('code');

        $this->assertDatabaseCount('two_factor_codes', 1);
    }

    public function test_the_challenge_screen_needs_a_pending_sign_in(): void
    {
        $this->get('/login/two-factor')->assertRedirect(route('login'));
    }

    public function test_the_challenge_is_skipped_when_the_feature_is_turned_off(): void
    {
        config(['security.two_factor.enabled' => false]);

        $user = User::factory()->withTwoFactor()->create(['email' => 'ada@example.com']);

        $this->post('/login', [
            'email' => 'ada@example.com',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_the_code_is_delivered_through_twilio_when_it_is_configured(): void
    {
        config(['security.sms.driver' => 'twilio']);
        config([
            'services.twilio.sid' => 'AC123',
            'services.twilio.secret' => 'secret',
            'services.twilio.from' => '+15550000000',
        ]);

        Http::fake();

        $user = User::factory()->withTwoFactor()->create(['email' => 'ada@example.com']);

        $this->post('/login', [
            'email' => 'ada@example.com',
            'password' => 'password',
        ])->assertRedirect(route('two-factor.login'));

        Http::assertSent(function (HttpRequest $request) use ($user): bool {
            return $request->url() === 'https://api.twilio.com/2010-04-01/Accounts/AC123/Messages.json'
                && $request['To'] === $user->phone
                && str_contains((string) $request['Body'], 'is your code to sign in');
        });
    }

    /**
     * Replace the SMS service with a stub that captures the delivered code.
     */
    private function mockSms(?string &$code = null): void
    {
        $this->mock(SmsService::class, function (MockInterface $mock) use (&$code): void {
            $mock->shouldReceive('send')->andReturnUsing(function (string $phone, string $message) use (&$code): void {
                if (preg_match('/(\d{6})/', $message, $matches)) {
                    $code = $matches[1];
                }
            });
        });
    }

    /**
     * Start a sign-in and return the code that was delivered.
     */
    private function signInAndCaptureCode(User $user): string
    {
        $code = null;

        $this->mockSms($code);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('two-factor.login'));

        $this->assertNotNull($code, 'No verification code was delivered.');

        return $code;
    }
}
