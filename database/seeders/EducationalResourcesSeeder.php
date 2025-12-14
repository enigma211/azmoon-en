<?php

namespace Database\Seeders;

use App\Models\ExamType;
use App\Models\ResourceCategory;
use Illuminate\Database\Seeder;

class EducationalResourcesSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create Exam Type: Engineering
        $engineering = ExamType::create([
            'title' => 'Engineering',
            'slug' => 'engineering',
            'description' => 'Educational resources for Engineering exams',
            'icon' => 'academic-cap',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Create Video Category
        ResourceCategory::create([
            'exam_type_id' => $engineering->id,
            'type' => 'video',
            'title' => 'Video Tutorials',
            'slug' => 'engineering-videos',
            'description' => 'Video tutorials for Engineering exams',
            'icon' => 'play-circle',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Create Document Category
        ResourceCategory::create([
            'exam_type_id' => $engineering->id,
            'type' => 'document',
            'title' => 'Study Materials',
            'slug' => 'engineering-documents',
            'description' => 'Study materials and documents for Engineering exams',
            'icon' => 'document-text',
            'sort_order' => 2,
            'is_active' => true,
        ]);
    }
}
