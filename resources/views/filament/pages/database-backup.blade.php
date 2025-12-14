<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Info Card -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-100">درباره پشتیبان‌گیری</h3>
                    <p class="text-sm text-blue-800 dark:text-blue-200 mt-1">
                        با استفاده از این بخش می‌توانید از دیتابیس خود بک‌آپ بگیرید. فایل‌های بک‌آپ در فرمت SQL ذخیره می‌شوند.
                        توصیه می‌شود به صورت منظم از دیتابیس خود بک‌آپ بگیرید.
                    </p>
                </div>
            </div>
        </div>

        <!-- Backups List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">لیست بک‌آپ‌ها</h2>
            </div>

            @php
                $backups = $this->getBackups();
            @endphp

            @if(count($backups) > 0)
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($backups as $backup)
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ $backup['name'] }}</h3>
                                    <div class="flex items-center gap-4 mt-1">
                                        <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $backup['date'] }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ $backup['size'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button 
                                    wire:click="downloadBackup('{{ $backup['path'] }}')"
                                    class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-blue-700 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 rounded-lg transition"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    دانلود
                                </button>
                                <button 
                                    wire:click="deleteBackup('{{ $backup['path'] }}')"
                                    wire:confirm="آیا مطمئن هستید که می‌خواهید این بک‌آپ را حذف کنید؟"
                                    class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-red-700 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 rounded-lg transition"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    حذف
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-white">هیچ بک‌آپی وجود ندارد</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">برای ایجاد اولین بک‌آپ، روی دکمه "ایجاد بک‌آپ جدید" کلیک کنید.</p>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
