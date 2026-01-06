<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Choice;
use App\Models\ExamAttempt;
use App\Livewire\AttemptsPage;
use Livewire\Livewire;
use function Pest\Laravel\actingAs;

test('database cascade delete: deleting an exam removes all related data', function () {
    $exam = Exam::factory()->create();
    $question = Question::factory()->create(['exam_id' => $exam->id]);
    $choice = Choice::factory()->create(['question_id' => $question->id]);
    $user = User::factory()->create();
    $attempt = ExamAttempt::create([
        'exam_id' => $exam->id,
        'user_id' => $user->id,
        'started_at' => now(),
        'status' => 'in_progress'
    ]);

    // Verify existence first
    $this->assertDatabaseHas('exams', ['id' => $exam->id]);
    $this->assertDatabaseHas('questions', ['id' => $question->id]);
    $this->assertDatabaseHas('choices', ['id' => $choice->id]);
    $this->assertDatabaseHas('exam_attempts', ['id' => $attempt->id]);

    // Delete Exam
    $exam->delete();

    // Verify cascading deletion
    $this->assertDatabaseMissing('exams', ['id' => $exam->id]);
    $this->assertDatabaseMissing('questions', ['id' => $question->id]);
    $this->assertDatabaseMissing('choices', ['id' => $choice->id]);
    $this->assertDatabaseMissing('exam_attempts', ['id' => $attempt->id]);
});

test('user isolation: My Attempts page only shows current user attempts', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $exam = Exam::factory()->create();

    // Attempt for User A
    $attemptA = ExamAttempt::create([
        'user_id' => $userA->id,
        'exam_id' => $exam->id,
        'started_at' => now(),
        'status' => 'submitted'
    ]);

    // Attempt for User B
    $attemptB = ExamAttempt::create([
        'user_id' => $userB->id,
        'exam_id' => $exam->id,
        'started_at' => now(),
        'status' => 'submitted'
    ]);

    actingAs($userA);

    Livewire::test(AttemptsPage::class)
        ->assertViewHas('attempts', function ($attempts) use ($attemptA, $attemptB) {
            return $attempts->contains($attemptA) && !$attempts->contains($attemptB);
        });
});
