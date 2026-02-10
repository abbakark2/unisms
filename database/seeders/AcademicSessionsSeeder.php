<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademicSessionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('academic_sessions')->insert([
            [
                'name' => '2022/2023',
                'is_active' => false,
            ],
            [
                'name' => '2023/2024',
                'is_active' => false,
            ],
            [
                'name' => '2024/2025',
                'is_active' => false,
            ],
            [
                'name' => '2025/2026',
                'is_active' => true,
            ],
        ]);
    }
}
