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

    public function test_debt_cases_can_be_listed_and_filtered_by_status(): void
    {
        $client = Client::create([
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'email' => 'mario.rossi@example.com',
        ]);

        $newCase = DebtCase::create([
            'client_id' => $client->id,
            'description' => 'New debt case',
            'debt_amount' => '500.00',
        ]);

        $closedCase = DebtCase::create([
            'client_id' => $client->id,
            'description' => 'Closed debt case',
            'debt_amount' => '750.00',
        ]);

        $closedCase->status = DebtCase::STATUS_CLOSED;
        $closedCase->save();

        $this->getJson('/api/cases')
            ->assertOk()
            ->assertJsonCount(2);

        $this->getJson('/api/cases?status=new')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $newCase->id);
    }

    public function test_case_can_be_viewed(): void
    {
        $client = Client::create([
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'email' => 'mario.rossi@example.com',
        ]);

        $debtCase = DebtCase::create([
            'client_id' => $client->id,
            'description' => 'Personal loan debt',
            'debt_amount' => '900.00',
        ]);

        $this->getJson("/api/cases/{$debtCase->id}")
            ->assertOk()
            ->assertJson([
                'id' => $debtCase->id,
                'client_id' => $client->id,
                'description' => 'Personal loan debt',
                'debt_amount' => '900.00',
                'status' => DebtCase::STATUS_NEW,
            ]);
    }

    public function test_missing_case_returns_not_found(): void
    {
        $this->getJson('/api/cases/999999')
            ->assertNotFound();
    }

    public function test_invalid_status_filter_is_rejected(): void
    {
        $this->getJson('/api/cases?status=invalid')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status');
    }

    public function test_closed_case_cannot_be_reopened(): void
    {
        $client = Client::create([
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'email' => 'mario.rossi@example.com',
        ]);
        $debtCase = DebtCase::create([
            'client_id' => $client->id,
            'description' => 'Personal loan debt',
            'debt_amount' => '1000.00',
        ]);

        $this->patchJson("/api/cases/{$debtCase->id}/status", [
            'status' => DebtCase::STATUS_IN_PROGRESS,
        ])
            ->assertOk()
            ->assertJsonPath('status', DebtCase::STATUS_IN_PROGRESS);

        $this->patchJson("/api/cases/{$debtCase->id}/status", [
            'status' => DebtCase::STATUS_CLOSED,
        ])
            ->assertOk()
            ->assertJsonPath('status', DebtCase::STATUS_CLOSED);

        $this->patchJson("/api/cases/{$debtCase->id}/status", [
            'status' => DebtCase::STATUS_IN_PROGRESS,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status');

        $this->assertDatabaseHas('debt_cases', [
            'id' => $debtCase->id,
            'status' => DebtCase::STATUS_CLOSED,
        ]);
    }

    public function test_case_cannot_skip_status_steps(): void
    {
        $client = Client::create([
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'email' => 'mario.rossi@example.com',
        ]);

        $debtCase = DebtCase::create([
            'client_id' => $client->id,
            'description' => 'Personal loan debt',
            'debt_amount' => '1000.00',
        ]);

        $this->patchJson("/api/cases/{$debtCase->id}/status", [
            'status' => DebtCase::STATUS_CLOSED,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status');

        $this->assertDatabaseHas('debt_cases', [
            'id' => $debtCase->id,
            'status' => DebtCase::STATUS_NEW,
        ]);
    }

    public function test_confirming_current_status_is_accepted(): void
    {
        $client = Client::create([
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'email' => 'mario.rossi@example.com',
        ]);

        $debtCase = DebtCase::create([
            'client_id' => $client->id,
            'description' => 'Personal loan debt',
            'debt_amount' => '1000.00',
        ]);

        $this->patchJson("/api/cases/{$debtCase->id}/status", [
            'status' => DebtCase::STATUS_NEW,
        ])
            ->assertOk()
            ->assertJsonPath('status', DebtCase::STATUS_NEW);

        $this->assertDatabaseHas('debt_cases', [
            'id' => $debtCase->id,
            'status' => DebtCase::STATUS_NEW,
        ]);
    }
}
