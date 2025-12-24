@php
    $currentYear = date('Y');
    if (isset($title)) {
        $title = str_replace('[year]', $currentYear, $title);
    }
    if (isset($seoTitle)) {
        $seoTitle = str_replace('[year]', $currentYear, $seoTitle);
        if (!isset($title))
            $title = $seoTitle;
    }
    if (isset($seoDescription)) {
        $seoDescription = str_replace('[year]', $currentYear, $seoDescription);
    }
    if (isset($description)) {
        $description = str_replace('[year]', $currentYear, $description);
        if (!isset($seoDescription))
            $seoDescription = $description;
    }
@endphp
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-WYWE6DMN6Q"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'G-WYWE6DMN6Q');
    </script>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description"
        content="{{ $seoDescription ?? 'allexam24: The largest exam simulation platform. Practice with real past questions and get your results immediately.' }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">
    <title>{{ $title ?? config('app.name', 'allexam24') }}</title>
    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title ?? config('app.name', 'allexam24') }}">
    <meta property="og:description"
        content="{{ $seoDescription ?? 'allexam24: The largest exam simulation platform. Practice with real past questions and get your results immediately.' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ config('app.name', 'allexam24') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? config('app.name', 'allexam24') }}">
    <meta name="twitter:description"
        content="{{ $seoDescription ?? 'allexam24: The largest exam simulation platform. Practice with real past questions and get your results immediately.' }}">
    @php
        $favicon = \App\Helpers\BrandingHelper::getFavicon();
    @endphp
    @if($favicon)
        <link rel="icon" href="{{ $favicon }}">
    @else
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        .loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-overlay.show {
            display: flex;
        }

        .loading-spinner {
            width: 48px;
            height: 48px;
            border: 4px solid #fff;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .touchable {
            touch-action: manipulation;
        }

        body.font-small {
            font-size: 14px;
        }

        body.font-medium {
            font-size: 16px;
        }

        body.font-large {
            font-size: 18px;
        }

        body.font-xlarge {
            font-size: 20px;
        }
    </style>
</head>

<body class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div id="lw-overlay" class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <main class="max-w-screen-md mx-auto">
        {{ $slot }}

        <footer class="bg-gray-900 text-gray-300 mt-8 mb-20">
            <div class="max-w-screen-md mx-auto px-4 py-8">
                <div class="flex flex-col items-center justify-center space-y-4">
                    <div class="text-center text-sm text-gray-400">
                        <p>&copy; {{ date('Y') }} {{ config('app.name', 'allexam24') }} - All rights reserved.</p>
                    </div>
                </div>
            </div>
        </footer>
    </main>

    <nav class="fixed bottom-0 left-0 w-full bg-white dark:bg-gray-800 border-t-2 border-gray-200 dark:border-gray-700">
        <div class="max-w-screen-md mx-auto grid grid-cols-4 text-center text-sm">
            <a href="{{ route('home') }}" wire:navigate class="py-2 flex flex-col items-center gap-1 touchable">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75V21a.75.75 0 00.75.75h4.5a.75.75 0 00.75-.75v-3.75a.75.75 0 01.75-.75h2.25a.75.75 0 01.75.75V21a.75.75 0 00.75.75h4.5A.75.75 0 0021 21V9.75" />
                </svg>
                <span>Home</span>
            </a>
            <a href="{{ route('domains') }}" wire:navigate class="py-2 flex flex-col items-center gap-1 touchable">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6" />
                </svg>
                <span>Exams</span>
            </a>
            <a href="{{ route('flashcards.index') }}" wire:navigate
                class="py-2 flex flex-col items-center gap-1 touchable">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.499 5.216 50.59 50.59 0 00-2.658.812m-15.482 0a50.57 50.57 0 012.658.812m15.482 0a50.57 50.57 0 002.658-.812" />
                </svg>
                <span>Flashcards</span>
            </a>
            <a href="{{ route('profile') }}" wire:navigate class="py-2 flex flex-col items-center gap-1 touchable">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 1115 0v.75H4.5v-.75z" />
                </svg>
                <span>Profile</span>
            </a>
        </div>
    </nav>

    @livewireScripts
    <script>
        window.addEventListener('livewire:navigating', () => { document.getElementById('lw-overlay')?.classList.add('show'); });
        window.addEventListener('livewire:navigated', () => { document.getElementById('lw-overlay')?.classList.remove('show'); });
        document.addEventListener('livewire:load', () => {
            Livewire.on('loading', () => document.getElementById('lw-overlay')?.classList.add('show'));
            Livewire.on('loaded', () => document.getElementById('lw-overlay')?.classList.remove('show'));
        });
        function applyUserPreferences() {
            const fontSize = localStorage.getItem('userFontSize') || 'medium';
            const theme = localStorage.getItem('userTheme') || 'light';
            const fontSizeMap = { 'small': '14px', 'medium': '16px', 'large': '18px', 'xlarge': '20px' };
            document.body.style.fontSize = fontSizeMap[fontSize] || '16px';
            document.body.setAttribute('data-font-size', fontSize);
            document.body.setAttribute('data-theme', theme);
            if (theme === 'dark') {
                document.body.style.backgroundColor = '#1f2937';
                document.body.style.color = '#f3f4f6';
            } else {
                document.body.style.backgroundColor = '';
                document.body.style.color = '';
            }
        }
        document.addEventListener('DOMContentLoaded', applyUserPreferences);
        window.addEventListener('livewire:navigated', applyUserPreferences);
        window.showNotification = function (message) {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-[9999] transition-opacity duration-300';
            notification.textContent = message;
            notification.style.opacity = '1';
            document.body.appendChild(notification);
            setTimeout(() => { notification.style.opacity = '0'; setTimeout(() => notification.remove(), 300); }, 2000);
        };
    </script>
</body>

</html>