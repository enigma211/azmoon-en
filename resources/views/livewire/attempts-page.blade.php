<div class="mx-auto max-w-4xl p-6 space-y-6">
    <h1 class="text-xl font-medium text-gray-900">Attempt History</h1>

    <div class="flex items-center gap-3">
        <label class="text-sm text-gray-600">Status:</label>
        <select wire:model.live="status" class="rounded border-gray-300 text-sm">
            <option value="all">All</option>
            <option value="in_progress">In Progress</option>
            <option value="submitted">Completed</option>
            <option value="abandoned">Abandoned</option>
        </select>
    </div>

    @if($attempts->count())
        <div class="space-y-3">
            @foreach($attempts as $attempt)
                <div class="rounded-lg border p-4 bg-white shadow-sm">
                    <div class="flex flex-col gap-3">
                        {{-- Header: Exam title --}}
                        <div class="flex items-start justify-between">
                            <div class="min-w-0">
                                <div class="font-semibold text-gray-900 truncate">{{ $attempt->exam->title ?? '---' }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Attempt #{{ $attempt->id }}
                                </div>
                            </div>
                            @if($attempt->status === 'submitted')
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                    Finished
                                </span>
                            @elseif($attempt->status === 'in_progress')
                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-700">
                                    In Progress
                                </span>
                            @endif
                        </div>
                        
                        {{-- Stats row --}}
                        <div class="flex flex-wrap items-center gap-4 text-sm">
                            <div class="flex items-center gap-1">
                                <span class="text-gray-500">Date:</span>
                                <span>{{ $attempt->started_at ? \Carbon\Carbon::parse($attempt->started_at)->format('Y/m/d H:i') : '—' }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="text-gray-500">Answered:</span>
                                <span class="font-medium">{{ $attempt->answers_count ?? 0 }} Qs</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="text-gray-500">Score:</span>
                                <span class="font-semibold {{ ($attempt->score ?? 0) >= 50 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ is_null($attempt->score) ? '—' : number_format($attempt->score, 1).'%' }}
                                </span>
                            </div>
                            @if($attempt->status === 'submitted' && $attempt->exam)
                                <a href="{{ route('exam.result', ['exam' => $attempt->exam_id, 'attempt' => $attempt->id]) }}" 
                                   class="inline-flex items-center gap-1 rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-700 transition mr-auto">
                                    View Report
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div>
            {{ $attempts->links() }}
        </div>
    @else
        <div class="rounded border p-4 text-sm text-gray-600">No attempts recorded.</div>
    @endif
</div>
