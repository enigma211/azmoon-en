<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>دیباگ PWA - آزمون کده</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Tahoma, Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
            font-size: 14px;
        }
        .card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card h3 {
            margin-bottom: 10px;
            color: #333;
            font-size: 16px;
        }
        .status {
            padding: 8px 12px;
            border-radius: 4px;
            margin: 5px 0;
            font-size: 13px;
        }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .warning { background: #fff3cd; color: #856404; }
        .info { background: #d1ecf1; color: #0c5460; }
        button {
            background: #4f46e5;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            width: 100%;
            margin-top: 10px;
            font-size: 14px;
        }
        pre {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
            font-size: 12px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h2 style="margin-bottom: 20px; text-align: center;">🔍 دیباگ PWA</h2>

    <div class="card">
        <h3>📱 اطلاعات دستگاه</h3>
        <div id="device-info"></div>
    </div>

    <div class="card">
        <h3>🌐 پروتکل و دامنه</h3>
        <div id="protocol-info"></div>
    </div>

    <div class="card">
        <h3>📋 Manifest</h3>
        <div id="manifest-info"></div>
    </div>

    <div class="card">
        <h3>⚙️ Service Worker</h3>
        <div id="sw-info"></div>
    </div>

    <div class="card">
        <h3>💾 Cache Storage</h3>
        <div id="cache-info"></div>
    </div>

    <div class="card">
        <h3>📦 قابلیت نصب</h3>
        <div id="install-info"></div>
        <button id="install-btn" style="display: none;">نصب اپلیکیشن</button>
    </div>

    <div class="card">
        <h3>🔧 اقدامات</h3>
        <button onclick="location.reload()">رفرش صفحه</button>
        <button onclick="unregisterSW()">حذف Service Worker</button>
        <button onclick="clearAllCaches()">پاک کردن Cache</button>
    </div>

    <script>
        let deferredPrompt;

        // Device Info
        document.getElementById('device-info').innerHTML = `
            <div class="status info">User Agent: ${navigator.userAgent}</div>
            <div class="status info">Platform: ${navigator.platform}</div>
            <div class="status info">Screen: ${screen.width}x${screen.height}</div>
            <div class="status info">Standalone: ${window.matchMedia('(display-mode: standalone)').matches ? 'بله' : 'خیر'}</div>
        `;

        // Protocol Info
        const isHTTPS = location.protocol === 'https:';
        document.getElementById('protocol-info').innerHTML = `
            <div class="status ${isHTTPS ? 'success' : 'error'}">
                پروتکل: ${location.protocol}
            </div>
            <div class="status info">دامنه: ${location.hostname}</div>
            <div class="status info">پورت: ${location.port || 'پیش‌فرض'}</div>
            ${!isHTTPS ? '<div class="status error">⚠️ HTTPS مورد نیاز است!</div>' : ''}
        `;

        // Check Manifest
        fetch('/manifest.webmanifest')
            .then(res => res.json())
            .then(manifest => {
                const hasIcons = manifest.icons && manifest.icons.length > 0;
                const has144Icon = manifest.icons?.some(i => {
                    const sizes = i.sizes.split('x');
                    return parseInt(sizes[0]) >= 144;
                });
                
                document.getElementById('manifest-info').innerHTML = `
                    <div class="status success">✅ Manifest موجود است</div>
                    <div class="status ${hasIcons ? 'success' : 'error'}">
                        آیکون‌ها: ${manifest.icons?.length || 0} عدد
                    </div>
                    <div class="status ${has144Icon ? 'success' : 'error'}">
                        آیکون 144px+: ${has144Icon ? 'موجود' : 'ناموجود'}
                    </div>
                    <div class="status info">نام: ${manifest.name}</div>
                    <div class="status info">Display: ${manifest.display}</div>
                    <pre>${JSON.stringify(manifest, null, 2).substring(0, 500)}...</pre>
                `;
            })
            .catch(err => {
                document.getElementById('manifest-info').innerHTML = `
                    <div class="status error">❌ خطا: ${err.message}</div>
                `;
            });

        // Check Service Worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistration()
                .then(reg => {
                    if (reg) {
                        const state = reg.active?.state || 'نامشخص';
                        document.getElementById('sw-info').innerHTML = `
                            <div class="status success">✅ Service Worker ثبت شده</div>
                            <div class="status info">وضعیت: ${state}</div>
                            <div class="status info">Scope: ${reg.scope}</div>
                            <div class="status info">Update: ${reg.updateViaCache}</div>
                        `;
                    } else {
                        document.getElementById('sw-info').innerHTML = `
                            <div class="status error">❌ Service Worker ثبت نشده</div>
                            <div class="status warning">در حال تلاش برای ثبت...</div>
                        `;
                        
                        // Try to register
                        navigator.serviceWorker.register('/service-worker.js')
                            .then(() => {
                                document.getElementById('sw-info').innerHTML += `
                                    <div class="status success">✅ ثبت موفق - لطفاً رفرش کنید</div>
                                `;
                            })
                            .catch(err => {
                                document.getElementById('sw-info').innerHTML += `
                                    <div class="status error">❌ خطا در ثبت: ${err.message}</div>
                                `;
                            });
                    }
                })
                .catch(err => {
                    document.getElementById('sw-info').innerHTML = `
                        <div class="status error">❌ خطا: ${err.message}</div>
                    `;
                });
        } else {
            document.getElementById('sw-info').innerHTML = `
                <div class="status error">❌ مرورگر از Service Worker پشتیبانی نمی‌کند</div>
            `;
        }

        // Check Cache
        if ('caches' in window) {
            caches.keys().then(names => {
                const count = names.length;
                document.getElementById('cache-info').innerHTML = `
                    <div class="status ${count > 0 ? 'success' : 'warning'}">
                        تعداد Cache: ${count}
                    </div>
                    ${names.length > 0 ? `<div class="status info">نام‌ها: ${names.join(', ')}</div>` : ''}
                `;
            });
        } else {
            document.getElementById('cache-info').innerHTML = `
                <div class="status error">❌ Cache API پشتیبانی نمی‌شود</div>
            `;
        }

        // Install Prompt
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            
            document.getElementById('install-info').innerHTML = `
                <div class="status success">✅ اپلیکیشن قابل نصب است!</div>
                <div class="status info">رویداد beforeinstallprompt فعال شد</div>
            `;
            document.getElementById('install-btn').style.display = 'block';
        });

        // Check if already installed
        if (window.matchMedia('(display-mode: standalone)').matches) {
            document.getElementById('install-info').innerHTML = `
                <div class="status success">✅ اپلیکیشن نصب شده است</div>
            `;
        } else {
            document.getElementById('install-info').innerHTML = `
                <div class="status warning">⏳ در انتظار رویداد نصب...</div>
                <div class="status info">اگر رویداد نیامد، شرایط PWA بررسی شود</div>
            `;
        }

        // Install button
        document.getElementById('install-btn').addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                document.getElementById('install-info').innerHTML += `
                    <div class="status ${outcome === 'accepted' ? 'success' : 'warning'}">
                        نتیجه: ${outcome === 'accepted' ? 'نصب شد' : 'لغو شد'}
                    </div>
                `;
                deferredPrompt = null;
            }
        });

        // Unregister SW
        async function unregisterSW() {
            if ('serviceWorker' in navigator) {
                const reg = await navigator.serviceWorker.getRegistration();
                if (reg) {
                    await reg.unregister();
                    alert('Service Worker حذف شد. صفحه را رفرش کنید.');
                }
            }
        }

        // Clear caches
        async function clearAllCaches() {
            if ('caches' in window) {
                const names = await caches.keys();
                await Promise.all(names.map(name => caches.delete(name)));
                alert('تمام Cache پاک شد. صفحه را رفرش کنید.');
            }
        }
    </script>
</body>
</html>
