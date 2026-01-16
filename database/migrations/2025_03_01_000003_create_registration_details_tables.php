<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Consolidated migration for registration details tables.
 * Includes: academic_year_id, destination_class.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained()->nullOnDelete(); // Consolidated

            // Previous Education
            $table->string('previous_school_level'); // SD/Sederajat, SMP/Sederajat, SMA/Sederajat
            $table->string('previous_school_name');
            $table->string('previous_school_npsn')->nullable();
            $table->text('previous_school_address');

            // Destination
            $table->foreignId('destination_institution_id')->constrained('institutions');
            $table->string('destination_class')->nullable(); // Consolidated from add_destination_class

            // Funding
            $table->string('funding_source'); // Orang Tua/Wali/Ditanggung sendiri/Lainnya

            $table->timestamps();
        });

        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();

            $table->string('type'); // kk, akta, ijazah
            $table->string('file_path');
            $table->enum('status', ['pending', 'valid', 'invalid'])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_documents');
        Schema::dropIfExists('registrations');
    }
};
