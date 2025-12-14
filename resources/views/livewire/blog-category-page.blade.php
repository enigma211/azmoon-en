<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Text -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $currentCategory->title }}</h1>
            <p class="text-gray-600">Archive of posts in {{ $currentCategory->title }}</p>
        </div>

        <!-- Categories Navigation -->
        <div class="flex overflow-x-auto pb-4 mb-6 gap-2 no-scrollbar">
            <a href="{{ route('blog.index') }}" class="px-4 py-2 rounded-full bg-white text-gray-700 text-sm font-medium hover:bg-gray-100 hover:text-indigo-600 whitespace-nowrap border border-gray-200 transition-colors">
                All Posts
            </a>
            @foreach($categories as $category)
                <a href="{{ route('blog.category', $category->slug) }}" class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap border transition-colors {{ $category->id === $currentCategory->id ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-100 hover:text-indigo-600' }}">
                    {{ $category->title }}
                </a>
            @endforeach
        </div>

        <!-- Blog List -->
        <div class="space-y-6">
            @if($posts->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                    </svg>
                    <p class="text-gray-500 text-lg">No posts published in this category yet.</p>
                </div>
            @else
                @foreach($posts as $post)
                    <article class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden flex flex-col sm:flex-row h-auto sm:h-48">
                        <!-- Image (Right side in RTL) -->
                        <a href="{{ route('blog.show', ['category' => $post->category->slug ?? 'general', 'slug' => $post->slug]) }}" class="w-full sm:w-1/3 h-48 sm:h-full relative group flex-shrink-0">
                            @if($post->image_path)
                                <img src="{{ Storage::url($post->image_path) }}" alt="{{ $post->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            @else
                                <div class="w-full h-full bg-indigo-50 flex items-center justify-center text-indigo-200">
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @endif
                        </a>

                        <!-- Content (Left side) -->
                        <div class="p-5 flex flex-col justify-between flex-1 w-full">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    @if($post->category)
                                        <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded">
                                            {{ $post->category->title }}
                                        </span>
                                    @endif
                                    <span class="text-xs text-gray-400">
                                        {{ @formatDate($post->published_at)->format('%d %B %Y') }}
                                    </span>
                                </div>

                                <h2 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2 hover:text-indigo-600 transition-colors">
                                    <a href="{{ route('blog.show', ['category' => $post->category->slug ?? 'general', 'slug' => $post->slug]) }}">
                                        {{ $post->title }}
                                    </a>
                                </h2>

                                <p class="text-gray-600 text-sm leading-relaxed line-clamp-2 hidden sm:block">
                                    {{ $post->summary }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between mt-4 sm:mt-0">
                                <div class="flex items-center text-xs text-gray-500 gap-3">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        {{ number_format($post->view_count) }} views
                                    </span>
                                </div>
                                
                                <a href="{{ route('blog.show', ['category' => $post->category->slug ?? 'general', 'slug' => $post->slug]) }}" class="text-indigo-600 text-xs font-semibold flex items-center hover:underline">
                                    Read More
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach

                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
