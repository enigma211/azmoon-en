<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->table }}
        
        <x-filament::section>
            <x-slot name="heading">
                راهنمای فرمت فایل‌های CSV
            </x-slot>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Questions Guide -->
                <div class="prose dark:prose-invert max-w-none">
                    <h3 class="text-lg font-bold text-primary-600 dark:text-primary-400 mb-4 border-b pb-2">
                        ۱. راهنمای فایل سوالات
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        فایل CSV سوالات باید دارای <strong>۷ ستون</strong> به ترتیب زیر باشد:
                    </p>
                    <ol class="text-sm text-gray-600 dark:text-gray-400 list-decimal mr-5 space-y-1">
                        <li><strong>شماره سوال:</strong> عدد صحیح (مثال: 1)</li>
                        <li><strong>متن سوال:</strong> متن کامل سوال</li>
                        <li><strong>گزینه 1:</strong> متن گزینه اول</li>
                        <li><strong>گزینه 2:</strong> متن گزینه دوم</li>
                        <li><strong>گزینه 3:</strong> متن گزینه سوم</li>
                        <li><strong>گزینه 4:</strong> متن گزینه چهارم</li>
                        <li><strong>شماره گزینه صحیح:</strong> عدد بین 1 تا 4</li>
                    </ol>
                    
                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                        <p class="text-sm font-bold text-blue-800 dark:text-blue-200 mb-2">مثال فایل سوالات:</p>
                        <code class="text-xs text-blue-700 dark:text-blue-300 block whitespace-pre font-mono bg-white dark:bg-black/20 p-2 rounded dir-ltr text-left">1,"پایتخت ایران کدام است؟","تهران","اصفهان","شیراز","مشهد",1
2,"HTML چیست؟","زبان برنامه نویسی","زبان نشانه گذاری","پایگاه داده","سیستم عامل",2</code>
                    </div>
                </div>

                <!-- Explanations Guide -->
                <div class="prose dark:prose-invert max-w-none">
                    <h3 class="text-lg font-bold text-success-600 dark:text-success-400 mb-4 border-b pb-2">
                        ۲. راهنمای فایل پاسخ‌های تشریحی
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        فایل CSV پاسخ‌های تشریحی باید دارای <strong>۲ ستون</strong> به ترتیب زیر باشد:
                    </p>
                    <ol class="text-sm text-gray-600 dark:text-gray-400 list-decimal mr-5 space-y-1">
                        <li><strong>شماره سوال:</strong> شماره سوالی که پاسخ مربوط به آن است</li>
                        <li><strong>متن پاسخ تشریحی:</strong> متن کامل توضیح پاسخ</li>
                    </ol>
                    
                    <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-100 dark:border-green-800">
                        <p class="text-sm font-bold text-green-800 dark:text-green-200 mb-2">مثال فایل پاسخ تشریحی:</p>
                        <code class="text-xs text-green-700 dark:text-green-300 block whitespace-pre font-mono bg-white dark:bg-black/20 p-2 rounded dir-ltr text-left">1,"تهران پایتخت سیاسی و اداری ایران است."
2,"HTML مخفف HyperText Markup Language به معنی زبان نشانه‌گذاری ابرمتن است."</code>
                    </div>

                    <div class="mt-4 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-100 dark:border-amber-800">
                        <p class="text-sm font-bold text-amber-800 dark:text-amber-200 mb-1">نکته مهم:</p>
                        <p class="text-xs text-amber-700 dark:text-amber-300 leading-relaxed">
                            سیستم بر اساس <strong>«شماره سوال»</strong> در آزمون انتخاب شده، پاسخ را پیدا می‌کند.
                            <br>
                            مثلاً اگر در فایل پاسخ تشریحی بنویسید <code>5,"توضیحات..."</code>، سیستم به دنبال سوالی در آزمون جاری می‌گردد که شماره آن ۵ باشد و پاسخ را برای آن ذخیره می‌کند.
                        </p>
                    </div>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
