<div class="min-h-screen bg-gray-50">
    <div class="mx-auto max-w-6xl px-4 py-8">
        
        <!-- Batches List -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                <div class="group relative rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 bg-white overflow-hidden border border-gray-100">
                    <!-- Card Header (Colored) -->
                    <div class="{{ $bgColor }} px-6 py-4 flex items-center justify-between text-white">
                        <!-- Title (Left) -->
                        <h3 class="text-lg font-bold flex items-center gap-2">
                            {{ $batch->title }}
                        </h3>

                        <!-- Calendar Icon (Right) -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 opacity-90">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6 bg-white flex flex-col items-center">
                        <a href="{{ route('exams', ['batch' => $batch->id]) }}" wire:navigate class="block w-full">
                            <div class="{{ $btnColor }} text-white font-bold text-center py-3 px-4 rounded-xl shadow-sm transition-all transform group-hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-2 text-base">
                                <span>View Exams</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </div>
                        </a>

                        <!-- Inactive Badge -->
                        @if(!$batch->is_active)
                            <div class="mt-3 text-gray-400 text-xs font-medium bg-gray-50 px-3 py-1 rounded-full border border-gray-100">
                                Inactive
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
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Batches Found</h3>
                <p class="text-gray-500">Currently, there are no exam batches available in this domain.</p>
            </div>
        @endif

        <!-- Domain Description -->
        @if($domain->description)
            <div class="mt-8 prose prose-slate max-w-none bg-white p-6 rounded-2xl shadow-sm">
                {!! $domain->description !!}
            </div>
        @endif
    </div>
</div>
