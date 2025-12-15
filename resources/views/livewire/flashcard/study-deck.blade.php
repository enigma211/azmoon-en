<div class="py-12 bg-gray-100 min-h-screen flex flex-col items-center">
    <div class="max-w-3xl w-full px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('flashcards.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center gap-1 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Decks
            </a>
            <div class="text-sm font-medium text-gray-500 bg-white px-3 py-1 rounded-full shadow-sm">
                <span class="text-red-500">{{ $cardsDueCount }} Due</span> • <span class="text-blue-500">{{ $cardsNewCount }} New</span>
            </div>
        </div>

        @if($sessionCompleted)
            <div class="bg-white rounded-2xl shadow-xl p-10 text-center animate-fade-in-up">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 text-green-600 mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">All Caught Up!</h2>
                <p class="text-gray-600 mb-8 text-lg">You've reviewed all due cards for this deck. Great job!</p>
                <a href="{{ route('flashcards.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg hover:shadow-xl transition-all">
                    Find Another Deck
                </a>
            </div>
        @elseif($currentCard)
            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-2 mb-6">
                @php
                    $total = $cardsDueCount + $cardsNewCount + 1; // Approximate
                    $progress = 0; // Difficult to calculate exact progress in Leitner without session state, keeping static or simple for now
                @endphp
                {{-- <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: 50%"></div> --}}
            </div>

            <!-- Flashcard Container -->
            <div class="perspective-1000 w-full relative min-h-[400px]">
                <div class="relative w-full h-full transition-all duration-500 transform-style-3d {{ $isFlipped ? 'rotate-y-180' : '' }}">
                    
                    <!-- Front -->
                    <div class="absolute inset-0 w-full bg-white rounded-2xl shadow-2xl backface-hidden flex flex-col p-8 border-2 border-transparent hover:border-indigo-100 transition-colors">
                        <div class="flex-1 flex flex-col items-center justify-center text-center">
                            <span class="text-xs font-bold tracking-wider text-gray-400 uppercase mb-4">Question</span>
                            <div class="prose prose-lg max-w-none text-gray-900">
                                {!! $currentCard->front_content !!}
                            </div>
                        </div>
                        <div class="mt-8 text-center">
                            <button wire:click="flip" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-50 text-indigo-700 rounded-full font-semibold hover:bg-indigo-100 transition-colors">
                                Show Answer
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Back -->
                    <div class="absolute inset-0 w-full bg-indigo-50 rounded-2xl shadow-2xl backface-hidden rotate-y-180 flex flex-col p-8 border-2 border-indigo-200">
                        <div class="flex-1 flex flex-col items-center justify-center text-center">
                            <span class="text-xs font-bold tracking-wider text-indigo-400 uppercase mb-4">Answer</span>
                            <div class="prose prose-lg max-w-none text-gray-900">
                                {!! $currentCard->back_content !!}
                            </div>
                        </div>
                        <div class="mt-8 grid grid-cols-2 gap-4">
                            <button wire:click="processResult(false)" class="flex flex-col items-center justify-center p-4 bg-white border border-red-200 rounded-xl hover:bg-red-50 hover:border-red-300 transition-all group">
                                <span class="text-red-600 font-bold text-lg group-hover:scale-110 transition-transform">I Don't Know</span>
                                <span class="text-xs text-red-400 mt-1">Reset to Box 1</span>
                            </button>
                            <button wire:click="processResult(true)" class="flex flex-col items-center justify-center p-4 bg-white border border-green-200 rounded-xl hover:bg-green-50 hover:border-green-300 transition-all group">
                                <span class="text-green-600 font-bold text-lg group-hover:scale-110 transition-transform">I Know It</span>
                                <span class="text-xs text-green-400 mt-1">Move to Next Box</span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
            
            <style>
                .perspective-1000 { perspective: 1000px; }
                .transform-style-3d { transform-style: preserve-3d; }
                .backface-hidden { backface-visibility: hidden; }
                .rotate-y-180 { transform: rotateY(180deg); }
                .animate-fade-in-up { animation: fadeInUp 0.5s ease-out; }
                @keyframes fadeInUp {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
            </style>
        @else
            <!-- Loading State -->
            <div class="text-center py-20">
                <svg class="animate-spin h-10 w-10 text-indigo-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-500">Loading flashcards...</p>
            </div>
        @endif

    </div>
</div>
