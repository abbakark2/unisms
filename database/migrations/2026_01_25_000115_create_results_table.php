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

            $table->foreignId('course_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('academic_session_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('semester', ['1st', '2nd']);

            $table->unsignedTinyInteger('first_ca')->default(0);
            $table->unsignedTinyInteger('second_ca')->default(0);
            $table->unsignedTinyInteger('attendance')->default(0);
            $table->unsignedTinyInteger('assignment')->default(0);
            $table->unsignedTinyInteger('exam')->default(0);

            $table->unsignedTinyInteger('total')->default(0);
            $table->string('grade', 2)->nullable();
            $table->decimal('grade_point', 3, 2)->nullable();

            $table->enum('status', [
                'pass',
                'fail',
                'carryover'
            ])->nullable();

            $table->timestamps();

            $table->unique([
                'student_id',
                'course_id',
                'academic_session_id',
                'semester'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
