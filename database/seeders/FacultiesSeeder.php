<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacultiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('faculties')->insert([
            [
                'name' => 'Faculty of Arts',
                'abbreviation' => 'FA',
            ],
            [
                'name' => 'Faculty of Education',
                'abbreviation' => 'FE',
            ],
            [
                'name' => 'Faculty of Engineering',
                'abbreviation' => 'Eng',
            ],
            [
                'name' => 'Faculty of Science',
                'abbreviation' => 'FS',
            ],
            [
                'name' => 'Faculty of Social Sciences',
                'abbreviation' => 'FSS',
            ],
            [
                'name' => 'Faculty of Environmental Studies',
                'abbreviation' => 'FES',
            ],
        ]);
    }
}
