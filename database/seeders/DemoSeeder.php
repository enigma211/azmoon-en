<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\ExamDomain;
use App\Models\ExamBatch;
use App\Models\Exam;
use App\Models\ExamSection;
use App\Models\Question;
use App\Models\Choice;
use App\Models\ResourceItem;
use App\Models\User;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Domain
        $domain = ExamDomain::firstOrCreate(
            ['slug' => 'engineering'],
            [
                'title' => 'Engineering',
                'is_active' => true,
            ]
        );

        // 2) Batch
        $batch = ExamBatch::firstOrCreate(
            [
                'exam_domain_id' => $domain->id,
                'slug' => 'summer-2025',
            ],
            [
                'title' => 'Summer 2025',
                'is_active' => true,
                'starts_at' => Carbon::now()->subDays(10),
                'ends_at' => Carbon::now()->addDays(20),
            ]
        );

        // 3) Exam
        $exam = Exam::firstOrCreate(
            [
                'exam_batch_id' => $batch->id,
                'slug' => 'demo-10-questions',
            ],
            [
                'title' => 'Demo Exam (10 Questions)',
                'description' => 'Sample exam to test the system.',
                'duration_minutes' => 20,
                'pass_threshold' => 60.0,
                'is_published' => true,
            ]
        );

        // 4) Section
        $section = ExamSection::firstOrCreate(
            [
                'exam_id' => $exam->id,
                'title' => 'General Section',
            ],
            [
                'order' => 1,
            ]
        );

        // Helper to create questions succinctly
        $makeQuestion = function(array $attrs) use ($section) {
            $q = Question::create(array_merge([
                'exam_section_id' => $section->id,
                'difficulty' => 'easy',
                'score' => 1,
            ], $attrs));
            return $q;
        };

        // 5) Create 10 questions (mixed types)
        // Single choice x3
        for ($i = 1; $i <= 3; $i++) {
            $q = $makeQuestion([
                'type' => 'single_choice',
                'text' => "Single Choice Question {$i}",
                'negative_score' => 0,
            ]);
            Choice::create(['question_id' => $q->id, 'text' => 'Option 1', 'is_correct' => false, 'order' => 1]);
            Choice::create(['question_id' => $q->id, 'text' => 'Option 2', 'is_correct' => true,  'order' => 2]);
            Choice::create(['question_id' => $q->id, 'text' => 'Option 3', 'is_correct' => false, 'order' => 3]);
            Choice::create(['question_id' => $q->id, 'text' => 'Option 4', 'is_correct' => false, 'order' => 4]);
        }

        // Multi choice x3
        for ($i = 1; $i <= 3; $i++) {
            $q = $makeQuestion([
                'type' => 'multi_choice',
                'text' => "Multi Choice Question {$i}",
                'negative_score' => 0.25,
            ]);
            Choice::create(['question_id' => $q->id, 'text' => 'Option A', 'is_correct' => true,  'order' => 1]);
            Choice::create(['question_id' => $q->id, 'text' => 'Option B', 'is_correct' => true,  'order' => 2]);
            Choice::create(['question_id' => $q->id, 'text' => 'Option C', 'is_correct' => false, 'order' => 3]);
            Choice::create(['question_id' => $q->id, 'text' => 'Option D', 'is_correct' => false, 'order' => 4]);
        }

        // True/False x2
        for ($i = 1; $i <= 2; $i++) {
            $q = $makeQuestion([
                'type' => 'true_false',
                'text' => "True/False Question {$i}",
                'negative_score' => 0,
            ]);
            Choice::create(['question_id' => $q->id, 'text' => 'True', 'is_correct' => ($i % 2 === 1), 'order' => 1]);
            Choice::create(['question_id' => $q->id, 'text' => 'False',  'is_correct' => ($i % 2 === 0), 'order' => 2]);
        }

        // Short answer x2 (use explanation as answer key for demo)
        for ($i = 1; $i <= 2; $i++) {
            $makeQuestion([
                'type' => 'short_answer',
                'text' => "Short Answer Question {$i} (Key: keyword{$i})",
                'explanation' => "keyword{$i}",
                'negative_score' => 0,
            ]);
        }

        // 6) Resource PDFs (external URLs for demo)
        $pdfs = [
            ['title' => 'Study Guide 1', 'url' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf'],
            ['title' => 'Study Guide 2', 'url' => 'https://www.adobe.com/support/products/enterprise/knowledgecenter/media/c4611_sample_explain.pdf'],
        ];
        foreach ($pdfs as $p) {
            ResourceItem::create([
                'type' => 'pdf',
                'title' => $p['title'],
                'description' => 'Demo resource',
                'file_path' => $p['url'],
                'exam_domain_id' => $domain->id,
                'exam_batch_id' => $batch->id,
                'exam_id' => $exam->id,
            ]);
        }

        // 8) Demo student user
        $user = User::firstOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Demo Student',
                'password' => Hash::make('password123'),
            ]
        );
    }
}
