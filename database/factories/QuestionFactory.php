<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition()
    {
        return [
            'exam_id' => Exam::factory(),
            'type' => 'multiple_choice',
            'text' => $this->faker->sentence . '?',
            'score' => 10,
            'difficulty' => 'medium',
            'is_deleted' => false,
        ];
    }
}
