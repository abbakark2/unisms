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
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('matric_number')->unique();
            $table->year('admission_year')->nullable();
            $table->year('graduation_year')->nullable();
            $table->enum('level', ['100', '200', '300', '400', '500'])
              ->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->unsignedBigInteger('entry_session_id')->nullable();
            $table->enum('status', [
                'active',
                'inactive',
                'spillover',
                'graduated',
                'withdrawn'
            ])->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
