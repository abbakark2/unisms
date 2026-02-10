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
        $scienceId = DB::table('faculties')->where('name', 'Faculty of Science')->value('id');
        $engineeringId = DB::table('faculties')->where('name', 'Faculty of Engineering')->value('id');
        $fes = DB::table('faculties')->where('name', 'Faculty of Environmental Studies')->value('id');

        DB::table('departments')->insert([
            ['name' => 'Computer Science', 'faculty_id' => $scienceId],
            ['name' => 'Mathematics', 'faculty_id' => $scienceId],
            ['name' => 'Physics', 'faculty_id' => $scienceId],

            ['name' => 'Electrical Engineering', 'faculty_id' => $engineeringId],
            ['name' => 'Mechanical Engineering', 'faculty_id' => $engineeringId],

            ['name' => 'Architecture', 'faculty_id' => $fes],
            ['name' => 'Building', 'faculty_id' => $fes],
            ['name' => 'Fine Arts', 'faculty_id' => $fes],
            ['name' => 'Industrial Design', 'faculty_id' => $fes],
            ['name' => 'Geomatics', 'faculty_id' => $fes],
            ['name' => 'Urban and Regional Planning', 'faculty_id' => $fes],
        ]);
    }
}
