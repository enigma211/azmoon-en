<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
    <h3 class="text-base font-medium text-gray-900 dark:text-white mb-4">حالت شب و روز</h3>
    <div class="grid grid-cols-2 gap-3">
        <button 
            wire:click="updateTheme('light')"
            class="px-6 py-4 rounded-lg border-2 transition-all duration-200 {{ $theme === 'light' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300 bg-white hover:border-indigo-400' }}"
        >
            <div class="flex items-center justify-center gap-3">
                <svg class="w-6 h-6 {{ $theme === 'light' ? 'text-indigo-600' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <div>
                    <div class="font-semibold {{ $theme === 'light' ? 'text-indigo-700' : 'text-gray-900' }}">روشن</div>
                    <div class="text-xs text-gray-500">Light Mode</div>
                </div>
            </div>
        </button>
        
        <button 
            wire:click="updateTheme('dark')"
            class="px-6 py-4 rounded-lg border-2 transition-all duration-200 {{ $theme === 'dark' ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300 bg-white hover:border-indigo-400' }}"
        >
            <div class="flex items-center justify-center gap-3">
                <svg class="w-6 h-6 {{ $theme === 'dark' ? 'text-indigo-600' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
                <div>
                    <div class="font-semibold {{ $theme === 'dark' ? 'text-indigo-700' : 'text-gray-900' }}">تاریک</div>
                    <div class="text-xs text-gray-500">Dark Mode</div>
                </div>
            </div>
        </button>
    </div>
</div>
