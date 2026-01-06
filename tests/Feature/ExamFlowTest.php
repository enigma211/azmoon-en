<?php

use App\Livewire\ExamLanding;
use App\Livewire\ExamPlayer;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\Choice;
use App\Models\User;
use Livewire\Livewire;
use function Pest\Laravel\actingAs;

test('guest can start exam and play', function () {
    $exam = Exam::factory()->create();

    // Guest visits landing
    $this->get(route('exam.landing', $exam))
        ->assertStatus(200);

    // Guest enters player - should NOT redirect to login anymore
    $this->get(route('exam.play', $exam))
        ->assertStatus(200);
});

test('guest full exam flow: takes exam and sees result', function () {
    // Setup Exam
    $exam = Exam::factory()->create(['total_score' => 10, 'pass_threshold' => 5]);
    $question = Question::factory()->create(['exam_id' => $exam->id, 'score' => 10, 'type' => 'single_choice']);
    $correctChoice = Choice::factory()->create(['question_id' => $question->id, 'is_correct' => true]);

    // No actingAs($user) here!

    // 1. Enter Exam Player
    $component = Livewire::test(ExamPlayer::class, ['exam' => $exam]);

    // Assert Attempt created with NULL user_id
    $attempt = ExamAttempt::where('exam_id', $exam->id)->latest('id')->first();
    expect($attempt)->not->toBeNull();
    expect($attempt->user_id)->toBeNull();
    expect($attempt->status)->toBe('in_progress');

    // 2. Select Correct Answer
    $component->call('saveAnswer', $question->id, $correctChoice->id, true);

    // 3. Submit Exam
    $component->call('submit');

    // 4. Verify Redirect
    $component->assertRedirect(route('exam.result', ['exam' => $exam->id, 'attempt' => $attempt->id]));

    // 5. Verify Database
    $attempt->refresh();
    expect($attempt->status)->toBe('submitted');
    expect($attempt->passed)->toBeTrue();
});

test('authenticated user can view landing and start exam', function () {
    $user = User::factory()->create();
    $exam = Exam::factory()->create();

    actingAs($user);

    Livewire::test(ExamLanding::class, ['exam' => $exam])
        ->assertSee($exam->title)
        ->call('startExam')
        ->assertRedirect(route('exam.play', $exam));
});

test('full exam flow: user takes exam, answers correctly, and gets result', function () {
    $user = User::factory()->create();

    // Setup Exam with 1 Question and 1 Correct Choice
    $exam = Exam::factory()->create(['total_score' => 10, 'pass_threshold' => 5]);
    $question = Question::factory()->create(['exam_id' => $exam->id, 'score' => 10, 'type' => 'single_choice']);
    $correctChoice = Choice::factory()->create(['question_id' => $question->id, 'is_correct' => true]);
    $wrongChoice = Choice::factory()->create(['question_id' => $question->id, 'is_correct' => false]);

    actingAs($user);

    // Initial check: No attempts yet
    expect(ExamAttempt::count())->toBe(0);

    // 1. Enter Exam Player
    $component = Livewire::test(ExamPlayer::class, ['exam' => $exam]);

    // Assert Attempt created
    $attempt = ExamAttempt::first();
    expect($attempt)->not->toBeNull();
    expect($attempt->status)->toBe('in_progress');
    expect($attempt->user_id)->toBe($user->id);

    // 2. Select Correct Answer
    // Method signature: saveAnswer(int $questionId, int $choiceId, bool $checked)
    $component->call('saveAnswer', $question->id, $correctChoice->id, true);

    // 3. Submit Exam
    $component->call('submit');

    // 4. Verify Redirect
    $component->assertRedirect(route('exam.result', ['exam' => $exam->id, 'attempt' => $attempt->id]));

    // 5. Verify Database Records
    $attempt->refresh();
    expect($attempt->status)->toBe('submitted');
    expect($attempt->score)->toBeGreaterThanOrEqual(100); // Should be 100% since we answered the only question correctly
    expect($attempt->passed)->toBeTrue();

    // Verify Answer was recorded
    $this->assertDatabaseHas('attempt_answers', [
        'exam_attempt_id' => $attempt->id,
        'question_id' => $question->id,
        'choice_id' => $correctChoice->id,
        'selected' => 1,
    ]);
});

test('exam attempts are cancelled when restarting', function () {
    $user = User::factory()->create();
    $exam = Exam::factory()->create();

    actingAs($user);

    // Start first time
    Livewire::test(ExamPlayer::class, ['exam' => $exam]);
    $firstAttempt = ExamAttempt::latest('id')->first();
    expect($firstAttempt->status)->toBe('in_progress');

    // Leave and Start again (Simulate re-entering the page)
    Livewire::test(ExamPlayer::class, ['exam' => $exam]);

    $firstAttempt->refresh();
    expect($firstAttempt->status)->toBe('cancelled'); // Logic from mount method

    $secondAttempt = ExamAttempt::latest('id')->first();
    expect($secondAttempt->id)->not->toBe($firstAttempt->id);
    expect($secondAttempt->status)->toBe('in_progress');
});
