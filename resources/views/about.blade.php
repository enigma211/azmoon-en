@component('layouts.app', ['seoTitle' => 'About AllExam24'])
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 bg-white border-b border-gray-200 space-y-10 text-gray-800" dir="ltr">
                    <div class="text-center space-y-4">
                        <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">About AllExam24</p>
                        <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900">Empowering Every Exam Journey</h1>
                    </div>

                    <section class="space-y-4">
                        <h2 class="text-2xl font-bold text-gray-900">Who We Are</h2>
                        <p>
                            At AllExam24, we believe that quality education and exam preparation should be accessible to everyone, everywhere.
                            We are a premier online platform dedicated to helping students, professionals, and job seekers pass their certification exams on the first try.
                        </p>
                        <p>
                            Whether you are aspiring to become a commercial driver (CDL), a nurse, a skilled tradesperson, or a real estate agent,
                            AllExam24 provides the tools you need to succeed.
                        </p>
                    </section>

                    <section class="space-y-4">
                        <h2 class="text-2xl font-bold text-gray-900">Our Mission</h2>
                        <p>
                            Our mission is simple: To bridge the gap between learning and passing. We understand that official handbooks can be dry,
                            complicated, and overwhelming. That’s why we have transformed complex regulations and textbook theories into interactive,
                            easy-to-digest practice tests. We aim to empower our users with confidence, turning exam anxiety into readiness.
                        </p>
                    </section>

                    <section class="space-y-4">
                        <h2 class="text-2xl font-bold text-gray-900">Why Choose AllExam24?</h2>
                        <ul class="list-disc list-inside space-y-2">
                            <li><strong>State-Specific Accuracy:</strong> Our CDL and trade practice tests are tailored to each US state's regulations.</li>
                            <li><strong>Always Free:</strong> Core practice tests are 100% free—no hidden paywalls or subscriptions.</li>
                            <li><strong>Simulation Mode:</strong> Real exam environments so you get comfortable with timing and format before test day.</li>
                            <li><strong>Instant Feedback:</strong> Detailed explanations for every question help you learn from mistakes immediately.</li>
                        </ul>
                    </section>

                    <section class="space-y-4">
                        <h2 class="text-2xl font-bold text-gray-900">Our Content &amp; Quality Standards</h2>
                        <p>
                            Accuracy is our top priority. Our question bank is meticulously curated based on the latest official handbooks
                            (such as the CDL Manuals from the DMV/BMV) and industry standards. We regularly update our database to reflect changes
                            in laws and exam formats for 2024 and beyond.
                        </p>
                    </section>

                    <section class="space-y-4">
                        <h2 class="text-2xl font-bold text-gray-900">Disclaimer</h2>
                        <p>
                            AllExam24 is an independent educational platform and is not affiliated with, endorsed by, or connected to any state DMV,
                            government agency, or official testing organization. All trademarks and trade names are the property of their respective owners.
                        </p>
                    </section>

                    <section class="space-y-4">
                        <h2 class="text-2xl font-bold text-gray-900">Contact Us</h2>
                        <p>
                            Have a suggestion, found an error, or just want to say hello? We’d love to hear from you.
                        </p>
                        <p class="text-lg font-semibold">
                            📧 Email: <a href="mailto:support@allexam24.com" class="text-indigo-600 hover:text-indigo-800">support@allexam24.com</a>
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
