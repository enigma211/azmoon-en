<div class="min-h-screen bg-gradient-to-b from-slate-50 to-white">
    <div class="mx-auto max-w-4xl px-4 py-10">
        
        <!-- Page Header -->
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-slate-800 mb-2">{{ $domain->title }}</h1>
            <p class="text-slate-500">Select your state to view available exams</p>
        </div>

        <!-- States Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            @foreach($batches as $batch)
                <a href="{{ route('exams', ['batch' => $batch->id]) }}" 
                   wire:navigate 
                   class="group relative px-4 py-3 bg-white rounded-lg border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50 transition-all duration-200 hover:shadow-sm">
                    <span class="text-slate-700 group-hover:text-indigo-600 font-medium text-sm transition-colors">
                        {{ $batch->title }}
                    </span>
                    @if(!$batch->is_active)
                        <span class="absolute top-1 right-1 w-2 h-2 bg-gray-300 rounded-full" title="Inactive"></span>
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
