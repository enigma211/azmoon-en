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

<nav x-data="{ open: false }" class="bg-gray-50/95 dark:bg-gray-100/95 border-b border-gray-200 backdrop-blur supports-[backdrop-filter]:bg-gray-50/60 shadow-sm z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-4xl mx-auto px-4">
        <div class="flex justify-center items-center h-16 relative">
            <!-- Logo - Centered -->
            <div class="absolute left-1/2 transform -translate-x-1/2 flex items-center">
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2">
                    @php
                        $logo = \App\Helpers\BrandingHelper::getLogo();
                    @endphp
                    @if($logo)
                        <img src="{{ $logo }}" alt="{{ config('app.name') }}" class="h-8 w-auto">
                    @else
                        <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ config('app.name', 'آزمون کده') }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>

    
</nav>
