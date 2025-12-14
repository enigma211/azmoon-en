<div class="mx-auto max-w-2xl p-4 space-y-4">
    <h1 class="text-xl font-bold">{{ $exam->title }}</h1>
    @if($exam->description)
        <p class="text-sm text-gray-600">{{ $exam->description }}</p>
    @endif

    <div class="grid grid-cols-2 gap-4 text-sm">
        <div>
            <div class="text-gray-500">دامنه</div>
            <div class="font-medium">{{ optional($exam->domain)->title }}</div>
        </div>
        <div>
            <div class="text-gray-500">نوبت</div>
            <div class="font-medium">{{ optional($exam->batch)->title }}</div>
        </div>
        <div>
            <div class="text-gray-500">شروع</div>
            <div class="font-medium">{{ optional($exam->starts_at)->format('Y/m/d H:i') }}</div>
        </div>
        <div>
            <div class="text-gray-500">پایان</div>
            <div class="font-medium">{{ optional($exam->ends_at)->format('Y/m/d H:i') }}</div>
        </div>
        @if(property_exists($exam, 'pass_threshold'))
        <div>
            <div class="text-gray-500">نمره قبولی</div>
            <div class="font-medium">{{ $exam->pass_threshold }}%</div>
        </div>
        @endif
    </div>

    <div class="flex gap-3">
        <a href="{{ route('exam.play', ['exam' => $exam->id]) }}" wire:navigate class="rounded bg-indigo-600 px-4 py-2 text-white">شروع آزمون</a>
        <a href="{{ route('exams', ['batch' => optional($exam->batch)->id]) }}" wire:navigate class="rounded bg-gray-100 px-4 py-2 text-gray-700">بازگشت</a>
    </div>
</div>
