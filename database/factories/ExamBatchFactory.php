<?php

namespace Database\Factories;

use App\Models\ExamBatch;
use App\Models\ExamDomain;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamBatchFactory extends Factory
{
    protected $model = ExamBatch::class;

    public function definition()
    {
        return [
            'exam_domain_id' => ExamDomain::factory(),
            'title' => $this->faker->words(3, true),
            'slug' => $this->faker->slug,
            // Add other necessary fields if any found in model, generally title/slug are minimal
        ];
    }
}
