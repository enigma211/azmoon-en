@php
    $settings = \App\Models\SystemSetting::first();
    $content = $settings?->about_content;
@endphp

@component('layouts.app', ['seoTitle' => 'درباره ما'])
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200" dir="rtl">
                    <h1 class="text-3xl font-bold mb-8 text-center text-gray-800">درباره ما</h1>
                    
                    <div class="prose max-w-none text-gray-700 leading-relaxed text-center">
                        @if($content)
                            {!! $content !!}
                        @else
                            <div class="mb-8">
                                <h2 class="text-xl font-bold mb-4 text-indigo-700">راه های ارتباطی</h2>
                                
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <span class="font-bold text-gray-900">شماره تماس:</span>
                                        <a href="tel:09362196030" class="text-indigo-600 hover:text-indigo-800 text-lg" dir="ltr">09362196030</a>
                                    </div>

                                    <div class="flex flex-col items-center mt-4">
                                        <span class="font-bold text-gray-900 mb-2">آدرس:</span>
                                        <p class="text-center max-w-md">
                                            تهران - ابوذر - خیابان شهید حجت اله شهرابی خیابان پرستار جنوبی - بهاران بلوک 7 ورودی 2 - پلاک : 2.0
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                بازگشت به صفحه اصلی
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endcomponent
