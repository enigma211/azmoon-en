<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}
        
        <div class="mt-4 flex justify-end">
            <x-filament::button type="submit">
                Save Settings
            </x-filament::button>
        </div>
    </form>

    <hr class="my-8 border-gray-200 dark:border-gray-700">

    <div class="space-y-6" dir="ltr">
        @if(session('success'))
            <div class="fi-alert fi-color-success">
                <div class="fi-alert-content">
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="fi-alert fi-color-danger">
                <div class="fi-alert-content">
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
