<?php

namespace Database\Factories;

use App\Models\EvaluationItem;
use App\Models\EvaluationTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvaluationItemFactory extends Factory
{
    protected $model = EvaluationItem::class;

    public function definition()
    {
        $type = $this->faker->randomElement(['score', 'text', 'textarea', 'radio', 'checkbox']);
        $options = in_array($type, ['radio', 'checkbox']) ? ['option1', 'option2', 'option3'] : null;
        
        return [
            'template_id' => EvaluationTemplate::factory(),
            'title' => $this->faker->sentence,
            'field_type' => $type,
            'options' => $options,
            'is_required' => $this->faker->boolean(80),
            'max_score' => $type === 'score' ? $this->faker->numberBetween(1, 20) : null,
            'weight' => $this->faker->optional(0.7)->randomFloat(2, 1, 100),
        ];
    }
}