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
        Schema::create('results', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('course_registration_id')
                ->constrained('course_registrations')
                ->cascadeOnDelete();

            $table->foreignId('academic_session_id')
                ->constrained('academic_sessions')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('first_ca')->default(0);
            $table->unsignedTinyInteger('second_ca')->default(0);
            $table->unsignedTinyInteger('attendance')->default(0);
            $table->unsignedTinyInteger('assignment')->default(0);
            $table->unsignedTinyInteger('exam')->default(0);
            $table->unsignedTinyInteger('total')->default(0);

            $table->string('grade', 2)->nullable();
            $table->decimal('grade_point', 3, 2)->nullable();

            $table->enum('status', ['pass', 'fail', 'incomplete'])->nullable();

            $table->timestamps();

            $table->unique(
                ['student_id', 'course_registration_id'],
                'result_student_reg_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
