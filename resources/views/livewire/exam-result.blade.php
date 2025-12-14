<div class="mx-auto max-w-4xl p-6 space-y-6">
    <h1 class="text-xl font-medium text-gray-900">نتیجه آزمون: {{ $exam->title }}</h1>

    @php
        $ok = $stats && is_array($stats);
    @endphp
    
    {{-- Debug: Show if stats exist --}}
    @if(config('app.debug') && !$ok)
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
            <strong>Debug:</strong> No stats found in session. 
            Session keys: {{ implode(', ', array_keys(session()->all())) }}
        </div>
    @endif

    @if($ok)
        @php
            $passed = (bool)($stats['passed'] ?? false);
        @endphp
        <div class="rounded-xl shadow-lg p-6 bg-gradient-to-r from-indigo-50 via-sky-50 to-purple-50 border border-indigo-100">
            <div class="flex items-center gap-3">
                @if($passed)
                    @svg('heroicon-o-check-circle', 'w-10 h-10 text-green-600')
                    <div>
                        <div class="text-lg font-semibold text-green-700">قبول</div>
                        <div class="text-sm text-gray-700">نمره: {{ number_format($stats['percentage'] ?? 0, 1) }}% - حد نصاب قبولی در این آزمون {{ number_format($passThreshold ?? 50, 0) }}% بوده است.</div>
                    </div>
                @else
                    @svg('heroicon-o-x-circle', 'w-10 h-10 text-rose-600')
                    <div>
                        <div class="text-lg font-semibold text-rose-700">رد</div>
                        <div class="text-sm text-gray-700">نمره: {{ number_format($stats['percentage'] ?? 0, 1) }}% - حد نصاب قبولی در این آزمون {{ number_format($passThreshold ?? 50, 0) }}% بوده است.</div>
                    </div>
                @endif
            </div>

            <div class="mt-5 grid grid-cols-3 gap-3 text-center">
                <div class="rounded-lg p-4 bg-gradient-to-br from-green-50 to-emerald-50 border border-green-100">
                    <div class="text-xs text-green-700">پاسخ صحیح</div>
                    <div class="mt-1 text-2xl font-bold text-green-700">{{ $stats['correct'] ?? 0 }}</div>
                </div>
                <div class="rounded-lg p-4 bg-gradient-to-br from-rose-50 to-pink-50 border border-rose-100">
                    <div class="text-xs text-rose-700">پاسخ غلط</div>
                    <div class="mt-1 text-2xl font-bold text-rose-700">{{ $stats['wrong'] ?? 0 }}</div>
                </div>
                <div class="rounded-lg p-4 bg-gradient-to-br from-amber-50 to-yellow-50 border border-amber-100">
                    <div class="text-xs text-amber-700">بی‌پاسخ</div>
                    <div class="mt-1 text-2xl font-bold text-amber-700">{{ $stats['unanswered'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <!-- Questions Review -->
        <div class="space-y-4">
            <h2 class="text-lg font-medium text-gray-900">بررسی سوالات (فقط سوالات پاسخ‌داده‌شده)</h2>
            @foreach($review as $item)
                <div class="rounded-xl shadow p-6 bg-gradient-to-r from-white to-slate-50 {{ !empty($item['is_correct']) ? 'border-2 border-green-200' : 'border-2 border-rose-200' }}">
                    <!-- Question Number and Status -->
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900">سوال {{ $item['question_number'] }}</h3>
                        @if(!empty($item['is_deleted']))
                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 text-sm font-semibold px-3 py-1">
                                @svg('heroicon-o-exclamation-triangle', 'w-4 h-4')
                                حذف شده
                            </span>
                        @elseif(!empty($item['is_correct']))
                            <span class="inline-flex items-center gap-1 rounded-full bg-green-100 text-green-800 text-sm font-semibold px-3 py-1">
                                @svg('heroicon-o-check', 'w-4 h-4')
                                صحیح
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-red-100 text-red-800 text-sm font-semibold px-3 py-1">
                                @svg('heroicon-o-x-mark', 'w-4 h-4')
                                غلط
                            </span>
                        @endif
                    </div>

                    <!-- Question Text -->
                    <div class="prose prose-sm max-w-none mb-4">
                        {!! $item['text_html'] ?? '' !!}
                    </div>

                    @if(!empty($item['is_deleted']))
                        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 mb-4">
                            <p class="text-sm text-amber-700">
                                <strong>توجه:</strong> این سوال بطور رسمی حذف شده و در محاسبه نمره نهایی شما لحاظ نشده است.
                            </p>
                        </div>
                    @endif

                    <!-- Show user's choice first (with option number), then the correct choice (with number) -->
                    @if(!empty($item['user_choice_text']))
                        <div class="rounded-lg border p-4 bg-gradient-to-r from-sky-50 to-blue-50 border-sky-200 mb-2">
                            <div class="flex items-start gap-3">
                                @if(!empty($item['is_correct']))
                                    @svg('heroicon-o-check-circle', 'w-6 h-6 text-green-600 flex-shrink-0 mt-1')
                                @else
                                    @svg('heroicon-o-x-circle', 'w-6 h-6 text-rose-600 flex-shrink-0 mt-1')
                                @endif
                                <div class="flex-1">
                                    <span class="font-bold text-gray-900">انتخاب شما: گزینه {{ $item['user_no'] }}</span>
                                    <div class="mt-1 prose prose-sm max-w-none text-gray-800">
                                        {!! $item['user_choice_text'] !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(!empty($item['correct_choice_text']))
                        <div class="rounded-lg border-2 p-4 bg-green-50 border-green-200">
                            <div class="flex items-start gap-3">
                                @svg('heroicon-o-check-circle', 'w-6 h-6 text-green-600 flex-shrink-0 mt-1')
                                <div class="flex-1">
                                    <span class="font-bold text-gray-900">گزینه صحیح: گزینه {{ $item['correct_no'] }}</span>
                                    <div class="mt-1 prose prose-sm max-w-none text-gray-800">
                                        {!! $item['correct_choice_text'] !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="rounded border p-4 text-sm text-gray-600">نتیجه‌ای برای نمایش یافت نشد.</div>
    @endif

    <div class="flex justify-center">
        <a href="{{ route('home') }}" wire:navigate class="inline-flex items-center rounded bg-indigo-600 px-4 py-2 text-white text-sm font-medium hover:bg-indigo-700 transition">بازگشت به خانه</a>
    </div>
</div>
