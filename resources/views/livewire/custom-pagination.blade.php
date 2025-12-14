@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-center mt-8">
        <div class="flex items-center gap-2 bg-white p-2 rounded-full shadow-sm border border-gray-100">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                    <span class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-50 text-gray-300 cursor-not-allowed" aria-hidden="true">
                        <svg class="w-5 h-5 rtl:rotate-180" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </span>
            @else
                <button type="button" wire:click="previousPage" wire:loading.attr="disabled" rel="prev" class="flex items-center justify-center w-10 h-10 rounded-full bg-white text-gray-500 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200" aria-label="{{ __('pagination.previous') }}">
                    <svg class="w-5 h-5 rtl:rotate-180" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
            @endif

            {{-- Pagination Elements --}}
            <div class="hidden sm:flex gap-1">
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span aria-disabled="true">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-transparent text-gray-400">{{ $element }}</span>
                        </span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page">
                                    <span class="flex items-center justify-center w-10 h-10 rounded-full bg-indigo-600 text-white font-bold shadow-md ring-2 ring-indigo-100">{{ $page }}</span>
                                </span>
                            @else
                                <button type="button" wire:click="gotoPage({{ $page }})" class="flex items-center justify-center w-10 h-10 rounded-full bg-white text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 font-medium transition-all duration-200">{{ $page }}</button>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Mobile View (Simplified) --}}
            <div class="sm:hidden flex items-center text-sm text-gray-500 font-medium px-2">
                <span>صفحه {{ $paginator->currentPage() }} از {{ $paginator->lastPage() }}</span>
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <button type="button" wire:click="nextPage" wire:loading.attr="disabled" rel="next" class="flex items-center justify-center w-10 h-10 rounded-full bg-white text-gray-500 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200" aria-label="{{ __('pagination.next') }}">
                    <svg class="w-5 h-5 rtl:rotate-180" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
            @else
                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                    <span class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-50 text-gray-300 cursor-not-allowed" aria-hidden="true">
                        <svg class="w-5 h-5 rtl:rotate-180" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </span>
            @endif
        </div>
    </nav>
@endif
