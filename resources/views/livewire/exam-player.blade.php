@php
    // Defensive checks to avoid errors before data is fully wired
    $q = $question ?? null;
    // Check if user can interact (logged in)
    $canInteract = true;
@endphp
<div class="mx-auto max-w-2xl p-4 space-y-4" 
     oncontextmenu="return false;" 
     onselectstart="return false;" 
     oncopy="return false;"
     oncut="return false;"
     style="user-select: none; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none;">
    <!-- Exam Title -->
    <div class="text-center mb-2">
        <h1 class="text-sm font-bold text-gray-800">{{ $this->exam->title }}</h1>
    </div>
    
    <!-- Assumptions Button -->
    @if($this->exam->assumptions_text || $this->exam->assumptions_image)
        <div class="flex justify-center mb-3">
            <button 
                @click="$dispatch('open-modal', 'assumptions-modal')"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-100 px-4 py-2 text-amber-800 text-sm font-medium hover:bg-amber-200 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                View Exam Assumptions
            </button>
        </div>
    @endif
    
    <div class="flex flex-col sm:flex-row items-center justify-between mb-4 bg-white p-3 rounded-lg shadow-sm gap-3 sm:gap-0">
        <div class="flex flex-wrap justify-center sm:justify-start gap-4 text-sm font-medium w-full sm:w-auto">
            <div class="text-green-600 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Correct: {{ $this->stats['correct'] }}</span>
            </div>
            <div class="text-red-600 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                <span>Wrong: {{ $this->stats['wrong'] }}</span>
            </div>
            <div class="text-gray-500 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Skipped: {{ $this->stats['skipped'] }}</span>
            </div>
        </div>
        <div class="text-xs text-gray-700">
            @if(!is_null($durationSeconds))
                <span wire:poll.1s="tick">
                    Time: {{ floor($remainingSeconds / 60) }}:{{ sprintf('%02d', $remainingSeconds % 60) }}
                </span>
            @else
                <span>No Time Limit</span>
            @endif
        </div>
    </div>
    
    <!-- Progress bar -->
    <div class="mb-4">
        @php $pct = ($total ?? 0) > 0 ? intval((($index ?? 0)+1) / ($total ?? 1) * 100) : 0; @endphp
        <div class="h-2 w-full rounded bg-gray-200">
            <div class="h-2 rounded bg-indigo-600" style="width: {{ $pct }}%"></div>
        </div>
        <div class="mt-1 flex justify-between text-[11px] text-gray-500">
            <span>Progress: {{ $pct }}%</span>
            <span>Question {{ ($index ?? 0) + 1 }} of {{ $total ?? 0 }}</span>
        </div>
    </div>

    @if($q)
        <div wire:key="question-{{ $q->id }}">
        <article class="prose prose-lg max-w-none leading-loose">
            {!! $q->text !!}
        </article>

        @if(!empty($q->image_path))
            <div class="mt-3 flex justify-center">
                <img src="{{ Storage::url($q->image_path) }}" alt="image" class="max-w-full h-auto rounded" style="width: auto;" />
            </div>
        @endif

        @if(!empty($q->image_path_2))
            <div class="mt-3 flex justify-center">
                <img src="{{ Storage::url($q->image_path_2) }}" alt="image" class="max-w-full h-auto rounded" style="width: auto;" />
            </div>
        @endif

        @php
            $isChecked = $checkedQuestions[$q->id] ?? false;
            $userCorrect = $isChecked ? $this->isQuestionCorrect($q->id) : false;
        @endphp

        @if($q->is_deleted)
            <div class="mt-6 rounded-lg border-2 border-amber-400 bg-amber-50 p-6 text-center">
                <div class="flex items-center justify-center gap-2 text-amber-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span class="text-lg font-bold">This question has been officially removed</span>
                </div>
                <p class="mt-2 text-sm text-amber-600">This question will not be calculated in your final score. Please proceed to the next question.</p>
            </div>
        @elseif($q->choices && $q->choices->count())
            <div class="mt-3 space-y-2">
                @if($canInteract)
                    @foreach($q->choices as $choice)
                        @php 
                            $isSelected = ($answers[$q->id][$choice->id] ?? false) === true;
                            $isCorrectChoice = (bool)$choice->is_correct;
                            $isMulti = $q->type === 'multi_choice';
                            $inputType = $isMulti ? 'checkbox' : 'radio';
                            
                            // Determine style class
                            $class = 'border-gray-200 hover:bg-gray-50';
                            if ($isChecked) {
                                if ($isCorrectChoice) {
                                    $class = 'bg-green-50 border-green-500 ring-1 ring-green-500';
                                } elseif ($isSelected && !$isCorrectChoice) {
                                    $class = 'bg-red-50 border-red-500';
                                } else {
                                    $class = 'border-gray-200 opacity-75';
                                }
                            } elseif ($isSelected) {
                                $class = 'bg-indigo-50 border-indigo-400';
                            }
                        @endphp
                        <label wire:key="choice-{{ $q->id }}-{{ $choice->id }}" class="relative flex items-start gap-3 rounded-lg border-2 p-4 cursor-pointer transition {{ $class }}">
                            @php $inputId = 'q'.$q->id.'_c'.$choice->id; @endphp
                            <input type="{{ $inputType }}"
                                   id="{{ $inputId }}"
                                   name="question_{{ $q->id }}{{ $isMulti ? '[]' : '' }}"
                                   value="{{ $choice->id }}"
                                   class="h-5 w-5 mt-0.5 border-gray-300 text-indigo-600 focus:ring-indigo-500 disabled:cursor-not-allowed"
                                   @checked($isSelected)
                                   @disabled($isChecked)
                                   @if($isMulti)
                                       wire:change="saveAnswer({{ $q->id }}, {{ $choice->id }}, $event.target.checked)"
                                   @else
                                       wire:click="saveAnswer({{ $q->id }}, {{ $choice->id }}, true)"
                                   @endif
                                   />
                            <div class="flex-1 text-sm leading-relaxed choice-text" dir="ltr">
                                {!! $choice->text !!}
                            </div>
                            
                            @if($isChecked)
                                @if($isCorrectChoice)
                                    <svg class="w-6 h-6 text-green-500 absolute right-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                @elseif($isSelected && !$isCorrectChoice)
                                    <svg class="w-6 h-6 text-red-500 absolute right-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                @endif
                            @endif
                        </label>
                    @endforeach
                @else
                    <div class="rounded-lg border-2 border-dashed border-gray-300 p-8 text-center bg-gray-50">
                        <!-- Guest view ... -->
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Choices are hidden</h3>
                        <p class="mt-1 text-sm text-gray-500">To view options and answer the question, please login.</p>
                        <div class="mt-6">
                            <a href="{{ route('login') }}" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">Login</a>
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Immediate Feedback / Check Answer -->
            @if($canInteract && !$q->is_deleted)
                <div class="mt-4">
                    @if(!$isChecked)
                        <button wire:click="checkAnswer" 
                                class="w-full sm:w-auto rounded-lg bg-blue-600 px-6 py-2 text-white font-bold hover:bg-blue-700 transition shadow-md">
                            Check Answer
                        </button>
                    @else
                        <!-- Explanation -->
                        <div class="rounded-lg p-4 {{ $userCorrect ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                            <div class="flex items-center gap-2 mb-2">
                                @if($userCorrect)
                                    <span class="text-green-700 font-bold flex items-center gap-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Correct!
                                    </span>
                                @else
                                    <span class="text-red-700 font-bold flex items-center gap-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Incorrect
                                    </span>
                                @endif
                            </div>
                            
                            @if($q->explanation || $q->explanation_image_path)
                                <div class="prose prose-sm max-w-none text-gray-800 mt-2">
                                    <strong class="block text-gray-900 mb-1">Explanation:</strong>
                                    @if($q->explanation)
                                        {!! $q->explanation !!}
                                    @endif
                                    
                                    @if($q->explanation_image_path)
                                        <div class="mt-3">
                                            <img src="{{ Storage::url($q->explanation_image_path) }}" alt="Explanation" class="max-w-full h-auto rounded border border-gray-200" />
                                        </div>
                                    @endif
                                </div>
                            @else
                                <p class="text-sm text-gray-500">No explanation provided for this question.</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        @endif

        <div class="mt-8 space-y-4">
            <!-- Navigation buttons -->
            <div class="flex items-center justify-between border-t border-gray-100 pt-6">
                <button wire:click="prev" 
                        class="flex items-center gap-2 rounded-lg bg-gray-100 px-6 py-2.5 text-gray-700 disabled:opacity-50 hover:bg-gray-200 transition font-medium" 
                        @disabled(($index ?? 0) === 0)>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Previous
                </button>
                
                <div class="flex gap-3">
                    @if((($index ?? 0) + 1) < ($total ?? 0))
                        <button wire:click="next" 
                                class="flex items-center gap-2 rounded-lg bg-white border border-gray-300 px-6 py-2.5 text-gray-700 hover:bg-gray-50 transition font-medium shadow-sm">
                            Skip
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                        </button>

                        <button wire:click="next" 
                                class="flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-2.5 text-white hover:bg-indigo-700 transition font-medium shadow-md">
                            Next
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    @else
                        <button wire:click="submit" 
                                wire:confirm="Are you sure you want to submit your exam?"
                                class="flex items-center gap-2 rounded-lg bg-green-600 px-6 py-2.5 text-white hover:bg-green-700 transition font-bold shadow-md">
                            Finish & See Result
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </button>
                    @endif
                </div>
            </div>
            
            <div class="flex justify-center mt-4 border-t border-gray-100 pt-4">
                <livewire:question-vote :question="$q" :key="'vote-'.$q->id" />
            </div>

    @auth
        <!-- Report Issue Button -->
        <div class="flex justify-center pt-2">
            <button wire:click="$set('showReportModal', true)" 
                    class="text-sm text-gray-600 hover:text-red-600 underline transition">
                Report Issue
            </button>
        </div>
    @endauth
        </div>
    </div>
    @else
        <div class="rounded border p-4 text-sm text-gray-600">No questions to display.</div>
    @endif

    <!-- Report Issue Modal -->
    @auth
@if($showReportModal)
<div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showReportModal') }" x-show="show" x-cloak>
    <div class="flex min-h-screen items-center justify-center p-4">
        <!-- Backdrop -->
        <div x-show="show" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="$wire.set('showReportModal', false)"
             class="fixed inset-0 bg-black bg-opacity-50"></div>
        
        <!-- Modal Content -->
        <div x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
            
            <!-- Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Report Question Issue</h3>
                <button @click="$wire.set('showReportModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-800 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif
            
            <!-- Form -->
            <form wire:submit.prevent="submitReport">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Issue Description:</label>
                    <textarea wire:model="reportText" 
                              rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Please describe the issue in detail..."></textarea>
                    @error('reportText')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Actions -->
                <div class="flex gap-3">
                    <button type="submit" 
                            class="flex-1 rounded-lg bg-red-600 px-4 py-2 text-white font-medium hover:bg-red-700 transition">
                        Submit Report
                    </button>
                    <button type="button" 
                            @click="$wire.set('showReportModal', false)"
                            class="flex-1 rounded-lg bg-gray-200 px-4 py-2 text-gray-700 font-medium hover:bg-gray-300 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
    @endif
    @endauth

    <!-- Periodic autosave flush (debounced) - only for subscribed users -->
    @auth
        <div wire:poll.2s="flushDirty" class="hidden" aria-hidden="true"></div>
    @endauth

    <!-- Assumptions Modal -->
    @if($this->exam->assumptions_text || $this->exam->assumptions_image)
        <div 
            x-data="{ show: false }"
            @open-modal.window="if ($event.detail === 'assumptions-modal') show = true"
            @close-modal.window="show = false"
            @keydown.escape.window="show = false"
            x-show="show"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;">
            
            <!-- Backdrop -->
            <div 
                class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
                @click="show = false">
            </div>
            
            <!-- Modal Content -->
            <div class="flex min-h-screen items-center justify-center p-4">
                <div 
                    @click.stop
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-90"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-90"
                    class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-y-auto">
                    
                    <!-- Header -->
                    <div class="sticky top-0 bg-gradient-to-r from-amber-500 to-orange-500 text-white px-6 py-4 rounded-t-2xl flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-xl font-bold">Exam Assumptions</h3>
                        </div>
                        <button 
                            @click="show = false"
                            class="text-white hover:bg-white/20 rounded-lg p-2 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Body -->
                    <div class="p-6 space-y-4">
                        @if($this->exam->assumptions_text)
                            <div class="prose prose-sm max-w-none bg-amber-50 rounded-lg p-4 border border-amber-200">
                                {!! $this->exam->assumptions_text !!}
                            </div>
                        @endif
                        
                        @if($this->exam->assumptions_image)
                            <div class="flex justify-center bg-gray-50 rounded-lg p-4">
                                <img 
                                    src="{{ Storage::url($this->exam->assumptions_image) }}" 
                                    alt="Assumptions Image" 
                                    class="max-w-full h-auto rounded shadow-lg">
                            </div>
                        @endif
                    </div>
                    
                    <!-- Footer -->
                    <div class="sticky bottom-0 bg-gray-50 px-6 py-4 rounded-b-2xl flex justify-end">
                        <button 
                            @click="show = false"
                            class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    // Prevent keyboard shortcuts for copy, cut, and developer tools
    document.addEventListener('keydown', function(e) {
        // Prevent Ctrl+C (Copy)
        if (e.ctrlKey && e.key === 'c') {
            e.preventDefault();
            return false;
        }
        
        // Prevent Ctrl+X (Cut)
        if (e.ctrlKey && e.key === 'x') {
            e.preventDefault();
            return false;
        }
        
        // Prevent Ctrl+A (Select All)
        if (e.ctrlKey && e.key === 'a') {
            e.preventDefault();
            return false;
        }
        
        // Prevent Ctrl+U (View Source)
        if (e.ctrlKey && e.key === 'u') {
            e.preventDefault();
            return false;
        }
        
        // Prevent Ctrl+S (Save)
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            return false;
        }
        
        // Prevent F12 (Developer Tools)
        if (e.key === 'F12') {
            e.preventDefault();
            return false;
        }
        
        // Prevent Ctrl+Shift+I (Developer Tools)
        if (e.ctrlKey && e.shiftKey && e.key === 'I') {
            e.preventDefault();
            return false;
        }
        
        // Prevent Ctrl+Shift+J (Console)
        if (e.ctrlKey && e.shiftKey && e.key === 'J') {
            e.preventDefault();
            return false;
        }
        
        // Prevent Ctrl+Shift+C (Inspect Element)
        if (e.ctrlKey && e.shiftKey && e.key === 'C') {
            e.preventDefault();
            return false;
        }
    });
    
    // Additional protection: disable drag and drop
    document.addEventListener('dragstart', function(e) {
        e.preventDefault();
        return false;
    });
</script>
