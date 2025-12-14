@php
    $settings = \App\Models\SystemSetting::first();
    $content = $settings?->terms_content;
@endphp

@component('layouts.app', ['seoTitle' => 'Terms and Conditions'])
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200" dir="ltr">
                    <h1 class="text-3xl font-bold mb-8 text-center text-gray-800">Terms and Conditions</h1>
                    
                    <div class="prose max-w-none text-gray-700 leading-relaxed">
                        @if($content)
                            {!! $content !!}
                        @else
                            <h2 class="text-xl font-bold mt-6 mb-4 text-indigo-700">1. Introduction</h2>
                            <p class="mb-4">
                                Dear user, thank you for choosing our service. Please read the following terms and conditions carefully. Your use of our website and services constitutes your full acceptance of these terms.
                            </p>

                            <h2 class="text-xl font-bold mt-6 mb-4 text-indigo-700">2. User Account</h2>
                            <p class="mb-4">
                                To use the site's services, users must register with correct information. The responsibility for maintaining the security of account information lies with the user.
                            </p>
                            
                            <h2 class="text-xl font-bold mt-6 mb-4 text-indigo-700">3. Intellectual Property</h2>
                            <p class="mb-4">
                                All content on the site, including text, images, questions, and exams, belongs to this website, and any copying without written permission is subject to legal action.
                            </p>

                            <h2 class="text-xl font-bold mt-6 mb-4 text-indigo-700">4. Privacy Policy</h2>
                            <p class="mb-4">
                                We are committed to protecting user privacy. Your personal information will remain confidential with us and will not be shared with third parties.
                            </p>

                            <h2 class="text-xl font-bold mt-6 mb-4 text-indigo-700">5. Changes to Terms</h2>
                            <p class="mb-4">
                                This website reserves the right to change the terms and conditions at any time. Changes will be communicated to users through this page.
                            </p>
                        @endif
                        
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endcomponent
