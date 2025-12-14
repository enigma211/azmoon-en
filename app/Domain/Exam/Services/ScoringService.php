<?php

namespace App\Domain\Exam\Services;

use App\Models\Exam;
use App\Models\Question;

class ScoringService
{
    /**
     * Compute earned, total and percentage based on provided answers.
     * Uses exam-level scoring configuration for automatic calculation.
     * Answers format:
     *  - For choice-based: answers[questionId] = [choiceId => true/false]
     *  - For short answer: answers[questionId] = ['text' => '...']
     */
    public function compute(Exam $exam, array $answers): array
    {
        // Filter out deleted questions from scoring
        $activeQuestions = $exam->questions->where('is_deleted', false);
        $totalQuestions = $activeQuestions->count();
        
        if ($totalQuestions === 0) {
            return ['earned' => 0.0, 'total' => 0.0, 'percentage' => 0.0, 'correct' => 0, 'wrong' => 0, 'unanswered' => 0];
        }

        // Use exam-level scoring configuration
        $totalScore = (float) ($exam->total_score ?? 100);
        $scorePerQuestion = $totalScore / $totalQuestions;
        $negativeRatio = (int) ($exam->negative_score_ratio ?? 3); // e.g., 3 wrong = -1 correct

        $correct = 0;
        $wrong = 0;
        $unanswered = 0;

        // Questions are now directly linked to exam, not through sections
        // Only process active (non-deleted) questions
        foreach ($activeQuestions as $q) {
            $ans = $answers[$q->id] ?? null;
            $isAnswered = false;
            $isCorrect = false;

            switch ($q->type) {
                case 'single_choice':
                case 'true_false':
                    $selectedIds = collect($ans ?? [])->filter()->keys();
                    $isAnswered = $selectedIds->count() > 0;
                    if ($isAnswered) {
                        $correctIds = $q->choices->where('is_correct', true)->pluck('id');
                        $isCorrect = $selectedIds->count() === 1 && $selectedIds->diff($correctIds)->isEmpty() && $correctIds->diff($selectedIds)->isEmpty();
                    }
                    break;
                case 'multi_choice':
                    $selectedIds = collect($ans ?? [])->filter()->keys();
                    $isAnswered = $selectedIds->count() > 0;
                    if ($isAnswered) {
                        $correctIds = $q->choices->where('is_correct', true)->pluck('id');
                        $isCorrect = $selectedIds->diff($correctIds)->isEmpty() && $correctIds->diff($selectedIds)->isEmpty();
                    }
                    break;
                case 'short_answer':
                    $text = is_array($ans) ? ($ans['text'] ?? '') : (string) $ans;
                    $text = trim(mb_strtolower($text));
                    $isAnswered = $text !== '';
                    if ($isAnswered) {
                        $keys = collect([$q->explanation])->filter()->map(fn($s) => trim(mb_strtolower((string)$s)))->filter();
                        $isCorrect = $keys->contains($text);
                    }
                    break;
                default:
                    $isAnswered = false;
                    $isCorrect = false;
            }

            if (!$isAnswered) {
                $unanswered++;
            } elseif ($isCorrect) {
                $correct++;
            } else {
                $wrong++;
            }
        }

        // Calculate earned score
        $earned = $correct * $scorePerQuestion;
        
        // Apply negative scoring: every N wrong answers reduces score by one correct answer's worth
        if ($negativeRatio > 0 && $wrong > 0) {
            $penalty = ($wrong / $negativeRatio) * $scorePerQuestion;
            $earned -= $penalty;
        }

        $earned = max(0.0, $earned); // Don't go below zero
        $percentage = $totalScore > 0 ? round(($earned / $totalScore) * 100, 2) : 0.0;

        return [
            'earned' => round($earned, 2),
            'total' => $totalScore,
            'percentage' => $percentage,
            'correct' => $correct,
            'wrong' => $wrong,
            'unanswered' => $unanswered,
        ];
    }
}
