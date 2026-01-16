<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Consolidated migration for fund_transfers table.
 * Combines all previous incremental migrations into one clean file.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('fund_transfers', function (Blueprint $table) {
            $table->id();
            
            // Core relationships
            $table->foreignId('institution_id')->constrained()->restrictOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('bill_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete(); // Creator
            
            // Transfer details
            $table->decimal('amount', 15, 2);
            $table->date('transfer_date');
            $table->string('transfer_method')->default('cash');
            $table->text('notes')->nullable();
            
            // Settlement workflow status
            $table->enum('status', ['PENDING', 'APPROVED', 'COMPLETED', 'REJECTED'])->default('PENDING');
            
            // Approval tracking
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Receipt tracking
            $table->timestamp('received_at')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();

            // Performance indexes
            $table->index('institution_id');
            $table->index('student_id');
            $table->index('status');
            $table->index(['institution_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fund_transfers');
    }
};
