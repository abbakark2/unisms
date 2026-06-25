<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // This tells Laravel: "Create a User and use its ID"
            'user_id' => User::factory(),

            // This assumes you have a DepartmentFactory
            'department_id' => Department::inRandomOrder()->first()?->id ?? Department::factory(),

            'matric_number' => 'U/CS/' . fake()->numberBetween(10, 25) . '/' . fake()->numberBetween(200, 400),
            'admission_year' => fake()->year(),
            'graduation_year' => fake()->year(),

            // Use fake()->randomElement() for cleaner code
            'level' => fake()->randomElement(['100', '200', '300', '400', '500']),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'status' => fake()->randomElement(['active', 'inactive', 'spillover', 'graduated']),
        ];
    }
}
