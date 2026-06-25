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
        Schema::create('course_registrations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('course_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('academic_session_id')
                ->constrained('academic_sessions')
                ->cascadeOnDelete();

            $table->enum('semester', ['1st', '2nd']);
            $table->boolean('is_carryover')->default(false);

            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('approved');

            // Audit — who registered this course (student self or admin)
            $table->foreignId('registered_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(
                ['student_id', 'course_id', 'academic_session_id'],
                'cr_student_course_session_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_registrations');
    }
};
