<div class="mx-auto max-w-md p-4 sm:p-6">
    <!-- Header -->
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $batch->title }}</h1>
        
        
        <!-- Alerts Container -->
        <div class="mt-6 space-y-3">
            <!-- Alert 1 -->
        </div>
    </div>

    <!-- Exams List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($exams as $exam)
            <div class="group relative flex flex-col bg-white rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden border border-gray-100 h-full">
                
                <!-- Badge (Optional) -->
                @if($exam->badge_text)
                    <div class="py-2 px-4 text-center text-white font-bold text-sm tracking-wide" 
                         style="background-color: {{ $exam->badge_color ?? '#6366f1' }}">
                        {{ $exam->badge_text }}
                    </div>
                @endif

                <!-- Thumbnail (Optional) -->
                @if($exam->thumbnail)
                    <div class="aspect-video w-full overflow-hidden bg-gray-100 relative">
                        <img src="{{ asset('storage/' . $exam->thumbnail) }}" 
                             alt="{{ $exam->title }}" 
                             class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                    </div>
                @endif
                
                <!-- Content Container -->
                <div class="p-6 flex flex-col flex-grow">
                    
                    <!-- Title -->
                    <h3 class="font-bold text-lg text-gray-800 mb-3 leading-tight">
                        {{ $exam->title }}
                    </h3>

                    <!-- Description -->
                    @if($exam->description)
                        <p class="text-sm text-gray-500 leading-relaxed mb-4 line-clamp-3">
                            {{ $exam->description }}
                        </p>
                    @endif
                    
                    <div class="mt-auto">
                        <!-- Meta Info -->
                        <div class="flex items-center gap-4 mb-6 pt-4 border-t border-gray-50">
                            <!-- Question Count -->
                            @php
                                $questionCount = $exam->questions()->where('is_deleted', false)->count();
                            @endphp
                            <div class="flex items-center gap-1.5 text-gray-600" title="Questions Count">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                                </svg>
                                <span class="text-sm font-medium">Questions: {{ $questionCount }}</span>
                            </div>

                            <!-- Duration -->
                            <div class="flex items-center gap-1.5 text-gray-600" title="Duration">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <span class="text-sm font-medium">
                                    {{ $exam->duration_minutes ? $exam->duration_minutes . 'm' : 'No Limit' }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <a href="{{ route('exam.play', ['exam' => $exam->id]) }}" wire:navigate class="block w-full">
                            <div class="w-full bg-white text-indigo-600 border border-indigo-600 hover:bg-indigo-600 hover:text-white font-bold text-sm py-3 rounded-full shadow-sm text-center transition-all duration-300 uppercase tracking-wider">
                                Start Test
                            </div>
                        </a>
                    </div>
                    
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Empty state -->
    @if($exams->isEmpty())
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <p class="text-gray-500">No exams available in this batch yet.</p>
        </div>
    @endif

    <!-- Batch Description -->
    @if($batch->description)
        <div class="mt-12 border-t border-gray-100 pt-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Description</h2>
            <div class="prose prose-slate max-w-none bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-gray-600">
                {!! $batch->description !!}
            </div>
        </div>
    @endif
</div>
