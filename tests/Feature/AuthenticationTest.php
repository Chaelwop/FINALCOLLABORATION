<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_login_screen_can_be_rendered(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Welcome back')
            ->assertSee('Sign in');
    }

    public function test_the_registration_screen_can_be_rendered(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Create your account');
    }

    public function test_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'ada@example.com']);
    }

    public function test_registration_requires_a_unique_email(): void
    {
        User::factory()->create(['email' => 'ada@example.com']);

        $this->post('/register', [
            'name' => 'Ada Byron',
            'email' => 'ada@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_users_can_sign_in_with_their_password(): void
    {
        $user = User::factory()->create(['email' => 'ada@example.com']);

        $response = $this->post('/login', [
            'email' => 'ada@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_users_cannot_sign_in_with_an_invalid_password(): void
    {
        User::factory()->create(['email' => 'ada@example.com']);

        $this->post('/login', [
            'email' => 'ada@example.com',
            'password' => 'not-the-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_users_can_sign_out(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/logout')
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_the_dashboard_requires_authentication(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_the_password_reset_screen_can_be_rendered(): void
    {
        $this->get('/forgot-password')
            ->assertOk()
            ->assertSee('Forgot your password?');
    }
}
