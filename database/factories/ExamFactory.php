<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\ExamBatch;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamFactory extends Factory
{
    protected $model = Exam::class;

    public function definition()
    {
        return [
            'exam_batch_id' => ExamBatch::factory(), // We might need an ExamBatchFactory too, or just create one inline in tests
            'title' => $this->faker->sentence,
            'slug' => $this->faker->slug,
            'description' => $this->faker->paragraph,
            'duration_minutes' => 60,
            'pass_threshold' => 50,
            'is_published' => true,
            'total_score' => 100,
            'negative_score_ratio' => 0,
        ];
    }
}
