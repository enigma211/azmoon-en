<div class="min-h-screen bg-gray-50">
    <div class="mx-auto max-w-md px-4 py-8">
        <!-- Header -->
        <header class="mb-8 text-center">
            <h1 class="text-lg font-bold text-gray-900">آزمون‌های رسمی دوره‌های گذشته</h1>
            @if(isset($domain) && $domain->id == 1)
                <div class="mt-3 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-xs leading-relaxed text-amber-900 text-right">
                    کلیه سوالات از وب سایت دفتر مقررات ملی ساختمان که بصورت رایگان در اختیار مهندسین قرار گرفته است دریافت شده است
                </div>
            @elseif(isset($domain) && $domain->id == 3)
                <div class="mt-3 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-xs leading-relaxed text-amber-900 text-right">
                    کلیه سوالات از وب سایت شورای عالی کارشناسان رسمی دادگستری که بصورت رایگان در اختیار مهندسین قرار گرفته است دریافت شده است
                </div>
            @endif
        </header>

        <!-- Batches List -->
        <div class="flex flex-col gap-5">
            @php
                $styles = [
                    ['bg' => 'bg-[#154c79]', 'btn' => 'bg-[#154c79] hover:bg-[#0f3a5d]'], // Deep Blue
                    ['bg' => 'bg-[#2d8b88]', 'btn' => 'bg-[#2d8b88] hover:bg-[#236e6b]'], // Teal
                    ['bg' => 'bg-[#cfa438]', 'btn' => 'bg-[#cfa438] hover:bg-[#b38d2f]'], // Gold/Mustard
                    ['bg' => 'bg-[#8e44ad]', 'btn' => 'bg-[#8e44ad] hover:bg-[#732d91]'], // Purple
                    ['bg' => 'bg-[#c0392b]', 'btn' => 'bg-[#c0392b] hover:bg-[#a93226]'], // Red
                ];
            @endphp

            @foreach($batches as $index => $batch)
                @php
                    $style = $styles[$index % count($styles)];
                    $bgColor = $style['bg'];
                    $btnColor = $style['btn'];
                @endphp

                <div class="relative rounded-2xl shadow-md bg-white overflow-hidden">
                    <!-- Card Header (Colored) -->
                    <div class="{{ $bgColor }} px-6 py-2 flex items-center justify-between text-white">
                        <!-- Title (Right) -->
                        <h3 class="text-base font-bold flex items-center gap-2">
                            {{ $batch->title }}
                        </h3>

                        <!-- Calendar Icon (Left) -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 opacity-90">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                    </div>

                    <!-- Card Body -->
                    <div class="p-7 bg-white flex flex-col items-center">
                        <a href="{{ route('exams', ['batch' => $batch->id]) }}" wire:navigate class="block w-fit mx-auto">
                            <div class="{{ $btnColor }} text-white font-bold text-center py-2 px-3 rounded-xl shadow transition-colors flex items-center justify-center gap-2 text-sm">
                                <span>مشاهده آزمون‌ها</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                                </svg>
                            </div>
                        </a>

                        <!-- Inactive Badge -->
                        @if(!$batch->is_active)
                            <div class="mt-2 text-gray-400 text-xs font-medium bg-gray-100 px-3 py-1 rounded-full">
                                غیرفعال
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Empty State -->
        @if($batches->isEmpty())
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-6">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">نوبتی یافت نشد</h3>
                <p class="text-gray-500">در حال حاضر هیچ نوبت آزمونی در این دامنه موجود نیست.</p>
            </div>
        @endif
    </div>
</div>
