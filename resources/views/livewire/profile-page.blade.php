<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session()->has('warning'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="mr-3">
                        <p class="text-sm text-red-700 font-bold">
                            {{ session('warning') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('message'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="mr-3">
                        <p class="text-sm text-green-700 font-bold">
                            {{ session('message') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if ($isGuest)
            {{-- Guest User: Show Login Form --}}
            <header class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-gray-900">User Profile</h1>
                <p class="text-sm text-gray-600 mt-2">To access your profile, please login with your email and password</p>
            </header>

            <div class="flex justify-center mt-4">
                <a href="{{ route('login') }}"
                    class="inline-flex items-center rounded-lg bg-indigo-600 px-6 py-2.5 text-white text-sm font-medium hover:bg-indigo-700 transition shadow-md hover:shadow-lg">
                    Login with Email
                </a>
            </div>
        @else
            {{-- Logged In User: Show Profile --}}
            <header class="mb-8">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg p-4 text-white">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h1 class="text-xl font-bold mb-1">
                                Welcome, {{ auth()->user()->name }}!
                            </h1>
                            <p class="text-indigo-100 text-sm">
                                Subscription Status:
                                @if($isPremium)
                                    <span class="font-semibold">Premium</span>
                                    @if($daysRemaining !== null && $daysRemaining > 0)
                                        <span class="text-xs"> ({{ ceil($daysRemaining) }} days remaining)</span>
                                    @endif
                                @else
                                    <span class="font-semibold">Free User</span>
                                @endif
                            </p>
                        </div>
                        <button wire:click="logout"
                            class="px-3 py-1.5 bg-white/20 hover:bg-white/30 rounded-lg text-white text-xs font-medium transition-colors backdrop-blur">
                            Logout
                        </button>
                    </div>
                </div>

                <!-- <div class="mt-4 flex justify-center gap-3">
                        <a href="{{ route('attempts') }}" wire:navigate class="inline-flex items-center rounded-lg bg-indigo-600 px-5 py-2.5 text-white text-sm font-medium hover:bg-indigo-700 transition shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Exam History & Grades
                        </a>
                    </div> -->
            </header>


            <!-- Spacer -->
            <div class="h-8"></div>

            <!-- Support Tickets Link -->
            <button onclick="window.location.href='{{ route('support-tickets') }}'"
                class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg shadow-lg p-6 transition-all duration-200 hover:shadow-xl cursor-pointer">
                <div class="flex items-center justify-center gap-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <div class="text-center">
                        <h3 class="text-xl font-bold">Support</h3>
                        <p class="text-sm text-blue-100 mt-1">Submit tickets and view responses</p>
                    </div>
                </div>
            </button>

            <!-- Spacer -->
            <div class="h-6"></div>
        @endif

        <!-- Information & Legal Links -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 divide-y divide-gray-100">
            <a href="{{ route('about') }}"
                class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors group">
                <div class="flex items-center gap-3">
                    <div
                        class="p-2 bg-indigo-50 text-indigo-600 rounded-lg group-hover:bg-indigo-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="font-medium text-gray-700 group-hover:text-gray-900">About Us</span>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <a href="{{ route('terms') }}"
                class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors group">
                <div class="flex items-center gap-3">
                    <div
                        class="p-2 bg-indigo-50 text-indigo-600 rounded-lg group-hover:bg-indigo-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span class="font-medium text-gray-700 group-hover:text-gray-900">Terms and Conditions</span>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <a href="{{ route('privacy-policy') }}"
                class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors group">
                <div class="flex items-center gap-3">
                    <div
                        class="p-2 bg-indigo-50 text-indigo-600 rounded-lg group-hover:bg-indigo-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <span class="font-medium text-gray-700 group-hover:text-gray-900">Privacy Policy</span>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        <!-- Spacer -->
        <div class="h-8"></div>


        <!-- Old Subscription Card (Hidden, keeping for reference) -->
        <div class="hidden bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Subscription Status (Legacy)</h2>

            @if($subscription)
                <div class="space-y-4">
                    <!-- Plan Name -->
                    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                        <span class="text-gray-600 dark:text-gray-400">Plan:</span>
                        <span
                            class="font-semibold text-lg {{ $subscription->price > 0 ? 'text-green-600' : 'text-gray-600' }}">
                            {{ $subscription->title }}
                        </span>
                    </div>

                    <!-- Start Date -->
                    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                        <span class="text-gray-600 dark:text-gray-400">Start Date:</span>
                        <span
                            class="font-medium">{{ auth()->user()->subscription_start ? \Carbon\Carbon::parse(auth()->user()->subscription_start)->format('Y/m/d') : '-' }}</span>
                    </div>

                    <!-- End Date -->
                    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                        <span class="text-gray-600 dark:text-gray-400">End Date:</span>
                        <span class="font-medium">
                            @if(auth()->user()->subscription_end)
                                {{ \Carbon\Carbon::parse(auth()->user()->subscription_end)->format('Y/m/d') }}
                            @else
                                <span class="text-green-600">Unlimited</span>
                            @endif
                        </span>
                    </div>

                    <!-- Days Remaining -->
                    @if($daysRemaining !== null)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Days Remaining:</span>
                            <span
                                class="font-bold text-lg {{ $isExpired ? 'text-red-600' : ($daysRemaining <= 7 ? 'text-yellow-600' : 'text-green-600') }}">
                                @if($isExpired)
                                    Expired
                                @else
                                    {{ $daysRemaining }} days
                                @endif
                            </span>
                        </div>
                    @else
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Validity:</span>
                            <span class="font-bold text-lg text-green-600">Unlimited</span>
                        </div>
                    @endif

                    <!-- Expired Warning -->
                    @if($isExpired)
                        <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                            <p class="text-red-800 dark:text-red-200 font-medium">
                                ⚠️ Your subscription has expired. Please renew to access premium features.
                            </p>
                        </div>
                    @endif
                </div>
            @else
                <p class="text-gray-600 dark:text-gray-400">No subscription info found.</p>
            @endif
        </div>



        @if($isGuest)
            <div class="mt-12 bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        Send us your Feedback
                    </h3>
                    <form wire:submit.prevent="submitGuestFeedback" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Name (Optional)</label>
                                <input type="text" wire:model="guest_name" class="w-full rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Your Name">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" wire:model="guest_email" class="w-full rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="email@example.com">
                                @error('guest_email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        {{-- Honeypot --}}
                        <div class="hidden">
                            <input type="text" wire:model="honeypot">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Message <span class="text-red-500">*</span></label>
                            <textarea wire:model="guest_message" rows="4" class="w-full rounded-lg border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Write your feedback or questions here..."></textarea>
                            @error('guest_message') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-6 rounded-lg transition-colors shadow-sm text-sm">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</div>