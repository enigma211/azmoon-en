// AllExam24 - PWA Service Worker
// Version: 2.0.0
// Strategy: Network-First for HTML, Cache-First for Assets

const CACHE_VERSION = 'v2.0.0';
const CACHE_NAME = `allexam24-${CACHE_VERSION}`;
const OFFLINE_URL = '/offline';

// Essential files to precache during installation
const PRECACHE_ASSETS = [
  '/offline',
  '/manifest.webmanifest',
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png',
];

// Cache duration (24 hours)
const CACHE_MAX_AGE = 24 * 60 * 60 * 1000;

// ========================================
// Install Event - Install and Pre-cache
// ========================================
self.addEventListener('install', (event) => {
  console.log('[SW] Installing Service Worker version', CACHE_VERSION);
  
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('[SW] Pre-caching assets');
        return cache.addAll(PRECACHE_ASSETS);
      })
      .then(() => {
        console.log('[SW] Installation successful');
        return self.skipWaiting();
      })
      .catch((error) => {
        console.error('[SW] Installation error:', error);
      })
  );
});

// ========================================
// Activate Event - Activation and Cleanup
// ========================================
self.addEventListener('activate', (event) => {
  console.log('[SW] Activating Service Worker version', CACHE_VERSION);
  
  event.waitUntil(
    caches.keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => {
            if (cacheName !== CACHE_NAME) {
              console.log('[SW] Removing old cache:', cacheName);
              return caches.delete(cacheName);
            }
          })
        );
      })
      .then(() => {
        console.log('[SW] Activation successful');
        return self.clients.claim();
      })
  );
});

// ========================================
// Fetch Event - Request Handling
// ========================================
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Only GET requests
  if (request.method !== 'GET') {
    return;
  }

  // Ignore external requests (CDN, external API)
  if (url.origin !== location.origin) {
    return;
  }

  // Ignore Livewire requests
  if (url.pathname.startsWith('/livewire/')) {
    return;
  }

  // Network-First strategy for HTML pages
  if (request.mode === 'navigate' || request.headers.get('accept')?.includes('text/html')) {
    event.respondWith(networkFirstStrategy(request));
    return;
  }

  // Cache-First strategy for Static Assets
  if (isStaticAsset(url.pathname)) {
    event.respondWith(cacheFirstStrategy(request));
    return;
  }

  // Other requests: Network-First
  event.respondWith(networkFirstStrategy(request));
});

// ========================================
// Network-First Strategy
// ========================================
async function networkFirstStrategy(request) {
  try {
    const networkResponse = await fetch(request);
    
    // Cache successful response
    if (networkResponse && networkResponse.status === 200) {
      const cache = await caches.open(CACHE_NAME);
      cache.put(request, networkResponse.clone());
    }
    
    return networkResponse;
  } catch (error) {
    console.log('[SW] Network failed, trying cache:', request.url);
    
    // Check cache
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
      return cachedResponse;
    }
    
    // If HTML page, show Offline page
    if (request.mode === 'navigate') {
      const offlineResponse = new Response(OFFLINE_HTML, {
        headers: { 'Content-Type': 'text/html; charset=utf-8' }
      });
      return offlineResponse;
    }
    
    // Simple error response
    return new Response('Offline - Check your internet connection', {
      status: 503,
      statusText: 'Service Unavailable',
      headers: { 'Content-Type': 'text/plain; charset=utf-8' }
    });
  }
}

// ========================================
// Cache-First Strategy
// ========================================
async function cacheFirstStrategy(request) {
  const cachedResponse = await caches.match(request);
  
  if (cachedResponse) {
    // Check expiration
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
// Identify Static Files
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
    title: 'AllExam24',
    body: 'New Notification',
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
      console.error('[SW] Error parsing push data:', e);
    }
  }
  
  const options = {
    body: data.body,
    icon: data.icon,
    badge: data.badge,
    data: data.data,
    vibrate: [200, 100, 200],
    tag: data.tag || 'allexam24-notification',
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
        // If a window is open, focus it
        for (const client of clientList) {
          if (client.url === urlToOpen && 'focus' in client) {
            return client.focus();
          }
        }
        // If no window is open, open a new one
        if (clients.openWindow) {
          return clients.openWindow(urlToOpen);
        }
      })
  );
});

// ========================================
// Message Handler - Page Communication
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
