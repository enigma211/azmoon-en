<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>آفلاین - آزمون کده</title>
    
    <!-- Fonts: Vazirmatn -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css'])
    
    <style>
        body {
            font-family: Vazirmatn, system-ui, -apple-system, sans-serif;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes pulse-slow {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .pulse-slow {
            animation: pulse-slow 2s ease-in-out infinite;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center">
        <!-- Icon -->
        <div class="mb-8 flex justify-center">
            <div class="relative">
                <!-- Background Circle -->
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full blur-2xl opacity-50"></div>
                
                <!-- Main Icon -->
                <div class="relative bg-white rounded-full p-8 shadow-xl float-animation">
                    <svg class="w-24 h-24 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Title -->
        <h1 class="text-3xl font-bold text-gray-900 mb-4">
            اتصال اینترنت قطع است
        </h1>
        
        <!-- Description -->
        <p class="text-gray-600 mb-8 leading-relaxed">
            برای استفاده از آزمون کده به اتصال اینترنت نیاز دارید.
            <br>
            لطفاً اتصال خود را بررسی کنید.
        </p>
        
        <!-- Connection Status -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
            <div class="flex items-center justify-center gap-3 mb-4">
                <div class="w-3 h-3 bg-red-500 rounded-full pulse-slow"></div>
                <span class="text-sm font-medium text-gray-700">آفلاین</span>
            </div>
            
            <div class="text-xs text-gray-500 space-y-2">
                <div class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>بررسی کنید که Wi-Fi یا داده موبایل فعال باشد</span>
                </div>
            </div>
        </div>
        
        <!-- Retry Button -->
        <button 
            onclick="window.location.reload()" 
            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 active:scale-95"
        >
            <div class="flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>تلاش مجدد</span>
            </div>
        </button>
        
        <!-- Tips -->
        <div class="mt-8 text-right">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">💡 نکات مفید:</h3>
            <ul class="text-xs text-gray-600 space-y-2">
                <li class="flex items-start gap-2">
                    <span class="text-indigo-600 mt-0.5">•</span>
                    <span>حالت هواپیما را خاموش کنید</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-indigo-600 mt-0.5">•</span>
                    <span>به شبکه Wi-Fi متصل شوید</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-indigo-600 mt-0.5">•</span>
                    <span>داده موبایل را فعال کنید</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-indigo-600 mt-0.5">•</span>
                    <span>روتر را ریست کنید</span>
                </li>
            </ul>
        </div>
        
        <!-- Auto Retry Indicator -->
        <div class="mt-8 flex items-center justify-center gap-2 text-xs text-gray-500">
            <div class="w-2 h-2 bg-gray-400 rounded-full animate-pulse"></div>
            <span>تلاش خودکار برای اتصال...</span>
        </div>
    </div>
    
    <!-- Auto Retry Script -->
    <script>
        // تلاش خودکار برای اتصال هر 5 ثانیه
        let retryCount = 0;
        const maxRetries = 20; // حداکثر 20 بار (100 ثانیه)
        
        function checkConnection() {
            if (retryCount >= maxRetries) {
                console.log('حداکثر تلاش انجام شد');
                return;
            }
            
            retryCount++;
            
            fetch('/manifest.webmanifest', { method: 'HEAD', cache: 'no-cache' })
                .then(response => {
                    if (response.ok) {
                        console.log('✅ اتصال برقرار شد، در حال بازگشت...');
                        window.location.href = '/';
                    }
                })
                .catch(() => {
                    console.log(`❌ تلاش ${retryCount} ناموفق، تلاش مجدد در 5 ثانیه...`);
                    setTimeout(checkConnection, 5000);
                });
        }
        
        // شروع تلاش خودکار بعد از 3 ثانیه
        setTimeout(checkConnection, 3000);
        
        // گوش دادن به تغییر وضعیت آنلاین/آفلاین
        window.addEventListener('online', () => {
            console.log('✅ اتصال برقرار شد');
            window.location.href = '/';
        });
        
        window.addEventListener('offline', () => {
            console.log('❌ اتصال قطع شد');
        });
    </script>
</body>
</html>
