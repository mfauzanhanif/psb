<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Consolidated migration for master data tables.
 * Includes: institutions, academic_years, fee_components (with type).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // type: pondok, madrasah, smp, ma, mts
            $table->string('type');
            $table->timestamps();
        });

        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., 2025/2026
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('fee_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., Pendaftaran, Uang Gedung
            $table->enum('type', ['yearly', 'monthly'])->default('yearly'); // Consolidated from add_type_to_fee_components
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });

        // Add FK constraint to users.institution_id (consolidated from add_fk_to_users_table)
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('institution_id')->references('id')->on('institutions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['institution_id']);
        });
        Schema::dropIfExists('fee_components');
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('institutions');
    }
};
