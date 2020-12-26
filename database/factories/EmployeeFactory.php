<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EmployeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'lastname' => $this->faker->lastName,
            'mobile' => '+989' . random_int(1111, 9999) . random_int(11111, 99999),
            'personnelCode' => '333999333',
            'email' => $this->faker->unique()->safeEmail,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'position' => 'مدیر IT',
            'isAdmin' => false,
            'avatar' => $this->faker->imageUrl(),
            'active' => true,
            'verified_at' => now(),
            'dateBirth' => $this->faker->date('Y-m-d'),
            'ip' => $this->faker->ipv4,
            'website' => $this->faker->url,

        ];
    }

    public function changeFields($name, $lastname, $mobile, $personnelCode, $position, $isAdmin = false)
    {
        return $this->state([
            'name' => $name,
            'lastname' => $lastname,
            'mobile' => $mobile,
            'personnelCode' => $personnelCode,
            'position' => $position,
            'isAdmin' => $isAdmin,
        ]);
    }
}
