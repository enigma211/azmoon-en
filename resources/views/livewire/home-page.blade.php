<div>
    <div class="max-w-4xl mx-auto px-4">
        <!-- Slider Section -->
        <section class="py-6">
            @if(($sliders ?? collect())->isNotEmpty())
                <!-- Slider Container -->
                <div class="relative overflow-hidden rounded-xl shadow-lg bg-white" x-data="slider({{ $sliders->count() }})">
                    <!-- Slides -->
                    <div class="relative h-56 sm:h-72 md:h-80 lg:h-96">
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

        <!-- Exam Domains Section -->
        <section class="py-6">
            @if(($domains ?? collect())->isEmpty())
                <div class="p-6 rounded-lg bg-white shadow text-gray-500 text-sm">No active categories available yet.</div>
            @else
                <div class="flex flex-col gap-5">
                    @foreach ($domains as $domain)
                        @php
                            $accentColor = 'bg-indigo-600';
                            $shadowColor = 'shadow-indigo-100';
                            $btnBgClass = 'bg-indigo-600 group-hover:bg-indigo-700';
                        @endphp
                        
                        <a href="{{ route('batches', $domain) }}" class="group relative block w-full bg-white rounded-2xl p-5 shadow-lg {{ $shadowColor }} hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-50">
                            <div class="absolute left-0 top-3 bottom-3 w-1.5 rounded-r-full {{ $accentColor }}"></div>
                            <div class="flex items-center justify-between relative z-10 pl-3">
                                <div class="flex flex-col items-start gap-1.5 flex-1">
                                    <h3 class="text-lg font-bold text-gray-800 leading-tight">{{ $domain->title }}</h3>
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
