<div class="mx-auto max-w-2xl p-4 space-y-4">
    <h1 class="text-lg font-bold">{{ $resource->title }}</h1>
    @if($resource->description)
        <p class="text-sm text-gray-600">{{ $resource->description }}</p>
    @endif

    @php
        $ext = strtolower(pathinfo($url, PATHINFO_EXTENSION));
    @endphp

    @if($url)
        @if(in_array($ext, ['jpg','jpeg','png','webp','gif']))
            <img src="{{ $url }}" alt="{{ $resource->title }}" class="w-full rounded" />
        @elseif($ext === 'pdf')
            <iframe src="{{ $url }}" class="h-[70vh] w-full rounded" title="PDF"></iframe>
        @elseif(in_array($ext, ['mp4','webm','ogg']))
            <video class="w-full rounded" controls>
                <source src="{{ $url }}" type="video/{{ $ext }}" />
            </video>
        @else
            <a href="{{ $url }}" target="_blank" class="rounded bg-indigo-600 px-3 py-2 text-white">باز کردن</a>
        @endif
    @else
        <div class="rounded border p-4 text-sm text-gray-600">فایلی برای نمایش وجود ندارد.</div>
    @endif

    <div>
        <a href="{{ route('resources') }}" wire:navigate class="rounded bg-gray-100 px-4 py-2 text-gray-700">بازگشت</a>
    </div>
</div>
