<?php

namespace App\Http\Controllers;

use App\Domain\Exam\Services\ScoringService;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\AttemptAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ExamController extends Controller
{
    public function finish(Request $request, Exam $exam, ScoringService $scoring)
    {
        // $this->authorize('view', $exam); // TODO: Create ExamPolicy if authorization needed

        if (!Auth::check()) {
            return redirect()->route('exam.landing', ['exam' => $exam->id])
                ->with('error', 'برای اتمام آزمون باید وارد شوید.');
        }

        // Find the current in_progress attempt (created by ExamPlayer mount)
        $attempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('user_id', Auth::id())
            ->where('status', 'in_progress')
            ->latest('id')
            ->first();

        // If no in_progress attempt found, create one (fallback)
        if (!$attempt) {
            $attempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'user_id' => Auth::id(),
                'started_at' => now(),
                'status' => 'in_progress',
            ]);
        }

        // Answers can come from request (preferred) or session fallback
        $answers = $request->input('answers');
        // If provided as JSON string, decode it
        if (is_string($answers) && $answers !== '') {
            $decoded = json_decode($answers, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $answers = $decoded;
            }
        }
        if (!is_array($answers)) {
            $answers = Session::get('exam_answers_' . $exam->id, []);
        }

        // Optionally hydrate answers table for any missing rows
        foreach ($answers as $qId => $choices) {
            if (is_array($choices)) {
                foreach ($choices as $cId => $selected) {
                    AttemptAnswer::updateOrCreate(
                        [
                            'exam_attempt_id' => $attempt->id,
                            'question_id' => (int) $qId,
                            'choice_id' => (int) $cId,
                        ],
                        [
                            'selected' => (bool) $selected,
                        ]
                    );
                }
            }
        }

        $exam->load(['questions.choices']);
        $scores = $scoring->compute($exam, $answers);
        $percentage = $scores['percentage'] ?? 0.0;
        $passThreshold = property_exists($exam, 'pass_threshold') ? ((float) ($exam->pass_threshold ?? 0)) : 0.0;

        $attempt->update([
            'submitted_at' => now(),
            'score' => $percentage,
            'passed' => $percentage >= $passThreshold,
            'status' => 'submitted',
        ]);

        // Clear session answers after submit
        Session::forget('exam_answers_' . $exam->id);

        return redirect()->route('exam.result', ['exam' => $exam->id, 'attempt' => $attempt->id])
            ->with('status', 'نتیجه آزمون ثبت شد.');
    }
}
