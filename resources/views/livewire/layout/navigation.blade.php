<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false, searchOpen: false, searchQuery: '' }" class="bg-gray-50/95 dark:bg-gray-100/95 border-b border-gray-200 backdrop-blur supports-[backdrop-filter]:bg-gray-50/60 shadow-sm z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-4xl mx-auto px-4">
        <div class="flex justify-between items-center h-16 relative">
            <!-- Empty left space for balance -->
            <div class="w-10"></div>
            
            <!-- Logo - Centered -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2">
                    @php
                        $logo = \App\Helpers\BrandingHelper::getLogo();
                    @endphp
                    @if($logo)
                        <img src="{{ $logo }}" alt="{{ config('app.name') }}" class="h-8 w-auto">
                    @else
                        <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ config('app.name', 'allexam24') }}</span>
                    @endif
                </a>
            </div>
            
            <!-- Search Icon - Right -->
            <div class="relative">
                <button 
                    @click="searchOpen = !searchOpen; if(searchOpen) $nextTick(() => $refs.searchInput.focus())"
                    class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-gray-100 rounded-full transition-colors"
                    :class="{ 'bg-indigo-100 text-indigo-600': searchOpen }"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Expandable Search Box -->
    <div 
        x-show="searchOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        @click.away="searchOpen = false"
        @keydown.escape.window="searchOpen = false"
        class="absolute left-0 right-0 bg-white shadow-lg border-b border-gray-200 z-50"
    >
        <div class="max-w-4xl mx-auto px-4 py-4">
            <form action="{{ route('search') }}" method="GET" class="relative">
                <input 
                    type="text" 
                    name="q"
                    x-ref="searchInput"
                    x-model="searchQuery"
                    placeholder="Search exams..." 
                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 pl-11 pr-4 text-base"
                    @keydown.enter="if(searchQuery.length >= 2) $el.form.submit()"
                >
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
                <button 
                    type="submit" 
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-indigo-600 hover:text-indigo-700"
                    :class="{ 'opacity-50 cursor-not-allowed': searchQuery.length < 2 }"
                    :disabled="searchQuery.length < 2"
                >
                    <span class="text-sm font-medium">Search</span>
                </button>
            </form>
            <p class="text-xs text-gray-500 mt-2">Enter at least 2 characters to search for exams</p>
        </div>
    </div>
</nav>
