@php
    $settings = \App\Models\SystemSetting::first();
    $content = $settings?->terms_content;
@endphp

@component('layouts.app', ['seoTitle' => 'قوانین و مقررات'])
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200" dir="rtl">
                    <h1 class="text-3xl font-bold mb-8 text-center text-gray-800">قوانین و مقررات</h1>
                    
                    <div class="prose max-w-none text-gray-700 leading-relaxed">
                        @if($content)
                            {!! $content !!}
                        @else
                            <h2 class="text-xl font-bold mt-6 mb-4 text-indigo-700">۱. مقدمه</h2>
                            <p class="mb-4">
                                کاربر گرامی، ضمن سپاس از انتخاب سرویس ما، خواهشمندیم قوانین و مقررات زیر را به دقت مطالعه فرمایید. استفاده شما از وب‌سایت و خدمات ما به منزله پذیرش کامل این قوانین است.
                            </p>

                            <h2 class="text-xl font-bold mt-6 mb-4 text-indigo-700">۲. حساب کاربری</h2>
                            <p class="mb-4">
                                برای استفاده از خدمات سایت، کاربران باید با اطلاعات صحیح اقدام به ثبت‌نام نمایند. مسئولیت حفظ امنیت اطلاعات حساب کاربری بر عهده کاربر است.
                            </p>
                            
                            <h2 class="text-xl font-bold mt-6 mb-4 text-indigo-700">۳. مالکیت معنوی</h2>
                            <p class="mb-4">
                                تمامی محتوای موجود در سایت شامل متن، تصاویر، سوالات و آزمون‌ها متعلق به این وب‌سایت بوده و هرگونه کپی‌برداری بدون کسب مجوز کتبی پیگرد قانونی دارد.
                            </p>

                            <h2 class="text-xl font-bold mt-6 mb-4 text-indigo-700">۴. حریم خصوصی</h2>
                            <p class="mb-4">
                                ما متعهد به حفظ حریم خصوصی کاربران هستیم و اطلاعات شخصی شما نزد ما محفوظ خواهد ماند و در اختیار اشخاص ثالث قرار نخواهد گرفت.
                            </p>

                            <h2 class="text-xl font-bold mt-6 mb-4 text-indigo-700">۵. تغییرات در قوانین</h2>
                            <p class="mb-4">
                                این وب‌سایت حق دارد در هر زمان قوانین و مقررات را تغییر دهد. تغییرات جدید از طریق همین صفحه به اطلاع کاربران خواهد رسید.
                            </p>
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
