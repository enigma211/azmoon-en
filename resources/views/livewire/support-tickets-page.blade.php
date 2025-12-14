<div class="max-w-4xl mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">پشتیبانی</h1>
        <p class="text-sm text-gray-600">تیکت‌های پشتیبانی خود را مدیریت کنید</p>
    </div>

    <!-- Success/Error Messages -->
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-50 border-r-4 border-green-400 rounded-lg">
            <p class="text-sm text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 border-r-4 border-red-400 rounded-lg">
            <p class="text-sm text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Create Ticket Button -->
    <div class="mb-6">
        <button 
            wire:click="toggleCreateForm"
            class="w-full sm:w-auto px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-semibold"
        >
            @if($showCreateForm)
                <span>✕ بستن فرم</span>
            @else
                <span>+ ایجاد تیکت جدید</span>
            @endif
        </button>
    </div>

    <!-- Create Ticket Form -->
    @if($showCreateForm)
        <div class="mb-6 bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">ایجاد تیکت جدید</h2>
            
            <form wire:submit.prevent="createTicket">
                <!-- Subject -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        موضوع تیکت <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        wire:model="subject"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="موضوع تیکت خود را وارد کنید"
                    >
                    @error('subject') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Message -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        متن تیکت <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        wire:model="message"
                        rows="6"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="توضیحات خود را بنویسید..."
                    ></textarea>
                    @error('message') 
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">حداکثر 2000 کاراکتر</p>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-3">
                    <button 
                        type="submit"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold"
                    >
                        ارسال تیکت
                    </button>
                    <button 
                        type="button"
                        wire:click="toggleCreateForm"
                        class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors"
                    >
                        انصراف
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Ticket Detail Modal -->
    @if($selectedTicket)
        <div class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $selectedTicket->subject }}</h2>
                            <p class="text-sm text-gray-500 mt-1">شماره تیکت: {{ $selectedTicket->ticket_number }}</p>
                        </div>
                        <button 
                            wire:click="closeTicketView"
                            class="text-gray-400 hover:text-gray-600"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Status Badge -->
                    <div class="mb-4">
                        @if($selectedTicket->isPending())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                در انتظار پاسخ
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                پاسخ داده شده
                            </span>
                        @endif
                        <span class="text-sm text-gray-500 mr-3">
                            {{ $selectedTicket->created_at->format('Y/m/d H:i') }}
                        </span>
                    </div>

                    <!-- Conversation Thread -->
                    <div class="mb-6 space-y-4 max-h-96 overflow-y-auto">
                        <!-- Initial Message -->
                        <div class="bg-blue-50 rounded-lg p-4 border-r-4 border-blue-500">
                            <div class="flex items-start justify-between mb-2">
                                <span class="text-xs font-semibold text-blue-800">شما</span>
                                <span class="text-xs text-gray-500">{{ $selectedTicket->created_at->format('Y/m/d H:i') }}</span>
                            </div>
                            <p class="text-gray-800 whitespace-pre-wrap">{{ $selectedTicket->message }}</p>
                        </div>

                        <!-- Replies -->
                        @foreach($selectedTicket->replies as $reply)
                            @if($reply->is_admin)
                                <!-- Admin Reply -->
                                <div class="bg-green-50 rounded-lg p-4 border-r-4 border-green-500">
                                    <div class="flex items-start justify-between mb-2">
                                        <span class="text-xs font-semibold text-green-800">پشتیبانی</span>
                                        <span class="text-xs text-gray-500">{{ $reply->created_at->format('Y/m/d H:i') }}</span>
                                    </div>
                                    <p class="text-gray-800 whitespace-pre-wrap">{{ $reply->message }}</p>
                                </div>
                            @else
                                <!-- User Reply -->
                                <div class="bg-blue-50 rounded-lg p-4 border-r-4 border-blue-500">
                                    <div class="flex items-start justify-between mb-2">
                                        <span class="text-xs font-semibold text-blue-800">شما</span>
                                        <span class="text-xs text-gray-500">{{ $reply->created_at->format('Y/m/d H:i') }}</span>
                                    </div>
                                    <p class="text-gray-800 whitespace-pre-wrap">{{ $reply->message }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Reply Success Message -->
                    @if (session()->has('reply_success'))
                        <div class="mb-4 p-3 bg-green-50 border-r-4 border-green-400 rounded-lg">
                            <p class="text-sm text-green-800">{{ session('reply_success') }}</p>
                        </div>
                    @endif

                    <!-- Reply Form -->
                    <div class="mb-4 border-t pt-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">پاسخ شما:</h3>
                        <form wire:submit.prevent="sendReply">
                            <textarea 
                                wire:model="replyMessage"
                                rows="4"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="پاسخ خود را بنویسید..."
                            ></textarea>
                            @error('replyMessage') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <div class="mt-3 flex gap-2">
                                <button 
                                    type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium"
                                >
                                    ارسال پاسخ
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Close Button -->
                    <div class="mt-6">
                        <button 
                            wire:click="closeTicketView"
                            class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
                        >
                            بستن
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Tickets List -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">تاریخچه تیکت‌ها</h2>
        </div>

        @if($tickets->isEmpty())
            <div class="p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
                <p class="mt-4 text-gray-500">هنوز تیکتی ثبت نکرده‌اید</p>
            </div>
        @else
            <div class="divide-y divide-gray-200">
                @foreach($tickets as $ticket)
                    <div 
                        wire:click="viewTicket({{ $ticket->id }})"
                        class="p-4 hover:bg-gray-50 cursor-pointer transition-colors"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="font-semibold text-gray-900">{{ $ticket->subject }}</h3>
                                    @if($ticket->isPending())
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            در انتظار پاسخ
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            پاسخ داده شده
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 mb-2">شماره تیکت: {{ $ticket->ticket_number }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $ticket->created_at->format('Y/m/d H:i') }}
                                </p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
