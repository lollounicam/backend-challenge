<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('debt_cases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('description', 500);
            $table->decimal('debt_amount', 12, 2)->unsigned();

            $table->enum('status', [
                'new',
                'in_progress',
                'closed',
            ])->default('new');

            $table->timestamp('opened_at')->useCurrent();

            $table->index('status');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debt_cases');
    }
};
