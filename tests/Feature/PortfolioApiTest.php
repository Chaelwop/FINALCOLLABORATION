<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_portfolios_can_be_created_listed_updated_and_deleted(): void
    {
        $portfolio = [
            'fullName' => 'Ada Lovelace',
            'jobTitle' => 'Software Engineer',
            'email' => 'ada@example.com',
            'aboutMe' => 'Builds thoughtful software.',
            'skills' => ['Laravel'],
            'projects' => [],
            'experience' => [],
        ];

        $created = $this->postJson('/api/portfolios', $portfolio)
            ->assertCreated()
            ->assertJsonPath('fullName', 'Ada Lovelace');

        $portfolioId = $created->json('id');
        $this->assertDatabaseHas('portfolios', [
            'id' => $portfolioId,
            'full_name' => 'Ada Lovelace',
        ]);

        $this->getJson('/api/portfolios')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $portfolioId);

        $this->putJson("/api/portfolios/{$portfolioId}", [
            ...$portfolio,
            'fullName' => 'Ada Byron',
        ])->assertOk()->assertJsonPath('fullName', 'Ada Byron');

        $this->deleteJson("/api/portfolios/{$portfolioId}")->assertOk();
        $this->assertDatabaseMissing('portfolios', ['id' => $portfolioId]);
    }
}