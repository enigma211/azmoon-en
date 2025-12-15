@php
    $settings = \App\Models\SystemSetting::first();
    $content = $settings?->terms_content;
@endphp

@component('layouts.app', ['seoTitle' => 'Terms of Use - AllExam24'])
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 bg-white border-b border-gray-200 space-y-10 text-gray-800" dir="ltr">
                    <div class="text-center space-y-2">
                        <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">Terms of Use</p>
                        <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900">AllExam24 Legal Notice</h1>
                        <p class="text-sm text-gray-500">Last Updated: December 2025</p>
                    </div>

                    <section class="space-y-4">
                        <h2 class="text-2xl font-bold text-gray-900">1. Acceptance of Terms</h2>
                        <p>
                            Welcome to AllExam24.com. By accessing or using our website, you agree to comply with and be bound by these Terms and Conditions.
                            If you do not agree with any part of these terms, please do not use our website.
                        </p>
                    </section>

                    <section class="space-y-4">
                        <h2 class="text-2xl font-bold text-gray-900">2. Educational Purpose Only (Disclaimer)</h2>
                        <ul class="list-disc list-inside space-y-3">
                            <li>
                                <strong>Not Official Advice:</strong> We are a private third-party resource and are not affiliated with the Department of Motor Vehicles (DMV),
                                any government agency, or official testing body.
                            </li>
                            <li>
                                <strong>No Guarantee:</strong> While we strive to keep our practice tests and study guides accurate and up-to-date, we make no representations
                                or warranties of any kind regarding the completeness or accuracy of the information. Passing our practice tests does not guarantee that you will pass
                                the official certification exam. Use this site as a study aid, not as the sole source of information.
                            </li>
                        </ul>
                    </section>

                    <section class="space-y-4">
                        <h2 class="text-2xl font-bold text-gray-900">3. Intellectual Property</h2>
                        <p>
                            All content on this website, including text, graphics, logos, question banks, and software, is the property of AllExam24 or its content suppliers
                            and is protected by international copyright laws. You may not scrape, copy, reproduce, or distribute our content without our written permission.
                        </p>
                    </section>

                    <section class="space-y-4">
                        <h2 class="text-2xl font-bold text-gray-900">4. Third-Party Links and Ads</h2>
                        <p>
                            Our website may contain links to third-party websites or advertisements (such as Google AdSense or Ezoic). We do not control these external sites
                            and are not responsible for their content or privacy practices. Clicking on these links is at your own risk.
                        </p>
                    </section>

                    <section class="space-y-4">
                        <h2 class="text-2xl font-bold text-gray-900">5. Limitation of Liability</h2>
                        <p>
                            In no event shall AllExam24 be liable for any direct, indirect, incidental, or consequential damages arising out of the use or inability to use our website.
                            We are not responsible for any errors, omissions, or the results obtained from the use of this information.
                        </p>
                    </section>

                    <section class="space-y-4">
                        <h2 class="text-2xl font-bold text-gray-900">6. Changes to Terms</h2>
                        <p>
                            We reserve the right to modify these terms at any time. Your continued use of the website after any changes indicates your acceptance of the new terms.
                        </p>
                    </section>

                    <section class="space-y-4">
                        <h2 class="text-2xl font-bold text-gray-900">7. Contact Information</h2>
                        <p>
                            If you have any questions about these Terms, please contact us at:
                            <a href="mailto:support@allexam24.com" class="text-indigo-600 hover:text-indigo-800">support@allexam24.com</a>
                        </p>
                    </section>

                    <div class="pt-6 border-t border-gray-200">
                        <a href="{{ route('home') }}" class="inline-flex items-center px-5 py-3 bg-gray-900 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                            Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endcomponent
