<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        
        <!-- Search Header -->
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-8 border border-gray-100">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Search Exams</h1>
            
            <form wire:submit="search" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label for="query" class="sr-only">Search Text</label>
                    <div class="relative">
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="query" 
                            placeholder="Search exam titles... (e.g. Texas Driver License)" 
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 pl-11 text-base"
                            autofocus
                        >
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                    </div>
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
                    <p class="text-lg">Enter at least 2 characters to search exams.</p>
                </div>
            @elseif($results->isEmpty())
                <div class="text-center text-gray-500 py-12 bg-white rounded-2xl border border-dashed border-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-16 h-16 mx-auto mb-4 text-gray-300">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <p class="text-lg font-medium text-gray-900">No exams found</p>
                    <p class="text-sm mt-1">Try different keywords.</p>
                </div>
            @else
                <div class="flex items-center justify-between px-2 mb-2">
                    <span class="text-sm text-gray-500">Found {{ $results->total() }} exam(s)</span>
                </div>

                @foreach($results as $exam)
                    <a href="{{ route('exam.landing', $exam) }}" wire:navigate class="block bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:border-indigo-300 hover:shadow-md transition-all group">
                        <div class="flex items-start gap-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center group-hover:bg-indigo-200 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-indigo-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" />
                                </svg>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                    @php
                                        $highlighted = preg_replace('/(' . preg_quote($query, '/') . ')/iu', '<span class="bg-yellow-200 rounded px-0.5">$1</span>', e($exam->title));
                                    @endphp
                                    {!! $highlighted !!}
                                </h3>
                                
                                @if($exam->batch && $exam->batch->domain)
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 font-medium">
                                            {{ $exam->batch->domain->title }}
                                        </span>
                                        <span class="text-xs text-gray-400">•</span>
                                        <span class="text-xs text-gray-500">{{ $exam->batch->title }}</span>
                                    </div>
                                @endif
                                
                                @if($exam->description)
                                    <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ Str::limit($exam->description, 150) }}</p>
                                @endif
                                
                                <div class="flex items-center gap-4 mt-3 text-xs text-gray-400">
                                    @if($exam->duration_minutes)
                                        <span class="flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                            {{ $exam->duration_minutes }} min
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Arrow -->
                            <div class="flex-shrink-0 text-gray-300 group-hover:text-indigo-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </div>
                        </div>
                    </a>
                @endforeach

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $results->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
