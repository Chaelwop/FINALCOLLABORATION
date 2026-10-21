<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PortfolioPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_landing_page_renders(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Build a portfolio')
            ->assertSee('PortfolioCraft');
    }

    public function test_the_dashboard_lists_the_portfolios_of_the_signed_in_user(): void
    {
        $user = User::factory()->create(['name' => 'Ada Lovelace']);
        Portfolio::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'full_name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'template' => 'modern',
            'is_public' => true,
            'data' => ['jobTitle' => 'Engineer', 'skills' => ['Laravel']],
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Your portfolios')
            ->assertSee('Ada Lovelace');
    }

    public function test_the_editor_renders_for_the_owner(): void
    {
        $user = User::factory()->create();
        $portfolio = Portfolio::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'full_name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'template' => 'simple',
            'is_public' => false,
            'data' => ['jobTitle' => 'Engineer'],
        ]);

        $this->actingAs($user)
            ->get("/portfolios/{$portfolio->id}/edit")
            ->assertOk()
            ->assertSee('Edit portfolio')
            ->assertSee('Ada Lovelace');
    }

    public function test_the_editor_is_not_available_for_other_users(): void
    {
        $portfolio = Portfolio::create([
            'id' => (string) Str::uuid(),
            'user_id' => User::factory()->create()->id,
            'full_name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'template' => 'simple',
            'is_public' => false,
            'data' => [],
        ]);

        $this->actingAs(User::factory()->create())
            ->get("/portfolios/{$portfolio->id}/edit")
            ->assertNotFound();
    }

    public function test_the_live_preview_fragment_renders_a_template(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/portfolios/preview', [
                'template' => 'modern',
                'portfolio' => [
                    'fullName' => 'Ada Lovelace',
                    'jobTitle' => 'Engineer',
                    'aboutMe' => 'Builds thoughtful software.',
                    'skills' => ['Laravel'],
                ],
            ], ['Accept' => 'text/html'])
            ->assertOk()
            ->assertSee('Ada Lovelace')
            ->assertSee('Engineer');
    }

    public function test_a_public_portfolio_can_be_viewed_without_signing_in(): void
    {
        $portfolio = Portfolio::create([
            'id' => (string) Str::uuid(),
            'user_id' => User::factory()->create()->id,
            'full_name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'template' => 'simple',
            'is_public' => true,
            'data' => ['jobTitle' => 'Engineer'],
        ]);

        $this->get("/p/{$portfolio->id}")
            ->assertOk()
            ->assertSee('Ada Lovelace');
    }

    public function test_a_private_portfolio_is_not_public(): void
    {
        $portfolio = Portfolio::create([
            'id' => (string) Str::uuid(),
            'user_id' => User::factory()->create()->id,
            'full_name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'template' => 'simple',
            'is_public' => false,
            'data' => [],
        ]);

        $this->get("/p/{$portfolio->id}")->assertNotFound();
    }
}
