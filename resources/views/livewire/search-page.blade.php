<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        
        <!-- Search Header -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-8 border border-gray-100">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">جستجوی پیشرفته سوالات</h1>
            
            <form wire:submit="search" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label for="query" class="sr-only">متن جستجو</label>
                    <div class="relative">
                        <input 
                            type="text" 
                            wire:model="query" 
                            placeholder="متن سوال یا کلمه کلیدی را بنویسید... (مثلا: آسانسور)" 
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 pr-11 text-base"
                        >
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                    </div>
                    <!-- Search Help Text -->
                    <p class="mt-2 text-xs text-gray-500">
                        <span class="font-bold text-gray-700">نکته جستجو:</span> برای یافتن دقیق کلمات چند بخشی، می‌توانید از حالات مختلف استفاده کنید. 
                        مثلاً: <span class="bg-gray-100 px-1 rounded">پی سازی</span> (فاصله معمولی)، 
                        <span class="bg-gray-100 px-1 rounded">پی‌سازی</span> (نیم‌فاصله) یا 
                        <span class="bg-gray-100 px-1 rounded">پیسازی</span> (بدون فاصله).
                    </p>
                </div>

                <div class="w-full md:w-64">
                    <label for="domain" class="sr-only">دامنه آزمون</label>
                    <select 
                        wire:model="domain" 
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 text-base"
                    >
                        <option value="">همه دامنه‌ها</option>
                        @foreach($domains as $d)
                            <option value="{{ $d->id }}">{{ $d->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Button -->
                <div class="w-full md:w-auto">
                    <button type="submit" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl transition-colors shadow-md flex items-center justify-center gap-2">
                        <span>جستجو</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Results -->
        <div class="space-y-4">
            @if(strlen($query) < 2)
                <div class="text-center text-gray-500 py-12 bg-white rounded-2xl border border-dashed border-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-16 h-16 mx-auto mb-4 text-gray-300">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <p class="text-lg">برای شروع جستجو حداقل ۲ حرف وارد کنید و دکمه جستجو را بزنید.</p>
                </div>
            @elseif($results->isEmpty())
                <div class="text-center text-gray-500 py-12 bg-white rounded-2xl border border-dashed border-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-16 h-16 mx-auto mb-4 text-gray-300">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <p class="text-lg font-medium text-gray-900">هیچ نتیجه‌ای یافت نشد</p>
                    <p class="text-sm mt-1">لطفاً کلمات کلیدی دیگری را امتحان کنید یا دامنه جستجو را تغییر دهید.</p>
                </div>
            @else
                <div class="flex items-center justify-between px-2 mb-2">
                    <span class="text-sm text-gray-500">تعداد نتایج: {{ $results->total() }} مورد</span>
                </div>

                @foreach($results as $question)
                    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:border-indigo-200 transition-colors">
                        <!-- Header: Exam & Domain Info -->
                        <div class="flex items-center flex-wrap gap-2 mb-3 text-xs">
                            @if($question->exam && $question->exam->batch && $question->exam->batch->domain)
                                <span class="px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 font-bold">
                                    {{ $question->exam->batch->domain->title }}
                                </span>
                            @endif
                            
                            @if($question->exam)
                                <span class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 font-medium flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $question->exam->title }}
                                </span>
                            @endif

                            <span class="mr-auto text-gray-400">
                                سوال {{ $question->order_column }}
                            </span>
                        </div>

                        <!-- Question Text -->
                        <div class="prose prose-sm max-w-none text-gray-800 font-medium leading-loose mb-4">
                            @php
                                // Simple highlighting
                                $highlighted = preg_replace('/(' . preg_quote($query, '/') . ')/iu', '<span class="bg-yellow-200 rounded px-0.5">$1</span>', strip_tags($question->text));
                            @endphp
                            {!! $highlighted !!}
                        </div>

                        <!-- Action -->
                        <div class="flex justify-end mt-4 border-t pt-3 border-gray-50">
                            <a 
                                href="{{ route('exam.study', ['exam' => $question->exam_id]) }}?q={{ $question->id }}" 
                                class="inline-flex items-center gap-1 text-sm font-bold text-indigo-600 hover:text-indigo-700 hover:underline"
                            >
                                مشاهده در آزمون
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                    <path fill-rule="evenodd" d="M15 10a.75.75 0 01-.75.75H7.612l2.158 1.96a.75.75 0 11-1.04 1.08l-3.5-3.25a.75.75 0 010-1.08l3.5-3.25a.75.75 0 111.04 1.08L7.612 9.25h6.638A.75.75 0 0115 10z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $results->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
