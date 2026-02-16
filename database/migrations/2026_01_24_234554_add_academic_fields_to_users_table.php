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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')
                ->after('id')
                ->constrained()
                ->restrictOnDelete();


            $table->foreignId('faculty_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();


            $table->foreignId('department_id')
                ->nullable()
                ->after('role_id')
                ->constrained()
                ->nullOnDelete();

            $table->string('phone')->nullable()->after('email');
            $table->string('dob')->nullable()->after('phone');

            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
