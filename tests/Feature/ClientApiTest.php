<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_be_created(): void
    {
        $response = $this->postJson('/api/clients', [
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'email' => 'mario.rossi@example.com',
        ]);

        $response
            ->assertCreated()
            ->assertJson([
                'first_name' => 'Mario',
                'last_name' => 'Rossi',
                'email' => 'mario.rossi@example.com',
            ]);

        $this->assertDatabaseHas('clients', [
            'email' => 'mario.rossi@example.com',
        ]);
    }

    public function test_duplicate_email_is_rejected(): void
    {
        Client::create([
            'first_name' => 'Mario',
            'last_name' => 'Rossi',
            'email' => 'mario.rossi@example.com',
        ]);

        $response = $this->postJson('/api/clients', [
            'first_name' => 'Maria',
            'last_name' => 'Bianchi',
            'email' => 'mario.rossi@example.com',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }
}
