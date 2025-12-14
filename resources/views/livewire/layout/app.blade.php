<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="آزمون کده: بزرگترین شبیه ساز آزمون نظام مهندسی. با نمونه سوالات واقعی سال‌های گذشته در محیطی مشابه آزمون اصلی تمرین کنید و کارنامه قبولی/مردودی خود را فوراً دریافت نمایید.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">
    <title>{{ $title ?? config('app.name', 'آزمون کده') }}</title>
    <meta property="og:locale" content="fa_IR">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title ?? config('app.name', 'آزمون کده') }}">
    <meta property="og:description" content="آزمون کده: بزرگترین شبیه ساز آزمون نظام مهندسی. با نمونه سوالات واقعی سال‌های گذشته در محیطی مشابه آزمون اصلی تمرین کنید و کارنامه قبولی/مردودی خود را فوراً دریافت نمایید.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ config('app.name', 'آزمون کده') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? config('app.name', 'آزمون کده') }}">
    <meta name="twitter:description" content="آزمون کده: بزرگترین شبیه ساز آزمون نظام مهندسی. با نمونه سوالات واقعی سال‌های گذشته در محیطی مشابه آزمون اصلی تمرین کنید و کارنامه قبولی/مردودی خود را فوراً دریافت نمایید.">
    @php
        $favicon = \App\Helpers\BrandingHelper::getFavicon();
    @endphp
    @if($favicon)
        <link rel="icon" type="image/png" href="{{ $favicon }}">
    @endif
    @vite(['resources/css/app.css','resources/js/app.js'])
    @livewireStyles
    <style>
      .loading-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.45); display: none; align-items: center; justify-content: center; z-index: 9999; }
      .loading-overlay.show { display: flex; }
      .loading-spinner { width: 48px; height: 48px; border: 4px solid #fff; border-top-color: transparent; border-radius: 50%; animation: spin 1s linear infinite; }
      @keyframes spin { to { transform: rotate(360deg); } }
      .touchable { touch-action: manipulation; }
      .enamad-logo img {
        max-width: 100px;
        height: auto;
        display: block;
      }
      .enamad-logo a {
        display: inline-block;
        transition: opacity 0.2s;
      }
      .enamad-logo a:hover {
        opacity: 0.8;
      }

      /* Font Size Classes - برای fallback */
      body.font-small { font-size: 14px; }
      body.font-medium { font-size: 16px; }
      body.font-large { font-size: 18px; }
      body.font-xlarge { font-size: 20px; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div id="lw-overlay" class="loading-overlay"><div class="loading-spinner"></div></div>

    <main class="max-w-screen-md mx-auto">
        {{ $slot }}
        
        <!-- Footer -->
        <footer class="bg-gray-900 text-gray-300 mt-8 mb-20">
            <div class="max-w-screen-md mx-auto px-4 py-8">
                <div class="flex flex-col items-center justify-center space-y-4">
                    <!-- لوگوی اینماد -->
                    <div class="enamad-logo">
                        <a referrerpolicy='origin' target='_blank' href='https://trustseal.enamad.ir/?id=668272&Code=OJG9rk1Il7Gg1QVCca78Alvk4RRDPkse'>
                            <img referrerpolicy='origin' src='https://trustseal.enamad.ir/logo.aspx?id=668272&Code=OJG9rk1Il7Gg1QVCca78Alvk4RRDPkse' alt='اینماد' style='cursor:pointer' code='OJG9rk1Il7Gg1QVCca78Alvk4RRDPkse'>
                        </a>
                    </div>
                    
                    <!-- Copyright -->
                    <div class="text-center text-sm text-gray-400">
                        <p>© {{ date('Y') }} {{ config('app.name', 'آزمون کده') }} - تمامی حقوق محفوظ است.</p>
                    </div>
                </div>
            </div>
        </footer>
    </main>

    <nav class="fixed bottom-0 left-0 w-full bg-white dark:bg-gray-800 border-t-2 border-gray-200 dark:border-gray-700">
        <div class="max-w-screen-md mx-auto grid grid-cols-4 text-center text-sm">
            <a href="{{ route('home') }}" wire:navigate class="py-2 flex flex-col items-center gap-1 touchable">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75V21a.75.75 0 00.75.75h4.5a.75.75 0 00.75-.75v-3.75a.75.75 0 01.75-.75h2.25a.75.75 0 01.75.75V21a.75.75 0 00.75.75h4.5A.75.75 0 0021 21V9.75" /></svg>
                <span>خانه</span>
            </a>
            <a href="{{ route('exams') }}" wire:navigate class="py-2 flex flex-col items-center gap-1 touchable">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6" /></svg>
                <span>آزمون‌ها</span>
            </a>
            <a href="{{ route('resources') }}" wire:navigate class="py-2 flex flex-col items-center gap-1 touchable">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 5.25h15m-15 4.5h15m-15 4.5h15m-15 4.5h15" /></svg>
                <span>منابع</span>
            </a>
            <a href="{{ route('profile') }}" wire:navigate class="py-2 flex flex-col items-center gap-1 touchable">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 1115 0v.75H4.5v-.75z" /></svg>
                <span>پروفایل</span>
            </a>
        </div>
    </nav>

    @livewireScripts
    <script>
      // Livewire v3 navigation events
      window.addEventListener('livewire:navigating', () => {
        document.getElementById('lw-overlay')?.classList.add('show');
      });
      window.addEventListener('livewire:navigated', () => {
        document.getElementById('lw-overlay')?.classList.remove('show');
      });

      // Fallback custom events
      document.addEventListener('livewire:load', () => {
        Livewire.on('loading', () => document.getElementById('lw-overlay')?.classList.add('show'));
        Livewire.on('loaded', () => document.getElementById('lw-overlay')?.classList.remove('show'));
      });

      // User Preferences Management with inline styles
      function applyUserPreferences() {
        const fontSize = localStorage.getItem('userFontSize') || '{{ auth()->check() ? auth()->user()->font_size ?? "medium" : "medium" }}';
        const theme = localStorage.getItem('userTheme') || '{{ auth()->check() ? auth()->user()->theme ?? "light" : "light" }}';
        
        // Font size mapping
        const fontSizeMap = {
          'small': '14px',
          'medium': '16px',
          'large': '18px',
          'xlarge': '20px'
        };
        
        // Apply font size
        document.body.style.fontSize = fontSizeMap[fontSize] || '16px';
        document.body.setAttribute('data-font-size', fontSize);
        
        // Apply theme
        document.body.setAttribute('data-theme', theme);
        if (theme === 'dark') {
          // Background body را تاریک می‌کنیم
          document.body.style.backgroundColor = '#1f2937';
          document.body.style.color = '#f3f4f6';
          
          // برای صفحه آزمون: متن سوالات و پاسخ‌ها را سفید می‌کنیم
          setTimeout(() => {
            // متن سوالات
            document.querySelectorAll('.question-text, .choice-text, .exam-question').forEach(el => {
              el.style.color = '#f3f4f6';
            });
            // متن‌های خاکستری را روشن‌تر می‌کنیم
            document.querySelectorAll('.text-gray-500, .text-gray-600').forEach(el => {
              el.style.color = '#d1d5db';
            });
          }, 100);
        } else {
          // حالت روشن
          document.body.style.backgroundColor = '';
          document.body.style.color = '';
          
          // ریست کردن رنگ‌ها
          setTimeout(() => {
            document.querySelectorAll('.question-text, .choice-text, .exam-question').forEach(el => {
              el.style.color = '';
            });
            document.querySelectorAll('.text-gray-500, .text-gray-600').forEach(el => {
              el.style.color = '';
            });
          }, 100);
        }
      }

      // Apply preferences on page load
      document.addEventListener('DOMContentLoaded', applyUserPreferences);
      
      // Apply preferences after Livewire navigation
      window.addEventListener('livewire:navigated', applyUserPreferences);

      // Global notification function
      window.showNotification = function(message) {
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-[9999] transition-opacity duration-300';
        notification.textContent = message;
        notification.style.opacity = '1';
        document.body.appendChild(notification);
        
        setTimeout(() => {
          notification.style.opacity = '0';
          setTimeout(() => notification.remove(), 300);
        }, 2000);
      };
    </script>
</body>
</html>
