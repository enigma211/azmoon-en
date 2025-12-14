<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
    <meta name="user-authenticated" content="true">
    @endauth
    <title>تست Push Notifications - آزمون کده</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css'])
    
    <style>
        body { font-family: Vazirmatn, system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-50 to-white p-4">
    <div class="max-w-2xl mx-auto py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">🔔 تست Push Notifications</h1>
            <p class="text-gray-600">مدیریت اعلان‌های فشاری</p>
        </div>

        <!-- Status Card -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border-2" id="status-card">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">وضعیت اعلان‌ها</h3>
                    <p class="text-sm text-gray-500" id="status-text">در حال بررسی...</p>
                </div>
            </div>
            <div class="space-y-2" id="status-details"></div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h3 class="font-bold text-gray-900 mb-4">🎮 اقدامات</h3>
            <div class="space-y-3">
                <button 
                    id="btn-request-permission" 
                    class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    درخواست اجازه
                </button>
                
                <button 
                    id="btn-subscribe" 
                    class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    فعال کردن اعلان‌ها
                </button>
                
                <button 
                    id="btn-unsubscribe" 
                    class="w-full bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    غیرفعال کردن اعلان‌ها
                </button>
                
                <button 
                    id="btn-send-test" 
                    class="w-full bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    ارسال اعلان تستی
                </button>
                
                <button 
                    onclick="window.location.href='/'" 
                    class="w-full bg-gray-600 text-white py-3 px-4 rounded-lg hover:bg-gray-700 transition-colors"
                >
                    بازگشت به صفحه اصلی
                </button>
            </div>
        </div>

        <!-- Info -->
        <div class="bg-blue-50 rounded-xl p-6 border border-blue-200">
            <h3 class="font-bold text-blue-900 mb-3">ℹ️ راهنما</h3>
            <ul class="text-sm text-blue-800 space-y-2">
                <li class="flex items-start gap-2">
                    <span class="text-blue-600 mt-0.5">•</span>
                    <span>برای دریافت اعلان‌ها، ابتدا اجازه دهید</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-blue-600 mt-0.5">•</span>
                    <span>بعد از فعال کردن، می‌توانید اعلان تستی ارسال کنید</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-blue-600 mt-0.5">•</span>
                    <span>اعلان‌ها حتی وقتی اپ بسته است کار می‌کنند</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-blue-600 mt-0.5">•</span>
                    <span>برای تست، باید لاگین باشید</span>
                </li>
            </ul>
        </div>

        <!-- Console Log -->
        <div class="mt-6 bg-gray-900 rounded-xl shadow-lg p-6 text-white">
            <h3 class="font-bold mb-4">📋 Console Log</h3>
            <div id="console-log" class="text-xs font-mono space-y-1 max-h-64 overflow-y-auto"></div>
        </div>
    </div>

    <script src="/js/push-notifications.js"></script>
    <script>
        const log = (msg, type = 'info') => {
            const colors = {
                info: 'text-blue-400',
                success: 'text-green-400',
                error: 'text-red-400',
                warning: 'text-yellow-400'
            };
            const logDiv = document.getElementById('console-log');
            const time = new Date().toLocaleTimeString('fa-IR');
            logDiv.innerHTML += `<div class="${colors[type]}">[${time}] ${msg}</div>`;
            logDiv.scrollTop = logDiv.scrollHeight;
            console.log(msg);
        };

        // بررسی وضعیت
        async function checkStatus() {
            const status = await window.pushManager.getSubscriptionStatus();
            
            const statusCard = document.getElementById('status-card');
            const statusText = document.getElementById('status-text');
            const statusDetails = document.getElementById('status-details');
            
            if (!window.pushManager.isSupported()) {
                statusCard.classList.add('border-red-500');
                statusText.textContent = '❌ پشتیبانی نمی‌شود';
                statusDetails.innerHTML = '<div class="text-sm text-red-600">مرورگر شما از Push Notifications پشتیبانی نمی‌کند</div>';
                log('مرورگر از Push Notifications پشتیبانی نمی‌کند', 'error');
                return;
            }
            
            if (status.permission === 'denied') {
                statusCard.classList.add('border-red-500');
                statusText.textContent = '🚫 اجازه داده نشده';
                statusDetails.innerHTML = '<div class="text-sm text-red-600">شما اجازه دریافت اعلان را رد کرده‌اید. برای فعال کردن، تنظیمات مرورگر را بررسی کنید.</div>';
                log('اجازه دریافت اعلان رد شده است', 'error');
            } else if (status.permission === 'granted' && status.subscribed) {
                statusCard.classList.add('border-green-500');
                statusText.textContent = '✅ فعال';
                statusDetails.innerHTML = '<div class="text-sm text-green-600">اعلان‌ها فعال هستند و آماده دریافت</div>';
                log('اعلان‌ها فعال هستند', 'success');
            } else if (status.permission === 'granted' && !status.subscribed) {
                statusCard.classList.add('border-yellow-500');
                statusText.textContent = '⚠️ اجازه داده شده اما غیرفعال';
                statusDetails.innerHTML = '<div class="text-sm text-yellow-600">اجازه داده شده اما اشتراک ثبت نشده است</div>';
                log('اجازه داده شده اما subscribe نشده', 'warning');
            } else {
                statusCard.classList.add('border-gray-300');
                statusText.textContent = '⏳ غیرفعال';
                statusDetails.innerHTML = '<div class="text-sm text-gray-600">برای دریافت اعلان، ابتدا اجازه دهید</div>';
                log('اعلان‌ها غیرفعال هستند', 'info');
            }
            
            updateButtons(status);
        }

        // به‌روزرسانی دکمه‌ها
        function updateButtons(status) {
            const btnPermission = document.getElementById('btn-request-permission');
            const btnSubscribe = document.getElementById('btn-subscribe');
            const btnUnsubscribe = document.getElementById('btn-unsubscribe');
            const btnSendTest = document.getElementById('btn-send-test');
            
            if (!window.pushManager.isSupported()) {
                btnPermission.disabled = true;
                btnSubscribe.disabled = true;
                btnUnsubscribe.disabled = true;
                btnSendTest.disabled = true;
                return;
            }
            
            btnPermission.disabled = status.permission === 'granted';
            btnSubscribe.disabled = status.subscribed || status.permission !== 'granted';
            btnUnsubscribe.disabled = !status.subscribed;
            btnSendTest.disabled = !status.subscribed;
        }

        // درخواست اجازه
        document.getElementById('btn-request-permission').addEventListener('click', async () => {
            try {
                log('درخواست اجازه...', 'info');
                const granted = await window.pushManager.requestPermission();
                if (granted) {
                    log('✅ اجازه داده شد', 'success');
                } else {
                    log('❌ اجازه داده نشد', 'error');
                }
                await checkStatus();
            } catch (error) {
                log('❌ خطا: ' + error.message, 'error');
            }
        });

        // فعال کردن
        document.getElementById('btn-subscribe').addEventListener('click', async () => {
            try {
                log('در حال فعال کردن اعلان‌ها...', 'info');
                await window.pushManager.subscribe();
                log('✅ اعلان‌ها فعال شدند', 'success');
                await checkStatus();
            } catch (error) {
                log('❌ خطا: ' + error.message, 'error');
            }
        });

        // غیرفعال کردن
        document.getElementById('btn-unsubscribe').addEventListener('click', async () => {
            if (!confirm('آیا مطمئن هستید که می‌خواهید اعلان‌ها را غیرفعال کنید؟')) {
                return;
            }
            try {
                log('در حال غیرفعال کردن اعلان‌ها...', 'info');
                await window.pushManager.unsubscribe();
                log('✅ اعلان‌ها غیرفعال شدند', 'success');
                await checkStatus();
            } catch (error) {
                log('❌ خطا: ' + error.message, 'error');
            }
        });

        // ارسال تست
        document.getElementById('btn-send-test').addEventListener('click', async () => {
            try {
                log('در حال ارسال اعلان تستی...', 'info');
                await window.pushManager.sendTestNotification();
                log('✅ اعلان تستی ارسال شد - چند ثانیه صبر کنید', 'success');
            } catch (error) {
                log('❌ خطا: ' + error.message, 'error');
            }
        });

        // بررسی اولیه
        window.addEventListener('load', () => {
            log('🚀 شروع تست Push Notifications...', 'info');
            checkStatus();
        });
    </script>
</body>
</html>
