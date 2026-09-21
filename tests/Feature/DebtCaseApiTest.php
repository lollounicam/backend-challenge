<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\DebtCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DebtCaseApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_debt_case_can_be_created(): void
    {
        $client = Client::create([
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'email' => 'mario.rossi@example.com',
        ]);

        $response = $this->postJson('/api/cases', [
            'client_id' => $client->id,
            'description' => 'Personal loan debt',
            'debt_amount' => '1250.50',
        ]);

        $response
            ->assertCreated()
            ->assertJson([
                'client_id' => $client->id,
                'description' => 'Personal loan debt',
                'debt_amount' => '1250.50',
                'status' => DebtCase::STATUS_NEW,
            ])
            ->assertJsonStructure([
                'id',
                'opened_at',
            ]);

        $this->assertDatabaseHas('debt_cases', [
            'client_id' => $client->id,
            'description' => 'Personal loan debt',
            'status' => DebtCase::STATUS_NEW,
        ]);
    }

    public function test_debt_case_with_non_positive_amount_is_rejected(): void
    {
        $client = Client::create([
            'first_name' => 'Maria',
            'last_name' => 'Bianchi',
            'email' => 'maria.bianchi@example.com',
        ]);

        $response = $this->postJson('/api/cases', [
            'client_id' => $client->id,
            'description' => 'Credit card debt',
            'debt_amount' => '0.00',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('debt_amount');

        $this->assertDatabaseCount('debt_cases', 0);
    }
}
