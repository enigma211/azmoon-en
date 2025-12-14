<div>
    <div class="max-w-4xl mx-auto px-4">
        <!-- Slider Section -->
        <section class="py-6">
            @if(($sliders ?? collect())->isNotEmpty())
                <!-- Slider Container -->
                <div class="relative overflow-hidden rounded-xl shadow-lg bg-white" x-data="slider({{ $sliders->count() }})">
                    <!-- Slides -->
                    <div class="relative h-48 sm:h-56 md:h-64 lg:h-64">
                        @foreach ($sliders as $index => $slider)
                            <div 
                                x-show="currentSlide === {{ $index }}"
                                x-transition:enter="transition ease-out duration-500"
                                x-transition:enter-start="opacity-0 transform translate-x-full"
                                x-transition:enter-end="opacity-100 transform translate-x-0"
                                x-transition:leave="transition ease-in duration-500"
                                x-transition:leave-start="opacity-100 transform translate-x-0"
                                x-transition:leave-end="opacity-0 transform -translate-x-full"
                                class="absolute inset-0"
                            >
                                @if($slider->link)
                                    <a href="{{ $slider->link }}" class="block w-full h-full">
                                        <img 
                                            src="{{ Storage::url($slider->image) }}" 
                                            alt="{{ $slider->title ?? 'Slider' }}" 
                                            class="w-full h-full object-cover"
                                        >
                                    </a>
                                @else
                                    <img 
                                        src="{{ Storage::url($slider->image) }}" 
                                        alt="{{ $slider->title ?? 'Slider' }}" 
                                        class="w-full h-full object-cover"
                                    >
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($sliders->count() > 1)
                        <div class="absolute bottom-2 sm:bottom-3 md:bottom-4 left-1/2 -translate-x-1/2 flex gap-1.5 sm:gap-2 z-10">
                            @foreach ($sliders as $index => $slider)
                                <button 
                                    @click="currentSlide = {{ $index }}"
                                    :class="currentSlide === {{ $index }} ? 'bg-white' : 'bg-white/50'"
                                    class="w-2 h-2 sm:w-2.5 sm:h-2.5 md:w-3 md:h-3 rounded-full transition"
                                    aria-label="Slide {{ $index + 1 }}"
                                ></button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <script>
                    function slider(totalSlides) {
                        return {
                            currentSlide: 0,
                            totalSlides: totalSlides,
                            autoplayInterval: null,
                            init() { this.startAutoplay(); },
                            nextSlide() { this.currentSlide = (this.currentSlide + 1) % this.totalSlides; this.resetAutoplay(); },
                            prevSlide() { this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides; this.resetAutoplay(); },
                            startAutoplay() { if (this.totalSlides > 1) { this.autoplayInterval = setInterval(() => { this.nextSlide(); }, 7000); } },
                            resetAutoplay() { clearInterval(this.autoplayInterval); this.startAutoplay(); }
                        }
                    }
                </script>
            @endif
        </section>

        <!-- Search Section -->
        <section class="py-4">
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-700 shadow-xl text-white p-6 sm:p-10">
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>
                
                <div class="relative z-10 max-w-3xl mx-auto text-center">
                    <h2 class="text-2xl sm:text-3xl font-black mb-3">{{ $heroTitle }}</h2>
                    @if($heroDescription)
                        <p class="text-white/90 mb-6 text-sm sm:text-base leading-relaxed">{{ $heroDescription }}</p>
                    @endif

                    <form action="{{ route('search') }}" method="GET" class="bg-white p-2 rounded-2xl shadow-lg flex flex-col sm:flex-row gap-2">
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                            </div>
                            <input type="text" name="q" placeholder="Search exams, topics, questions..." class="w-full border-0 bg-transparent py-3 pl-10 pr-4 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm" required>
                        </div>
                        <div class="sm:w-48 relative border-t sm:border-t-0 sm:border-l border-gray-100">
                            <select name="domain" class="w-full border-0 bg-transparent py-3 pl-4 pr-8 text-gray-900 focus:ring-0 sm:text-sm cursor-pointer">
                                <option value="">All Domains</option>
                                @if(isset($domains))
                                    @foreach ($domains as $domain)
                                        <option value="{{ $domain->id }}">{{ $domain->title }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-2.5 px-6 rounded-xl transition-colors shadow-md flex items-center justify-center gap-2">
                            <span>Search</span>
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Exam Domains Section -->
        <section class="py-6">
            @if(($domains ?? collect())->isEmpty())
                <div class="p-6 rounded-lg bg-white shadow text-gray-500 text-sm">No active categories available yet.</div>
            @else
                <div class="flex flex-col gap-5">
                    @foreach ($domains as $domain)
                        @php
                            $subtitle = $domain->description;
                            $accentColor = 'bg-indigo-600';
                            $shadowColor = 'shadow-indigo-100';
                            $btnBgClass = 'bg-indigo-600 group-hover:bg-indigo-700';
                            if (!$subtitle) $subtitle = 'Practice with real exam questions';
                        @endphp
                        
                        <a href="{{ route('batches', $domain) }}" class="group relative block w-full bg-white rounded-2xl p-5 shadow-lg {{ $shadowColor }} hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-50">
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
