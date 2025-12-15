<div class="min-h-screen bg-gray-50 py-4 px-4 sm:px-6"
     oncontextmenu="return false;" 
     onselectstart="return false;" 
     oncopy="return false;"
     oncut="return false;"
     style="user-select: none; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none;">
    <div class="max-w-3xl mx-auto">
        
        <!-- Header Section -->
        <div class="mb-6">
            <!-- Row 1: Beautiful Exam Title -->
            <div class="text-center mb-4 pb-3 border-b border-gray-200/60">
                <h1 class="text-lg sm:text-xl font-bold text-indigo-900 leading-tight">
                    {{ $exam->title }}
                </h1>
            </div>

            <!-- Row 2: Navigation Bar -->
            <div class="flex items-center justify-between">
                <a href="{{ route('exams', ['batch' => $exam->exam_batch_id]) }}" class="group flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-gray-500 hover:text-gray-800 hover:bg-gray-100 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 transition-transform group-hover:-translate-x-0.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                    <span class="text-sm font-bold">Back</span>
                </a>
                
                <div class="px-3 py-1 bg-white rounded-full shadow-sm border border-gray-100 text-xs font-bold text-gray-700 flex items-center gap-1.5">
                    <span class="text-gray-400">Question</span>
                    <span class="text-indigo-600 text-base">{{ $page }}</span>
                    <span class="text-gray-300">/</span>
                    <span>{{ count($questions) }}</span>
                </div>
            </div>
        </div>

        <!-- Question Card -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-6 relative">
            <!-- Progress Bar -->
            <div class="h-1 w-full bg-gray-100">
                <div class="h-full bg-blue-500 transition-all duration-300" style="width: {{ ($page / count($questions)) * 100 }}%"></div>
            </div>

            <div class="p-5 sm:p-6">
                <!-- Question Text -->
                <div class="text-sm sm:text-base font-bold text-gray-900 mb-6 leading-loose text-left" dir="ltr">
                    {!! $this->currentQuestion->text !!}
                </div>

                <!-- Question Images -->
                @if($this->currentQuestion->image_path)
                    <div class="mb-6">
                        <img src="{{ Storage::url($this->currentQuestion->image_path) }}" class="rounded-lg max-h-64 mx-auto object-contain border border-gray-100 shadow-sm" alt="Question Image">
                    </div>
                @endif
                @if($this->currentQuestion->image_path_2)
                    <div class="mb-6">
                        <img src="{{ Storage::url($this->currentQuestion->image_path_2) }}" class="rounded-lg max-h-64 mx-auto object-contain border border-gray-100 shadow-sm" alt="Second Question Image">
                    </div>
                @endif

                <!-- Options -->
                <div class="space-y-2.5">
                    @if($this->canUserInteract())
                        @foreach($this->currentQuestion->choices as $choice)
                            @php
                                $isSelected = $selectedOption == $choice->id;
                                $isCorrect = $choice->is_correct;
                                
                                // Determine classes based on state
                                $baseClasses = "w-full text-left p-3.5 rounded-xl border transition-all duration-200 flex items-center gap-3 relative overflow-hidden";
                                
                                if ($isAnswerRevealed) {
                                    if ($isCorrect) {
                                        $colorClasses = "border-green-500 bg-green-50 text-green-900";
                                    } elseif ($isSelected) {
                                        $colorClasses = "border-red-300 bg-red-50 text-red-900"; // Wrong selection
                                    } else {
                                        $colorClasses = "border-gray-100 bg-white text-gray-500 opacity-60";
                                    }
                                } else {
                                    if ($isSelected) {
                                        $colorClasses = "border-blue-500 bg-blue-50 text-blue-900 shadow-sm ring-1 ring-blue-500";
                                    } else {
                                        $colorClasses = "border-gray-200 bg-white text-gray-700 hover:border-blue-300 hover:bg-blue-50/50 cursor-pointer";
                                    }
                                }
                            @endphp

                            <div 
                                wire:click="selectOption({{ $choice->id }})"
                                class="{{ $baseClasses }} {{ $colorClasses }}"
                            >
                                <!-- Indicator Circle -->
                                <div class="shrink-0 w-5 h-5 rounded-full border flex items-center justify-center transition-colors duration-200
                                    {{ $isAnswerRevealed && $isCorrect ? 'border-green-500 bg-green-500 text-white' : ($isSelected ? 'border-blue-500 bg-blue-500 text-white' : 'border-gray-300 text-transparent') }}">
                                    @if($isAnswerRevealed && $isCorrect)
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5">
                                            <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" />
                                        </svg>
                                    @elseif($isSelected)
                                        {{-- Always show checkmark for selection, even if wrong (until revealed) --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5">
                                            <path fill-rule="evenodd" d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        {{-- Empty space for alignment --}}
                                    @endif
                                </div>

                                <!-- Option Text -->
                                <span class="font-medium text-sm leading-relaxed">
                                    {{ $choice->text }}
                                </span>
                            </div>
                        @endforeach
                    @else
                        <div class="rounded-lg border-2 border-dashed border-gray-300 p-8 text-center bg-gray-50">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Options are hidden</h3>
                            <p class="mt-1 text-sm text-gray-500">Please login with email and password to view options and answer the question.</p>
                            <div class="mt-6">
                                @guest
                                    <a href="{{ route('login') }}" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        Login / Register
                                    </a>
                                @else
                                    <a href="{{ route('profile') }}" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        View Profile
                                    </a>
                                @endguest
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Bottom Actions Bar -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col-reverse sm:flex-row items-center justify-between gap-4">
                <!-- Navigation -->
                <div class="flex items-center gap-3 w-full sm:w-auto justify-center">
                    <button 
                        wire:click="prevQuestion" 
                        @if($page === 1) disabled @endif
                        class="px-4 py-2 rounded-lg text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-bold flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                        قبلی
                    </button>

                    <button 
                        wire:click="nextQuestion" 
                        @if($page === count($questions)) disabled @endif
                        class="px-4 py-2 rounded-lg text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed text-sm font-bold flex items-center gap-2"
                    >
                        بعدی
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                        </svg>
                    </button>
                </div>

                <!-- Reveal Button -->
                @if($this->canUserInteract())
                    <button 
                        wire:click="toggleAnswer" 
                        class="w-full sm:w-auto px-6 py-2.5 rounded-xl font-bold text-white shadow-md transition-transform active:scale-95 flex items-center justify-center gap-2
                        {{ $isAnswerRevealed ? 'bg-gray-600 hover:bg-gray-700' : 'bg-indigo-600 hover:bg-indigo-700' }}"
                    >
                        @if($isAnswerRevealed)
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                            Hide Answer
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            Show Correct Answer
                        @endif
                    </button>
                @endif
            </div>
        </div>

        <!-- Explanation Section -->
        @if($isAnswerRevealed)
            <div 
                class="bg-green-50 border border-green-200 rounded-2xl p-6 shadow-sm animate-fade-in-up"
                x-data
                x-init="$el.scrollIntoView({behavior: 'smooth', block: 'center'})"
            >
                <h4 class="text-lg font-bold text-green-800 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                    </svg>
                    پاسخ تشریحی
                </h4>

                @if($this->currentQuestion->explanation)
                    <div class="prose prose-green max-w-none text-gray-700 leading-loose text-justify mb-4" dir="rtl">
                        {!! $this->currentQuestion->explanation !!}
                    </div>
                @endif

                @if($this->currentQuestion->explanation_image_path)
                    <div class="mt-4">
                        <img 
                            src="{{ Storage::url($this->currentQuestion->explanation_image_path) }}" 
                            class="rounded-xl max-w-full h-auto shadow-md border border-green-100" 
                            alt="تصویر پاسخ تشریحی"
                        >
                    </div>
                @endif

                @if(!$this->currentQuestion->explanation && !$this->currentQuestion->explanation_image_path)
                    <p class="text-gray-500 text-sm italic">توضیحات تشریحی برای این سوال ثبت نشده است.</p>
                @endif
            </div>
        @endif

    </div>
</div>

<style>
    .animate-fade-in-up {
        animation: fadeInUp 0.5s ease-out;
    }
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
