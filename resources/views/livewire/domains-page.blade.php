<div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <section class="py-6">
            <h2 class="text-lg font-bold mb-6 text-center text-gray-600">Exam Categories</h2>

            @if(($domains ?? collect())->isEmpty())
                <div class="p-6 rounded-lg bg-white shadow text-gray-500 text-center text-sm">No active categories available yet.</div>
            @else
                <div class="flex flex-col gap-5">
                    @foreach ($domains as $domain)
                        @php
                            $subtitle = $domain->seo_description;
                            $accentColor = 'bg-indigo-600';
                            $shadowColor = 'shadow-indigo-100';
                            $btnBgClass = 'bg-indigo-600 group-hover:bg-indigo-700';
                            if (!$subtitle) $subtitle = 'Practice with real exam questions';
                        @endphp
                        
                        <a href="{{ route('batches', $domain) }}" wire:navigate class="group relative block w-full bg-white rounded-2xl p-5 shadow-lg {{ $shadowColor }} hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-50">
                            <div class="absolute left-0 top-3 bottom-3 w-1.5 rounded-r-full {{ $accentColor }}"></div>
                            <div class="flex items-center justify-between relative z-10 pl-3">
                                <div class="flex flex-col items-start gap-1.5 flex-1">
                                    <h3 class="text-lg font-bold text-gray-800 leading-tight">{{ $domain->title }}</h3>
                                    <p class="text-gray-500 text-xs sm:text-sm font-medium leading-relaxed">{{ $subtitle }}</p>
                                </div>
                                <div class="shrink-0">
                                    <div class="flex items-center gap-1 {{ $btnBgClass }} text-white text-sm font-bold py-2 px-3 sm:px-4 rounded-lg shadow-md transition-colors">
                                        <span>Start Exam</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</div>
