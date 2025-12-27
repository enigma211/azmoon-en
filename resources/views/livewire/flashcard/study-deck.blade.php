<div class="min-h-screen bg-gray-50 flex flex-col items-center py-8 sm:py-12">
    <div class="w-full max-w-4xl px-4 sm:px-6">

        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <a href="{{ route('flashcards.index') }}"
                class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-medium transition-colors group">
                <svg class="w-5 h-5 mr-1 transform group-hover:-translate-x-1 transition-transform" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Decks
            </a>
            <div
                class="bg-white px-4 py-1.5 rounded-full shadow-sm border border-gray-100 flex items-center space-x-2 text-sm font-medium">
                <span class="text-red-500 font-bold">{{ $cardsDueCount }} Due</span>
                <span class="text-gray-300">•</span>
                <span class="text-indigo-600 font-bold">{{ $cardsNewCount }} New</span>
            </div>
        </div>

        <!-- Progress Line (Decorative) -->
        <div class="w-full h-1 bg-gray-200 rounded-full mb-12 opacity-50"></div>

        @if($sessionCompleted)
            <div class="bg-white rounded-3xl shadow-xl p-12 text-center max-w-2xl mx-auto animate-fade-in-up">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-green-50 text-green-500 mb-8">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-3xl font-extrabold text-gray-900 mb-4 tracking-tight">Session Complete!</h2>
                <p class="text-gray-500 mb-10 text-lg">You've successfully reviewed all cards scheduled for now.</p>
                <a href="{{ route('flashcards.index') }}"
                    class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-medium rounded-2xl text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg hover:shadow-xl transition-all hover:-translate-y-0.5">
                    Choose Another Deck
                </a>
            </div>
        @elseif($currentCard)
            <div class="max-w-3xl mx-auto">
                {{-- Card Container --}}
                <div
                    class="bg-white rounded-[2rem] shadow-[0_10px_40px_-15px_rgba(0,0,0,0.1)] p-8 sm:p-16 text-center min-h-[300px] flex flex-col items-center justify-center relative border border-gray-100 transition-all duration-300">

                    {{-- Label --}}
                    <span class="text-xs font-bold tracking-[0.2em] text-gray-400 uppercase mb-8 block">
                        {{ $isFlipped ? 'Answer' : 'Question' }}
                    </span>

                    {{-- Content --}}
                    <div class="prose prose-xl max-w-none text-gray-800 font-medium leading-relaxed">
                        @if(!$isFlipped)
                            {!! $currentCard->front_content !!}
                        @else
                            {!! $currentCard->back_content !!}
                        @endif
                    </div>

                    {{-- Bottom Gradient/Fade Effect (Optional decorative) --}}
                    <div
                        class="absolute bottom-0 left-0 right-0 h-12 bg-gradient-to-t from-white to-transparent pointer-events-none rounded-b-[2rem]">
                    </div>
                </div>

                <!-- Controls -->
                <div class="mt-12 flex justify-center">
                    @if(!$isFlipped)
                        <button wire:click="flip"
                            class="group inline-flex items-center gap-2 px-8 py-3 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-full font-semibold transition-all">
                            Show Answer
                            <svg class="w-4 h-4 transform group-hover:translate-y-0.5 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    @else
                        <div class="grid grid-cols-2 gap-6 w-full max-w-2xl animate-fade-in-up">
                            {{-- I Don't Know --}}
                            <button wire:click="processResult(false)"
                                class="group relative flex flex-col items-center justify-center p-6 bg-white border-2 border-red-50 rounded-2xl hover:border-red-100 hover:bg-red-50 hover:shadow-lg transition-all duration-200">
                                <span class="text-red-500 font-bold text-xl mb-1 group-hover:scale-105 transition-transform">I
                                    Don't Know</span>
                                <span
                                    class="text-xs font-medium text-red-300 uppercase tracking-wide group-hover:text-red-400">Reset
                                    to Box 1</span>
                            </button>

                            {{-- I Know It --}}
                            <button wire:click="processResult(true)"
                                class="group relative flex flex-col items-center justify-center p-6 bg-white border-2 border-green-50 rounded-2xl hover:border-green-100 hover:bg-green-50 hover:shadow-lg transition-all duration-200">
                                <span class="text-green-500 font-bold text-xl mb-1 group-hover:scale-105 transition-transform">I
                                    Know It</span>
                                <span
                                    class="text-xs font-medium text-green-300 uppercase tracking-wide group-hover:text-green-400">Move
                                    to Next Box</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <!-- Loading -->
            <div class="text-center py-32">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto mb-4"></div>
                <p class="text-gray-400 font-medium">Loading your session...</p>
            </div>
        @endif
    </div>

    <style>
        .animate-fade-in-up {
            animation: fadeInUp 0.4s cubic-bezier(0, 0, 0.2, 1);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</div>