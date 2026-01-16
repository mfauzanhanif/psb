<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Consolidated migration for students tables.
 * Includes: status index for performance.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique(); // Generated e.g., REG-2025-0001

            // Biodata
            $table->string('full_name');
            $table->string('nik')->unique();
            $table->string('nisn')->nullable();
            $table->string('place_of_birth');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female']);

            // Family Position
            $table->integer('child_number');
            $table->integer('total_siblings');

            // Address
            $table->text('address_street'); // Jalan/Blok/RT/RW
            $table->string('village'); // Desa/Kelurahan
            $table->string('district'); // Kecamatan
            $table->string('regency'); // Kabupaten
            $table->string('province');
            $table->string('postal_code')->nullable();

            // Status
            $table->enum('status', ['draft', 'verified', 'accepted', 'rejected'])->default('draft');

            $table->timestamps();

            // Performance indexes (consolidated from add_indexes_for_performance)
            $table->index('status');
        });

        Schema::create('student_parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();

            // father, mother, guardian
            $table->enum('type', ['father', 'mother', 'guardian']);

            $table->string('name');
            $table->enum('life_status', ['alive', 'deceased', 'unknown'])->default('alive');
            $table->string('nik')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('education')->nullable();
            $table->string('pesantren_education')->nullable();
            $table->string('job')->nullable();
            $table->string('income')->nullable();
            $table->string('phone_number')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_parents');
        Schema::dropIfExists('students');
    }
};
