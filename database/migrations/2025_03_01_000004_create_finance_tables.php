<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Consolidated migration for bills and transactions tables.
 * Combines all previous incremental migrations into one clean file.
 */
return new class extends Migration {
    public function up(): void
    {
        // Create bills table
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();

            $table->decimal('amount', 15, 2); // Total aggregated amount
            $table->decimal('remaining_amount', 15, 2);
            $table->enum('status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->text('description')->nullable(); // Components breakdown for reference

            $table->timestamps();

            // Performance indexes
            $table->index(['student_id', 'status']);
            $table->index('institution_id');
        });

        // Create transactions table
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['cash', 'transfer'])->default('cash');
            $table->date('transaction_date');
            $table->string('proof_image')->nullable();
            $table->text('notes')->nullable();
            $table->string('verification_token', 64)->nullable()->unique();

            // Hybrid Cash Collection fields
            $table->enum('payment_location', ['PANITIA', 'UNIT'])->default('PANITIA');
            $table->boolean('is_settled')->default(false);

            $table->timestamps();

            // Performance indexes
            $table->index(['student_id', 'transaction_date']);
            $table->index('payment_location');
            $table->index('is_settled');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('bills');
    }
};
