<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PortfolioApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'fullName' => 'Ada Lovelace',
            'jobTitle' => 'Software Engineer',
            'email' => 'ada@example.com',
            'aboutMe' => 'Builds thoughtful software.',
            'skills' => ['Laravel'],
            'projects' => [],
            'experience' => [],
            'template' => 'simple',
            'isPublic' => false,
        ], $overrides);
    }

    public function test_guests_cannot_reach_the_portfolio_api(): void
    {
        $this->getJson('/api/portfolios')->assertUnauthorized();
        $this->postJson('/api/portfolios', $this->payload())->assertUnauthorized();
    }

    public function test_portfolios_can_be_created_listed_updated_and_deleted(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $created = $this->postJson('/api/portfolios', $this->payload())
            ->assertCreated()
            ->assertJsonPath('fullName', 'Ada Lovelace');

        $portfolioId = $created->json('id');
        $this->assertDatabaseHas('portfolios', [
            'id' => $portfolioId,
            'full_name' => 'Ada Lovelace',
            'user_id' => $user->id,
        ]);

        $this->getJson('/api/portfolios')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $portfolioId);

        $this->putJson("/api/portfolios/{$portfolioId}", $this->payload(['fullName' => 'Ada Byron']))
            ->assertOk()
            ->assertJsonPath('fullName', 'Ada Byron');

        $this->deleteJson("/api/portfolios/{$portfolioId}")->assertOk();
        $this->assertDatabaseMissing('portfolios', ['id' => $portfolioId]);
    }

    public function test_portfolios_are_scoped_to_their_owner(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $portfolio = Portfolio::create([
            'id' => (string) Str::uuid(),
            'user_id' => $owner->id,
            'full_name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'template' => 'simple',
            'is_public' => false,
            'data' => [],
        ]);

        $this->actingAs($other);

        $this->getJson('/api/portfolios')->assertOk()->assertJsonCount(0);
        $this->putJson("/api/portfolios/{$portfolio->id}", $this->payload())->assertNotFound();
        $this->deleteJson("/api/portfolios/{$portfolio->id}")->assertNotFound();
    }

    public function test_the_template_must_be_supported(): void
    {
        $this->actingAs(User::factory()->create());

        $this->postJson('/api/portfolios', $this->payload(['template' => 'neon']))
            ->assertStatus(422)
            ->assertJsonValidationErrors('template');
    }
}
