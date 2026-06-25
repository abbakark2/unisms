<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $scienceId     = DB::table('faculties')->where('name', 'Faculty of Science')->value('id');
        $engineeringId = DB::table('faculties')->where('name', 'Faculty of Engineering')->value('id');
        $fes           = DB::table('faculties')->where('name', 'Faculty of Environmental Studies')->value('id');

        DB::table('departments')->insert([
            // Science
            ['name' => 'Computer Science',  'code' => 'CSC', 'faculty_id' => $scienceId],
            ['name' => 'Mathematics',       'code' => 'MTH', 'faculty_id' => $scienceId],
            ['name' => 'Physics',           'code' => 'PHY', 'faculty_id' => $scienceId],

            // Engineering
            ['name' => 'Electrical Engineering', 'code' => 'EEE', 'faculty_id' => $engineeringId],
            ['name' => 'Mechanical Engineering', 'code' => 'MEE', 'faculty_id' => $engineeringId],

            // Environmental Studies
            ['name' => 'Architecture',              'code' => 'ARC', 'faculty_id' => $fes],
            ['name' => 'Building',                  'code' => 'BLD', 'faculty_id' => $fes],
            ['name' => 'Fine Arts',                 'code' => 'FAR', 'faculty_id' => $fes],
            ['name' => 'Industrial Design',         'code' => 'IND', 'faculty_id' => $fes],
            ['name' => 'Geomatics',                 'code' => 'GEO', 'faculty_id' => $fes],
            ['name' => 'Urban and Regional Planning', 'code' => 'URP', 'faculty_id' => $fes],
        ]);
    }
}
