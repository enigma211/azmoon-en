<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\User;
use App\Livewire\ExamPlayer;
use Livewire\Livewire;
use Illuminate\Support\Facades\RateLimiter;
use function Pest\Laravel\actingAs;

test('rate limiting prevents excessive saves in exam player', function () {
    $user = User::factory()->create();
    $exam = Exam::factory()->create();
    // We need at least one question to avoid errors during mount/findQuestion
    $question = \App\Models\Question::factory()->create(['exam_id' => $exam->id]);
    $choice = \App\Models\Choice::factory()->create(['question_id' => $question->id]);

    actingAs($user);

    $component = Livewire::test(ExamPlayer::class, ['exam' => $exam]);

    // The limit is 120 per minute. Let's try to hit it.
    // Actually, testing literal 120 calls is slow. 
    // We can use RateLimiter::fake() or just mock the helper.

    // Let's try to simulate 121 calls.
    for ($i = 0; $i < 120; $i++) {
        $component->call('saveAnswer', $question->id, $choice->id, true);
    }

    // The 121st call should be throttled (silently dropped in code)
    // We can verify this by checking if RateLimiter has too many attempts
    $who = 'user:' . $user->id;
    $rateKey = sprintf('saveAnswer:%d:%s', $exam->id, $who);

    expect(RateLimiter::tooManyAttempts($rateKey, 120))->toBeTrue();
});
