<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\DebtCase;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $mario = Client::firstOrCreate(
            ['email' => 'mario.rossi@example.com'],
            [
                'first_name' => 'Mario',
                'last_name' => 'Rossi',
            ],
        );

        $laura = Client::firstOrCreate(
            ['email' => 'laura.bianchi@example.com'],
            [
                'first_name' => 'Laura',
                'last_name' => 'Bianchi',
            ],
        );

        $paolo = Client::firstOrCreate(
            ['email' => 'paolo.verdi@example.com'],
            [
                'first_name' => 'Paolo',
                'last_name' => 'Verdi',
            ],
        );

        $this->createDebtCase(
            $mario,
            'Personal loan debt',
            '1250.50',
            DebtCase::STATUS_NEW,
        );

        $this->createDebtCase(
            $mario,
            'Credit card debt',
            '780.00',
            DebtCase::STATUS_IN_PROGRESS,
        );

        $this->createDebtCase(
            $laura,
            'Car financing debt',
            '5400.25',
            DebtCase::STATUS_CLOSED,
        );

        $this->createDebtCase(
            $paolo,
            'Unpaid utility bills',
            '320.75',
            DebtCase::STATUS_NEW,
        );
    }

    private function createDebtCase(
        Client $client,
        string $description,
        string $debtAmount,
        string $status,
    ): void {
        $debtCase = DebtCase::firstOrCreate(
            [
                'client_id' => $client->id,
                'description' => $description,
            ],
            [
                'debt_amount' => $debtAmount,
            ],
        );

        $debtCase->debt_amount = $debtAmount;
        $debtCase->status = $status;
        $debtCase->save();
    }
}
