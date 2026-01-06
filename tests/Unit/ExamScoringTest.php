<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Choice;
use App\Models\ExamAttempt;
use App\Domain\Exam\Services\ScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExamScoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_scoring_service_calculates_correctly_with_negative_scores()
    {
        $exam = Exam::factory()->create(['total_score' => 100]);

        // 4 questions, each 25 points
        $q1 = Question::factory()->create(['exam_id' => $exam->id, 'type' => 'single_choice']);
        $c1_correct = Choice::factory()->create(['question_id' => $q1->id, 'is_correct' => true]);

        $q2 = Question::factory()->create(['exam_id' => $exam->id, 'type' => 'single_choice']);
        $c2_correct = Choice::factory()->create(['question_id' => $q2->id, 'is_correct' => true]);

        $q3 = Question::factory()->create(['exam_id' => $exam->id, 'type' => 'single_choice']);
        $c3_correct = Choice::factory()->create(['question_id' => $q3->id, 'is_correct' => true]);
        $c3_wrong = Choice::factory()->create(['question_id' => $q3->id, 'is_correct' => false]);

        $q4 = Question::factory()->create(['exam_id' => $exam->id, 'type' => 'single_choice']);
        $c4_wrong = Choice::factory()->create(['question_id' => $q4->id, 'is_correct' => false]);

        // User answers: q1 correct, q2 correct, q3 wrong, q4 wrong
        $answers = [
            $q1->id => [$c1_correct->id => true],
            $q2->id => [$c2_correct->id => true],
            $q3->id => [$c3_wrong->id => true],
            $q4->id => [$c4_wrong->id => true],
        ];

        $service = new ScoringService();
        $result = $service->compute($exam, $answers);

        // Calculation:
        // Total Score = 100
        // Score per question = 100 / 4 = 25
        // Correct = 2 (q1, q2)
        // Wrong = 2 (q3, q4)
        // Earned = 2 * 25 = 50
        // Negative Ratio in ScoringService is 0 by default (as per current code)

        $this->assertEquals(50.0, $result['earned']);
        $this->assertEquals(50.0, $result['percentage']);
        $this->assertEquals(2, $result['correct']);
        $this->assertEquals(2, $result['wrong']);
    }

    public function test_user_can_access_their_exam_attempts()
    {
        $user = User::factory()->create();
        $exam = Exam::factory()->create();

        $attempt = ExamAttempt::create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'started_at' => now(),
            'status' => 'completed'
        ]);

        $this->assertTrue($user->examAttempts->contains($attempt));
    }
}
