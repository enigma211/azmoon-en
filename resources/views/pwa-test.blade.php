<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PWA Test - allexam24</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css'])
    
    <style>
        body { font-family: Inter, system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-50 to-white p-4">
    <div class="max-w-4xl mx-auto py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">🧪 allexam24 PWA Test</h1>
            <p class="text-gray-600">Complete Progressive Web App Capabilities Check</p>
        </div>

        <!-- Status Cards -->
        <div class="grid gap-4 md:grid-cols-2 mb-8">
            <!-- Manifest Status -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-2" id="manifest-card">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Manifest</h3>
                        <p class="text-sm text-gray-500" id="manifest-status">Checking...</p>
                    </div>
                </div>
                <div class="text-xs text-gray-600 space-y-1" id="manifest-details"></div>
            </div>

            <!-- Service Worker Status -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-2" id="sw-card">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Service Worker</h3>
                        <p class="text-sm text-gray-500" id="sw-status">Checking...</p>
                    </div>
                </div>
                <div class="text-xs text-gray-600 space-y-1" id="sw-details"></div>
            </div>

            <!-- Install Status -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-2" id="install-card">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Installability</h3>
                        <p class="text-sm text-gray-500" id="install-status">Checking...</p>
                    </div>
                </div>
                <button id="install-btn" class="hidden w-full mt-3 bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors">
                    Install App
                </button>
            </div>

            <!-- Cache Status -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-2" id="cache-card">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Cache Storage</h3>
                        <p class="text-sm text-gray-500" id="cache-status">Checking...</p>
                    </div>
                </div>
                <div class="text-xs text-gray-600 space-y-1" id="cache-details"></div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h3 class="font-bold text-gray-900 mb-4">🎮 Test Actions</h3>
            <div class="grid gap-3 md:grid-cols-2">
                <button onclick="testOffline()" class="bg-orange-600 text-white py-3 px-4 rounded-lg hover:bg-orange-700 transition-colors">
                    Test Offline Mode
                </button>
                <button onclick="clearCache()" class="bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 transition-colors">
                    Clear Cache
                </button>
                <button onclick="checkManifest()" class="bg-indigo-600 text-white py-3 px-4 rounded-lg hover:bg-indigo-700 transition-colors">
                    Check Manifest
                </button>
                <button onclick="window.location.href='/'" class="bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors">
                    Back to Home
                </button>
            </div>
        </div>

        <!-- Lighthouse Score -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl shadow-lg p-6 text-white text-center">
            <h3 class="font-bold text-xl mb-2">📊 Lighthouse Score</h3>
            <p class="text-sm opacity-90 mb-4">Use Chrome DevTools to check PWA score</p>
            <div class="flex justify-center gap-4 text-sm">
                <div>
                    <div class="text-2xl font-bold">?</div>
                    <div class="opacity-75">Performance</div>
                </div>
                <div>
                    <div class="text-2xl font-bold">?</div>
                    <div class="opacity-75">PWA</div>
                </div>
                <div>
                    <div class="text-2xl font-bold">?</div>
                    <div class="opacity-75">Best Practices</div>
                </div>
            </div>
        </div>

        <!-- Console Log -->
        <div class="mt-8 bg-gray-900 rounded-xl shadow-lg p-6 text-white">
            <h3 class="font-bold mb-4">📋 Console Log</h3>
            <div id="console-log" class="text-xs font-mono space-y-1 max-h-64 overflow-y-auto"></div>
        </div>
    </div>

    <script>
        let deferredPrompt;
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

        // Check Manifest
        async function checkManifest() {
            try {
                const response = await fetch('/manifest.webmanifest');
                const manifest = await response.json();
                
                document.getElementById('manifest-status').textContent = '✅ Found';
                document.getElementById('manifest-card').classList.add('border-green-500');
                document.getElementById('manifest-details').innerHTML = `
                    <div>✓ Name: ${manifest.name}</div>
                    <div>✓ Icons: ${manifest.icons?.length || 0} count</div>
                    <div>✓ Start URL: ${manifest.start_url}</div>
                    <div>✓ Display: ${manifest.display}</div>
                `;
                log('✅ Manifest checked', 'success');
            } catch (error) {
                document.getElementById('manifest-status').textContent = '❌ Error';
                document.getElementById('manifest-card').classList.add('border-red-500');
                log('❌ Manifest check error: ' + error.message, 'error');
            }
        }

        // Check Service Worker
        async function checkServiceWorker() {
            if ('serviceWorker' in navigator) {
                try {
                    const registration = await navigator.serviceWorker.getRegistration();
                    if (registration) {
                        const state = registration.active?.state || 'unknown';
                        document.getElementById('sw-status').textContent = '✅ Active';
                        document.getElementById('sw-card').classList.add('border-green-500');
                        document.getElementById('sw-details').innerHTML = `
                            <div>✓ Status: ${state}</div>
                            <div>✓ Scope: ${registration.scope}</div>
                            <div>✓ Update: ${registration.updateViaCache}</div>
                        `;
                        log('✅ Service Worker is active', 'success');
                    } else {
                        throw new Error('Service Worker not registered');
                    }
                } catch (error) {
                    document.getElementById('sw-status').textContent = '❌ Inactive';
                    document.getElementById('sw-card').classList.add('border-red-500');
                    log('❌ Service Worker: ' + error.message, 'error');
                }
            } else {
                document.getElementById('sw-status').textContent = '❌ Not Supported';
                log('❌ Browser does not support Service Worker', 'error');
            }
        }

        // Check Cache
        async function checkCache() {
            if ('caches' in window) {
                try {
                    const cacheNames = await caches.keys();
                    const totalCaches = cacheNames.length;
                    
                    let totalSize = 0;
                    for (const cacheName of cacheNames) {
                        const cache = await caches.open(cacheName);
                        const keys = await cache.keys();
                        totalSize += keys.length;
                    }
                    
                    document.getElementById('cache-status').textContent = `✅ ${totalCaches} caches`;
                    document.getElementById('cache-card').classList.add('border-green-500');
                    document.getElementById('cache-details').innerHTML = `
                        <div>✓ Cache Count: ${totalCaches}</div>
                        <div>✓ File Count: ${totalSize}</div>
                        <div>✓ Names: ${cacheNames.join(', ')}</div>
                    `;
                    log(`✅ ${totalCaches} caches with ${totalSize} files`, 'success');
                } catch (error) {
                    document.getElementById('cache-status').textContent = '❌ Error';
                    log('❌ Cache check error: ' + error.message, 'error');
                }
            }
        }

        // Check Install
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            document.getElementById('install-status').textContent = '✅ Ready to install';
            document.getElementById('install-card').classList.add('border-green-500');
            document.getElementById('install-btn').classList.remove('hidden');
            log('✅ App is installable', 'success');
        });

        document.getElementById('install-btn')?.addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                log(`Install outcome: ${outcome}`, outcome === 'accepted' ? 'success' : 'warning');
                deferredPrompt = null;
                document.getElementById('install-btn').classList.add('hidden');
            }
        });

        // Test Offline
        function testOffline() {
            log('⚠️ To test offline, enable DevTools → Network → Offline', 'warning');
            alert('For offline test:\n1. Press F12\n2. Go to Network tab\n3. Enable Offline\n4. Refresh page');
        }

        // Clear Cache
        async function clearCache() {
            if (confirm('Are you sure you want to clear all caches?')) {
                try {
                    const cacheNames = await caches.keys();
                    await Promise.all(cacheNames.map(name => caches.delete(name)));
                    log('✅ All caches cleared', 'success');
                    alert('Cache cleared. Refresh the page.');
                } catch (error) {
                    log('❌ Cache clear error: ' + error.message, 'error');
                }
            }
        }

        // Initialize
        window.addEventListener('load', () => {
            log('🚀 Starting PWA Test...', 'info');
            checkManifest();
            checkServiceWorker();
            checkCache();
            
            // Check if already installed
            if (window.matchMedia('(display-mode: standalone)').matches) {
                document.getElementById('install-status').textContent = '✅ Installed';
                document.getElementById('install-card').classList.add('border-green-500');
                log('✅ App running in Standalone mode', 'success');
            } else {
                document.getElementById('install-status').textContent = '⏳ Not installed';
                log('ℹ️ App running in browser', 'info');
            }
        });
    </script>
</body>
</html>
