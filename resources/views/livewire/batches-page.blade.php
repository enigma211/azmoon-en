<div class="min-h-screen bg-gradient-to-b from-slate-50 to-white">
    <div class="mx-auto max-w-4xl px-4 py-10">
        
        <!-- Page Header -->
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-slate-800 mb-2">{{ $domain->title }}</h1>
            <p class="text-slate-500">Select your state to view available exams</p>
        </div>

        <!-- States Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @php
                $colors = [
                    'bg-blue-50 text-blue-700 hover:bg-blue-100 border-blue-100',
                    'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border-emerald-100',
                    'bg-purple-50 text-purple-700 hover:bg-purple-100 border-purple-100',
                    'bg-amber-50 text-amber-700 hover:bg-amber-100 border-amber-100',
                    'bg-rose-50 text-rose-700 hover:bg-rose-100 border-rose-100',
                    'bg-cyan-50 text-cyan-700 hover:bg-cyan-100 border-cyan-100',
                    'bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border-indigo-100',
                    'bg-teal-50 text-teal-700 hover:bg-teal-100 border-teal-100',
                    'bg-fuchsia-50 text-fuchsia-700 hover:bg-fuchsia-100 border-fuchsia-100',
                    'bg-orange-50 text-orange-700 hover:bg-orange-100 border-orange-100',
                    'bg-sky-50 text-sky-700 hover:bg-sky-100 border-sky-100',
                    'bg-lime-50 text-lime-700 hover:bg-lime-100 border-lime-100',
                ];
            @endphp

            @foreach($batches as $index => $batch)
                @php
                    $colorClass = $colors[$index % count($colors)];
                @endphp
                <a href="{{ route('exams', ['batch' => $batch->id]) }}" 
                   wire:navigate 
                   class="group relative px-5 py-4 rounded-xl border {{ $colorClass }} transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                    <span class="font-bold text-sm md:text-base flex items-center justify-between">
                        {{ $batch->title }}
                        @if($batch->is_active)
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 opacity-50 group-hover:opacity-100 transition-opacity">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        @endif
                    </span>
                    @if(!$batch->is_active)
                        <span class="absolute top-2 right-2 w-2 h-2 bg-gray-400 rounded-full" title="Inactive"></span>
                    @endif
                </a>
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
