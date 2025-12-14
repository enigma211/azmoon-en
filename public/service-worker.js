// آزمون کده - Service Worker PWA
// نسخه: 2.0.0
// استراتژی: Network-First برای HTML، Cache-First برای Assets

const CACHE_VERSION = 'v2.0.0';
const CACHE_NAME = `azmoonkade-${CACHE_VERSION}`;
const OFFLINE_URL = '/offline';

// فایل‌های ضروری که باید در نصب cache شوند
const PRECACHE_ASSETS = [
  '/offline',
  '/manifest.webmanifest',
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png',
];

// مدت زمان cache (24 ساعت)
const CACHE_MAX_AGE = 24 * 60 * 60 * 1000;

// ========================================
// Install Event - نصب و Pre-cache
// ========================================
self.addEventListener('install', (event) => {
  console.log('[SW] نصب Service Worker نسخه', CACHE_VERSION);
  
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('[SW] Pre-caching assets');
        return cache.addAll(PRECACHE_ASSETS);
      })
      .then(() => {
        console.log('[SW] نصب با موفقیت انجام شد');
        return self.skipWaiting();
      })
      .catch((error) => {
        console.error('[SW] خطا در نصب:', error);
      })
  );
});

// ========================================
// Activate Event - فعال‌سازی و پاک‌سازی
// ========================================
self.addEventListener('activate', (event) => {
  console.log('[SW] فعال‌سازی Service Worker نسخه', CACHE_VERSION);
  
  event.waitUntil(
    caches.keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => {
            if (cacheName !== CACHE_NAME) {
              console.log('[SW] حذف cache قدیمی:', cacheName);
              return caches.delete(cacheName);
            }
          })
        );
      })
      .then(() => {
        console.log('[SW] فعال‌سازی با موفقیت انجام شد');
        return self.clients.claim();
      })
  );
});

// ========================================
// Fetch Event - مدیریت درخواست‌ها
// ========================================
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // فقط درخواست‌های GET
  if (request.method !== 'GET') {
    return;
  }

  // نادیده گرفتن درخواست‌های خارجی (CDN, API خارجی)
  if (url.origin !== location.origin) {
    return;
  }

  // نادیده گرفتن درخواست‌های Livewire
  if (url.pathname.startsWith('/livewire/')) {
    return;
  }

  // استراتژی Network-First برای صفحات HTML
  if (request.mode === 'navigate' || request.headers.get('accept')?.includes('text/html')) {
    event.respondWith(networkFirstStrategy(request));
    return;
  }

  // استراتژی Cache-First برای Assets استاتیک
  if (isStaticAsset(url.pathname)) {
    event.respondWith(cacheFirstStrategy(request));
    return;
  }

  // بقیه درخواست‌ها: Network-First
  event.respondWith(networkFirstStrategy(request));
});

// ========================================
// استراتژی Network-First
// ========================================
async function networkFirstStrategy(request) {
  try {
    const networkResponse = await fetch(request);
    
    // Cache کردن پاسخ موفق
    if (networkResponse && networkResponse.status === 200) {
      const cache = await caches.open(CACHE_NAME);
      cache.put(request, networkResponse.clone());
    }
    
    return networkResponse;
  } catch (error) {
    console.log('[SW] Network failed, trying cache:', request.url);
    
    // بررسی cache
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
      return cachedResponse;
    }
    
    // اگر صفحه HTML بود، نمایش صفحه Offline
    if (request.mode === 'navigate') {
      const offlineResponse = await caches.match(OFFLINE_URL);
      if (offlineResponse) {
        return offlineResponse;
      }
    }
    
    // پاسخ خطای ساده
    return new Response('آفلاین - اتصال اینترنت خود را بررسی کنید', {
      status: 503,
      statusText: 'Service Unavailable',
      headers: { 'Content-Type': 'text/plain; charset=utf-8' }
    });
  }
}

// ========================================
// استراتژی Cache-First
// ========================================
async function cacheFirstStrategy(request) {
  const cachedResponse = await caches.match(request);
  
  if (cachedResponse) {
    // بررسی تاریخ انقضا
    const cachedDate = new Date(cachedResponse.headers.get('date'));
    const now = new Date();
    
    if (now - cachedDate < CACHE_MAX_AGE) {
      return cachedResponse;
    }
  }
  
  try {
    const networkResponse = await fetch(request);
    
    if (networkResponse && networkResponse.status === 200) {
      const cache = await caches.open(CACHE_NAME);
      cache.put(request, networkResponse.clone());
    }
    
    return networkResponse;
  } catch (error) {
    if (cachedResponse) {
      return cachedResponse;
    }
    throw error;
  }
}

// ========================================
// تشخیص فایل‌های استاتیک
// ========================================
function isStaticAsset(pathname) {
  const staticExtensions = [
    '.css', '.js', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.webp',
    '.woff', '.woff2', '.ttf', '.eot', '.ico', '.json'
  ];
  
  return staticExtensions.some(ext => pathname.endsWith(ext));
}

// ========================================
// Push Notification Handler
// ========================================
self.addEventListener('push', (event) => {
  console.log('[SW] Push notification received');
  
  let data = {
    title: 'آزمون کده',
    body: 'اعلان جدید',
    icon: '/icons/icon-192x192.png',
    badge: '/icons/icon-96x96.png',
    data: {
      url: '/'
    }
  };
  
  if (event.data) {
    try {
      data = { ...data, ...event.data.json() };
    } catch (e) {
      console.error('[SW] خطا در parse کردن push data:', e);
    }
  }
  
  const options = {
    body: data.body,
    icon: data.icon,
    badge: data.badge,
    data: data.data,
    vibrate: [200, 100, 200],
    tag: data.tag || 'azmoonkade-notification',
    requireInteraction: false,
    actions: data.actions || []
  };
  
  event.waitUntil(
    self.registration.showNotification(data.title, options)
  );
});

// ========================================
// Notification Click Handler
// ========================================
self.addEventListener('notificationclick', (event) => {
  console.log('[SW] Notification clicked');
  
  event.notification.close();
  
  const urlToOpen = event.notification.data?.url || '/';
  
  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true })
      .then((clientList) => {
        // اگر پنجره‌ای باز است، فوکوس کن
        for (const client of clientList) {
          if (client.url === urlToOpen && 'focus' in client) {
            return client.focus();
          }
        }
        // اگر پنجره‌ای باز نیست، پنجره جدید باز کن
        if (clients.openWindow) {
          return clients.openWindow(urlToOpen);
        }
      })
  );
});

// ========================================
// Message Handler - ارتباط با صفحه
// ========================================
self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
  
  if (event.data && event.data.type === 'CLEAR_CACHE') {
    event.waitUntil(
      caches.keys().then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => caches.delete(cacheName))
        );
      })
    );
  }
});
