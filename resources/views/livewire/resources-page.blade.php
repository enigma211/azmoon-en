<div class="min-h-screen bg-gradient-to-br from-gray-50 to-white">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <header class="mb-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900">منابع آموزشی</h1>
            </div>
        </header>

        @if(isset($items) && $items->count())
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($items as $item)
                    <a href="{{ route('resource.detail', ['resource' => $item->id]) }}" 
                       wire:navigate
                       class="group relative bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-gray-200">
                        
                        
                        <!-- Content -->
                        <div class="p-6">
                            <h3 class="font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-indigo-600 transition-colors">
                                {{ $item->title }}
                            </h3>
                            
                            @if($item->description)
                                <p class="text-sm text-gray-600 leading-relaxed mb-3 line-clamp-3">
                                    {{ \Illuminate\Support\Str::limit($item->description, 100) }}
                                </p>
                            @endif
                            
                            <!-- Type Badge -->
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                                    @switch($item->type)
                                        @case('pdf')
                                            bg-red-100 text-red-800
                                            @break
                                        @case('video')
                                            bg-blue-100 text-blue-800
                                            @break
                                        @default
                                            bg-gray-100 text-gray-800
                                            @break
                                    @endswitch
                                ">
                                    @switch($item->type)
                                        @case('pdf')
                                            PDF
                                            @break
                                        @case('video')
                                            ویدیو
                                            @break
                                        @default
                                            {{ $item->type }}
                                            @break
                                    @endswitch
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $items->onEachSide(1)->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-6">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">منبعی یافت نشد</h3>
                <p class="text-gray-500">در حال حاضر هیچ فایل PDF یا ویدیویی در این بخش موجود نیست.</p>
            </div>
        @endif
    </div>
</div>
