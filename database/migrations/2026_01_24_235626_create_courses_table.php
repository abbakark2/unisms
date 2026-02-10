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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_code');
            $table->string('course_title');
            $table->unsignedTinyInteger('unit');
            $table->unsignedTinyInteger('level');
            $table->enum('semester', ['1st', '2nd']);

            $table->foreignId('department_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['course_code', 'department_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
