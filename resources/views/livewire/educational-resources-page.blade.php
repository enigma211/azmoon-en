<div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <section class="py-6">
            <h2 class="text-lg font-bold mb-6 text-center text-gray-600">منابع آموزشی</h2>

            @if(($examTypes ?? collect())->isEmpty())
                <div class="p-6 rounded-lg bg-white shadow text-gray-500 text-sm text-center">
                    هنوز منابع آموزشی اضافه نشده است.
                </div>
            @else
                <div class="flex flex-col gap-5">
                    @foreach($examTypes as $examType)
                        @php
                            $isNezam = str_contains($examType->title, 'نظام مهندسی');
                            $isKarshenas = str_contains($examType->title, 'کارشناس رسمی');
                            
                            $subtitle = $examType->description;

                            // Define colors and text based on type
                            if ($isNezam) {
                                $accentColor = 'bg-blue-600';
                                $shadowColor = 'shadow-blue-100';
                                $btnBgClass = 'bg-blue-600 group-hover:bg-blue-700';
                                if (!$subtitle) $subtitle = 'جزوات و منابع آزمون نظام مهندسی';
                            } elseif ($isKarshenas) {
                                $accentColor = 'bg-amber-600';
                                $shadowColor = 'shadow-amber-100';
                                $btnBgClass = 'bg-[#E67E22] group-hover:bg-[#d35400]';
                                if (!$subtitle) $subtitle = 'منابع تخصصی آزمون کارشناس رسمی';
                            } else {
                                $accentColor = 'bg-gray-400';
                                $shadowColor = 'shadow-gray-100';
                                $btnBgClass = 'bg-gray-600 group-hover:bg-gray-700';
                                if (!$subtitle) $subtitle = 'مجموعه منابع آموزشی تخصصی';
                            }
                        @endphp

                        <a href="{{ route('educational-resources.categories', $examType->slug) }}" 
                           wire:navigate 
                           class="group relative block w-full bg-white rounded-2xl p-5 shadow-lg {{ $shadowColor }} hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-50">
                            <!-- Accent Bar (Left Side) -->
                            <div class="absolute left-0 top-3 bottom-3 w-1.5 rounded-r-full {{ $accentColor }}"></div>

                            <div class="flex items-center justify-between relative z-10 pl-3">
                                <!-- Content (Right Side in RTL) -->
                                <div class="flex flex-col items-start gap-1.5 flex-1">
                                    <!-- Title -->
                                    <h3 class="text-lg font-bold text-gray-800 leading-tight">
                                        {{ $examType->title }}
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
                                        <span>مشاهده منابع</span>
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
