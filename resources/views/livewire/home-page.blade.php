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
                                            alt="{{ $slider->title ?? 'اسلایدر' }}" 
                                            class="w-full h-full object-cover"
                                        >
                                    </a>
                                @else
                                    <img 
                                        src="{{ Storage::url($slider->image) }}" 
                                        alt="{{ $slider->title ?? 'اسلایدر' }}" 
                                        class="w-full h-full object-cover"
                                    >
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Navigation Arrows -->
                    @if($sliders->count() > 1)
                        <!-- Dots Indicator -->
                        <div class="absolute bottom-2 sm:bottom-3 md:bottom-4 left-1/2 -translate-x-1/2 flex gap-1.5 sm:gap-2 z-10">
                            @foreach ($sliders as $index => $slider)
                                <button 
                                    @click="currentSlide = {{ $index }}"
                                    :class="currentSlide === {{ $index }} ? 'bg-white' : 'bg-white/50'"
                                    class="w-2 h-2 sm:w-2.5 sm:h-2.5 md:w-3 md:h-3 rounded-full transition"
                                    aria-label="اسلاید {{ $index + 1 }}"
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

                            init() {
                                this.startAutoplay();
                            },

                            nextSlide() {
                                this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
                                this.resetAutoplay();
                            },

                            prevSlide() {
                                this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
                                this.resetAutoplay();
                            },

                            startAutoplay() {
                                if (this.totalSlides > 1) {
                                    this.autoplayInterval = setInterval(() => {
                                        this.nextSlide();
                                    }, 7000); // تغییر هر 7 ثانیه
                                }
                            },

                            resetAutoplay() {
                                clearInterval(this.autoplayInterval);
                                this.startAutoplay();
                            }
                        }
                    }
                </script>
            @endif
        </section>

        <!-- Search Section -->
        <section class="py-4">
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-700 shadow-xl text-white p-6 sm:p-10">
                <!-- Background Pattern -->
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>
                
                <div class="relative z-10 max-w-3xl mx-auto text-center">
                    <h2 class="text-2xl sm:text-3xl font-black mb-3">{{ $heroTitle }}</h2>
                    @if($heroDescription)
                        <p class="text-white/90 mb-6 text-sm sm:text-base leading-relaxed">
                            {{ $heroDescription }}
                        </p>
                    @endif

                    <form action="{{ route('search') }}" method="GET" class="bg-white p-2 rounded-2xl shadow-lg flex flex-col sm:flex-row gap-2">
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="q" 
                                placeholder="مثلا: آسانسور، پی‌سازی، مبحث نهم..." 
                                class="w-full border-0 bg-transparent py-3 pr-10 pl-4 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm sm:leading-6"
                                required
                            >
                        </div>

                        <div class="sm:w-48 relative border-t sm:border-t-0 sm:border-r border-gray-100">
                            <select 
                                name="domain" 
                                class="w-full border-0 bg-transparent py-3 pr-8 pl-4 text-gray-900 focus:ring-0 sm:text-sm sm:leading-6 cursor-pointer"
                            >
                                <option value="">همه دامنه‌ها</option>
                                @if(isset($domains))
                                    @foreach ($domains as $domain)
                                        <option value="{{ $domain->id }}">{{ $domain->title }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-2.5 px-6 rounded-xl transition-colors shadow-md flex items-center justify-center gap-2">
                            <span>جستجو</span>
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Exam Domains Section -->
        <section class="py-6">

            @if(($domains ?? collect())->isEmpty())
                <div class="p-6 rounded-lg bg-white shadow text-gray-500 text-sm">
                    هنوز دسته‌بندی فعالی وجود ندارد.
                </div>
            @else
                <div class="flex flex-col gap-5">
                    @foreach ($domains as $domain)
                        @php
                            $isNezam = str_contains($domain->title, 'نظام مهندسی');
                            $isKarshenas = str_contains($domain->title, 'کارشناس رسمی');
                            
                            $subtitle = $domain->description;

                            // Define colors and text based on type
                            if ($isNezam) {
                                $accentColor = 'bg-blue-600';
                                $shadowColor = 'shadow-blue-100';
                                $btnBgClass = 'bg-blue-600 group-hover:bg-blue-700';
                                if (!$subtitle) $subtitle = 'شبیه سازی دقیق سوالات دوره های گذشته';
                            } elseif ($isKarshenas) {
                                $accentColor = 'bg-amber-600';
                                $shadowColor = 'shadow-amber-100';
                                $btnBgClass = 'bg-[#E67E22] group-hover:bg-[#d35400]';
                                if (!$subtitle) $subtitle = 'بانک جامع سوالات سال های قبل';
                            } else {
                                $accentColor = 'bg-gray-400';
                                $shadowColor = 'shadow-gray-100';
                                $btnBgClass = 'bg-gray-600 group-hover:bg-gray-700';
                                if (!$subtitle) $subtitle = 'مجموعه آزمون‌های تخصصی و شبیه‌سازی';
                            }
                        @endphp
                        
                        <a href="{{ route('batches', $domain) }}" class="group relative block w-full bg-white rounded-2xl p-5 shadow-lg {{ $shadowColor }} hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-50">
                            <!-- Accent Bar (Left Side) -->
                            <div class="absolute left-0 top-3 bottom-3 w-1.5 rounded-r-full {{ $accentColor }}"></div>

                            <div class="flex items-center justify-between relative z-10 pl-3">
                                <!-- Content (Right Side in RTL) -->
                                <div class="flex flex-col items-start gap-1.5 flex-1">
                                    <!-- Title -->
                                    <h3 class="text-lg font-bold text-gray-800 leading-tight">
                                        {{ $domain->title }}
                                    </h3>
                                    <!-- Subtitle -->
                                    <p class="text-gray-500 text-xs sm:text-sm font-medium leading-relaxed pr-1">
                                        {{ $subtitle }}
                                    </p>
                                </div>

                                <!-- Button (Left Side in RTL) -->
                                <div class="shrink-0">
                                    <div class="flex items-center gap-1 {{ $btnBgClass }} text-white text-sm font-bold py-2 px-3 sm:px-4 rounded-lg shadow-md transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                                        </svg>
                                        <span>شروع آزمون</span>
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
