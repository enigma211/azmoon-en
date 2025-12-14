<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(string $event, array $meta = [], ?int $examId = null, ?int $attemptId = null): void
    {
        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'exam_id' => $examId,
                'attempt_id' => $attemptId,
                'event' => $event,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'meta' => $meta,
            ]);
        } catch (\Throwable $e) {
            // Fail silently to avoid breaking user flow
        }
    }
}
