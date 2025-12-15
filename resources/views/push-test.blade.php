<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
    <meta name="user-authenticated" content="true">
    @endauth
    <title>Push Notifications Test - allexam24</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css'])
    
    <style>
        body { font-family: Inter, system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-50 to-white p-4">
    <div class="max-w-2xl mx-auto py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">🔔 Push Notifications Test</h1>
            <p class="text-gray-600">Push Notification Management</p>
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
                    <h3 class="font-bold text-gray-900">Notification Status</h3>
                    <p class="text-sm text-gray-500" id="status-text">Checking...</p>
                </div>
            </div>
            <div class="space-y-2" id="status-details"></div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h3 class="font-bold text-gray-900 mb-4">🎮 Actions</h3>
            <div class="space-y-3">
                <button 
                    id="btn-request-permission" 
                    class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Request Permission
                </button>
                
                <button 
                    id="btn-subscribe" 
                    class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Enable Notifications
                </button>
                
                <button 
                    id="btn-unsubscribe" 
                    class="w-full bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Disable Notifications
                </button>
                
                <button 
                    id="btn-send-test" 
                    class="w-full bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Send Test Notification
                </button>
                
                <button 
                    onclick="window.location.href='/'" 
                    class="w-full bg-gray-600 text-white py-3 px-4 rounded-lg hover:bg-gray-700 transition-colors"
                >
                    Back to Home
                </button>
            </div>
        </div>

        <!-- Info -->
        <div class="bg-blue-50 rounded-xl p-6 border border-blue-200">
            <h3 class="font-bold text-blue-900 mb-3">ℹ️ Guide</h3>
            <ul class="text-sm text-blue-800 space-y-2">
                <li class="flex items-start gap-2">
                    <span class="text-blue-600 mt-0.5">•</span>
                    <span>To receive notifications, first grant permission</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-blue-600 mt-0.5">•</span>
                    <span>After enabling, you can send a test notification</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-blue-600 mt-0.5">•</span>
                    <span>Notifications work even when app is closed</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-blue-600 mt-0.5">•</span>
                    <span>You must be logged in to test</span>
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
            const time = new Date().toLocaleTimeString('en-US');
            logDiv.innerHTML += `<div class="${colors[type]}">[${time}] ${msg}</div>`;
            logDiv.scrollTop = logDiv.scrollHeight;
            console.log(msg);
        };

        // Check Status
        async function checkStatus() {
            const status = await window.pushManager.getSubscriptionStatus();
            
            const statusCard = document.getElementById('status-card');
            const statusText = document.getElementById('status-text');
            const statusDetails = document.getElementById('status-details');
            
            if (!window.pushManager.isSupported()) {
                statusCard.classList.add('border-red-500');
                statusText.textContent = '❌ Not Supported';
                statusDetails.innerHTML = '<div class="text-sm text-red-600">Your browser does not support Push Notifications</div>';
                log('Browser does not support Push Notifications', 'error');
                return;
            }
            
            if (status.permission === 'denied') {
                statusCard.classList.add('border-red-500');
                statusText.textContent = '🚫 Permission Denied';
                statusDetails.innerHTML = '<div class="text-sm text-red-600">You denied notification permission. Check browser settings to enable.</div>';
                log('Notification permission denied', 'error');
            } else if (status.permission === 'granted' && status.subscribed) {
                statusCard.classList.add('border-green-500');
                statusText.textContent = '✅ Active';
                statusDetails.innerHTML = '<div class="text-sm text-green-600">Notifications are active and ready to receive</div>';
                log('Notifications are active', 'success');
            } else if (status.permission === 'granted' && !status.subscribed) {
                statusCard.classList.add('border-yellow-500');
                statusText.textContent = '⚠️ Granted but inactive';
                statusDetails.innerHTML = '<div class="text-sm text-yellow-600">Permission granted but no subscription</div>';
                log('Granted but not subscribed', 'warning');
            } else {
                statusCard.classList.add('border-gray-300');
                statusText.textContent = '⏳ Inactive';
                statusDetails.innerHTML = '<div class="text-sm text-gray-600">To receive notifications, grant permission first</div>';
                log('Notifications are inactive', 'info');
            }
            
            updateButtons(status);
        }

        // Update Buttons
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

        // Request Permission
        document.getElementById('btn-request-permission').addEventListener('click', async () => {
            try {
                log('Requesting permission...', 'info');
                const granted = await window.pushManager.requestPermission();
                if (granted) {
                    log('✅ Permission granted', 'success');
                } else {
                    log('❌ Permission denied', 'error');
                }
                await checkStatus();
            } catch (error) {
                log('❌ Error: ' + error.message, 'error');
            }
        });

        // Enable
        document.getElementById('btn-subscribe').addEventListener('click', async () => {
            try {
                log('Enabling notifications...', 'info');
                await window.pushManager.subscribe();
                log('✅ Notifications enabled', 'success');
                await checkStatus();
            } catch (error) {
                log('❌ Error: ' + error.message, 'error');
            }
        });

        // Disable
        document.getElementById('btn-unsubscribe').addEventListener('click', async () => {
            if (!confirm('Are you sure you want to disable notifications?')) {
                return;
            }
            try {
                log('Disabling notifications...', 'info');
                await window.pushManager.unsubscribe();
                log('✅ Notifications disabled', 'success');
                await checkStatus();
            } catch (error) {
                log('❌ Error: ' + error.message, 'error');
            }
        });

        // Send Test
        document.getElementById('btn-send-test').addEventListener('click', async () => {
            try {
                log('Sending test notification...', 'info');
                await window.pushManager.sendTestNotification();
                log('✅ Test notification sent - wait a few seconds', 'success');
            } catch (error) {
                log('❌ Error: ' + error.message, 'error');
            }
        });

        // Check Initial Status
        window.addEventListener('load', () => {
            log('🚀 Starting Push Notifications Test...', 'info');
            checkStatus();
        });
    </script>
</body>
</html>
