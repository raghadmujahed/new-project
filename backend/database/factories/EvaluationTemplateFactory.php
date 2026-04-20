<?php

namespace Database\Factories;

use App\Models\EvaluationTemplate;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvaluationTemplateFactory extends Factory
{
    protected $model = EvaluationTemplate::class;

    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->optional()->paragraph(),
            'form_type' => $this->faker->randomElement(['evaluation', 'student_form']),
            'department_id' => Department::inRandomOrder()->first()?->id ?? null,
        ];
    }

    public function forDepartment($departmentId)
    {
        return $this->state(fn (array $attributes) => [
            'department_id' => $departmentId,
        ]);
    }

    public function global()
    {
        return $this->state(fn (array $attributes) => [
            'department_id' => null,
        ]);
    }
}