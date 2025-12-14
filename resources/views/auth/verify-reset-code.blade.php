<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Enter the 6-digit code we emailed to you, then you will be able to choose a new password.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.code.verify') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                value="{{ old('email', request('email')) }}"
                required
                autofocus
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- 6-digit Code -->
        <div class="mt-4">
            <x-input-label for="code" value="6-digit Code" />
            <x-text-input
                id="code"
                class="block mt-1 w-full"
                type="text"
                name="code"
                value="{{ old('code') }}"
                required
                maxlength="6"
                pattern="\d{6}"
            />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Verify Code') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
