<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-8"
     oncontextmenu="return false;" 
     onselectstart="return false;" 
     oncopy="return false;"
     oncut="return false;"
     style="user-select: none; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none;">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Video Content -->
        @if($category->type === 'video' && $post->video_embed_code)
            <div class="mb-6">
                <div class="aspect-video bg-gray-900 rounded-xl overflow-hidden shadow-lg">
                    {!! $post->video_embed_code !!}
                </div>
            </div>
        @endif

        <!-- Compact Title & Description -->
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl shadow-md p-6 mb-6 border border-indigo-100">
            <h1 class="text-2xl font-bold text-indigo-900 mb-3">{{ $post->title }}</h1>
            
            @if($post->description)
                <p class="text-indigo-700 text-base mb-0">{{ $post->description }}</p>
            @endif
        </div>

        <!-- Text Content -->
        @if($post->content)
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <div class="prose prose-base max-w-none text-gray-700">
                    {!! $post->content !!}
                </div>
            </div>
        @endif

        <!-- Back Button -->
        <div class="text-center mb-8">
            <a href="{{ route('educational-resources.posts', [$examType->slug, $category->slug]) }}" 
               wire:navigate
               class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                </svg>
                بازگشت به لیست
            </a>
        </div>
    </div>
</div>
