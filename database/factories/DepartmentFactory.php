<?php

namespace Database\Factories;

use App\Models\Faculty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departments = [
            ['name' => 'Computer Science', 'code' => 'CSC'],
            ['name' => 'Mathematics', 'code' => 'MTH'],
            ['name' => 'Physics', 'code' => 'PHY'],
            ['name' => 'Chemistry', 'code' => 'CHM'],
            ['name' => 'Biology', 'code' => 'BIO'],
            ['name' => 'Electrical Engineering', 'code' => 'EEE'],
            ['name' => 'Mechanical Engineering', 'code' => 'MEE'],
            ['name' => 'Civil Engineering', 'code' => 'CEE'],
            ['name' => 'Architecture', 'code' => 'ARC'],
            ['name' => 'Building', 'code' => 'BLD'],
        ];

        $dept = $this->faker->unique()->randomElement($departments);

        return [
            'name' => $dept['name'],
            'code' => $dept['code'],
            'faculty_id' => Faculty::factory(),
        ];
    }
}
