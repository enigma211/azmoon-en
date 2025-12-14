<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-12">
    <div class="container mx-auto px-4">
        

        <!-- Header -->
        <div class="text-center mb-12">
            @if($category->type === 'video')
                <h1 class="text-2xl font-bold text-gray-900 mb-4">ویدیوهای آموزشی آزمون {{ $examType->title }}</h1>
            @elseif($category->type === 'document')
                <h1 class="text-2xl font-bold text-gray-900 mb-4">جزوات آموزشی آزمون {{ $examType->title }}</h1>
            @else
                <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ $category->title }}</h1>
            @endif
        </div>

        <!-- Posts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($posts as $post)
                <a href="{{ route('educational-resources.post', [$examType->slug, $category->slug, $post->slug]) }}" 
                   wire:navigate
                   class="group bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-indigo-200">
                    <div class="p-5">
                        <!-- Content only (no thumbnail) -->
                        <div class="flex-1">
                            <h3 class="text-base font-bold text-gray-900 mb-2 group-hover:text-indigo-700 transition-colors line-clamp-2">
                                {{ $post->title }}
                            </h3>

                            @if($post->description)
                                <p class="text-gray-600 text-sm mb-0 line-clamp-2">{{ $post->description }}</p>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        @if($posts->isEmpty())
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-200 rounded-full mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <p class="text-gray-600 text-lg">هنوز محتوایی اضافه نشده است.</p>
            </div>
        @endif

        <!-- Pagination -->
        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    </div>
</div>
