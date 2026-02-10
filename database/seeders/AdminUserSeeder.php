<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRoleId = DB::table('roles')->where('name', 'super_admin')->value('id');
        $csDeptId = DB::table('departments')->where('name', 'Computer Science')->value('id');

        DB::table('users')->insert([
            'name' => 'System Administrator',
            'email' => 'admin@fes.edu.ng',
            'password' => Hash::make('password'),
            'role_id' => $adminRoleId,
            'department_id' => $csDeptId,
            'phone' => '07030098120',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
