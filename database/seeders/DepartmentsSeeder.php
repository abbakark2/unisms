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

        $now = now();

        DB::table('departments')->insert([
            // Science
            ['name' => 'Computer Science',  'code' => 'CSC', 'faculty_id' => $scienceId, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mathematics',       'code' => 'MTH', 'faculty_id' => $scienceId, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Physics',           'code' => 'PHY', 'faculty_id' => $scienceId, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chemistry',         'code' => 'CHM', 'faculty_id' => $scienceId, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Biology',           'code' => 'BIO', 'faculty_id' => $scienceId, 'created_at' => $now, 'updated_at' => $now],

            // Engineering
            ['name' => 'Electrical Engineering', 'code' => 'EEE', 'faculty_id' => $engineeringId, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mechanical Engineering', 'code' => 'MEE', 'faculty_id' => $engineeringId, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Civil Engineering',      'code' => 'CVE', 'faculty_id' => $engineeringId, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chemical Engineering',   'code' => 'CHE', 'faculty_id' => $engineeringId, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Computer Engineering',   'code' => 'CSE', 'faculty_id' => $engineeringId, 'created_at' => $now, 'updated_at' => $now],

            // Environmental Studies
            ['name' => 'Architecture',              'code' => 'ARC', 'faculty_id' => $fes, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Building',                  'code' => 'BLD', 'faculty_id' => $fes, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Fine Arts',                 'code' => 'FAR', 'faculty_id' => $fes, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Industrial Design',         'code' => 'IND', 'faculty_id' => $fes, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Geomatics',                 'code' => 'GEO', 'faculty_id' => $fes, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Urban and Regional Planning', 'code' => 'URP', 'faculty_id' => $fes, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
