<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Livewire\HomePage;
use App\Livewire\DomainsPage;
use App\Livewire\ProfilePage;
use App\Livewire\BatchesPage;
use App\Livewire\ExamsPage;
use App\Livewire\ExamLanding;
use App\Livewire\ExamPlayer;
use App\Livewire\ExamResult;
// use App\Livewire\ResourceDetail;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SitemapController;
use App\Livewire\AttemptsPage;
use App\Livewire\SupportTicketsPage;
use App\Livewire\Admin\LogsPage as AdminLogsPage;
// use App\Livewire\EducationalResourcesPage;
// use App\Livewire\ResourceCategoriesPage;
// use App\Livewire\ResourcePostsPage;
// use App\Livewire\ResourcePostDetailPage;

use App\Livewire\BlogPage;
use App\Livewire\BlogPostPage;

// SPA-style routes powered by Livewire v3 (wire:navigate handled in views)
Route::get('/', HomePage::class)->name('home');
Route::get('/search', \App\Livewire\SearchPage::class)->name('search');
Route::get('/blog', BlogPage::class)->name('blog.index');
Route::get('/blog/tags/{tag}', \App\Livewire\BlogTagPage::class)->name('blog.tag');
Route::get('/blog/{category}', \App\Livewire\BlogCategoryPage::class)->name('blog.category');
Route::get('/blog/{category}/{slug}', BlogPostPage::class)->name('blog.show');
Route::get('/domains', DomainsPage::class)->name('domains');
// Route::get('/resources', EducationalResourcesPage::class)->name('resources'); // تغییر به سیستم جدید
Route::get('/profile', ProfilePage::class)->middleware(['auth'])->name('profile');
Route::get('/attempts', AttemptsPage::class)->middleware(['auth'])->name('attempts');
Route::get('/support-tickets', SupportTicketsPage::class)->middleware(['auth'])->name('support-tickets');

// PWA Offline page
Route::get('/offline', function () {
    return view('offline');
})->name('offline');

// Debug/Test routes (only in local environment)
if (App::environment('local')) {
    // PWA Test page (فقط در محیط development)
    Route::get('/pwa-test', function () {
        return view('pwa-test');
    })->name('pwa.test');

    // PWA Debug page (برای عیب‌یابی موبایل)
    Route::get('/pwa-debug', function () {
        return view('pwa-debug');
    })->name('pwa.debug');

    // Push Notifications Test page
    Route::get('/push-test', function () {
        return view('push-test');
    })->name('push.test');
}

// SEO Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Domain -> Batches -> Exams flow (Public for SEO, access control in views)
Route::get('/domains/{domain}/batches', BatchesPage::class)->name('batches');
Route::get('/batches/{batch}/exams', ExamsPage::class)->name('exams');

// Exam journey
Route::get('/exam/{exam}', ExamLanding::class)->name('exam.landing');
Route::get('/exam/{exam}/play', ExamPlayer::class)->name('exam.play');
Route::get('/exam/{exam}/result', ExamResult::class)->name('exam.result');

Route::middleware(['auth'])->group(function () {
    Route::post('/exam/{exam}/finish', [ExamController::class, 'finish'])
        ->middleware('throttle:10,1')
        ->name('exam.finish');
});

// Educational Resources Routes (Removed)
// Route::get('/educational-resources', EducationalResourcesPage::class)->name('educational-resources');
// Route::get('/educational-resources/{slug}', ResourceCategoriesPage::class)->name('educational-resources.categories');
// Route::get('/educational-resources/{examTypeSlug}/{categorySlug}', ResourcePostsPage::class)->name('educational-resources.posts');
// Route::get('/educational-resources/{examTypeSlug}/{categorySlug}/{postSlug}', ResourcePostDetailPage::class)->name('educational-resources.post');

// Flashcards / Leitner System
Route::get('/flashcards', \App\Livewire\Flashcard\DeckList::class)->name('flashcards.index');
Route::get('/flashcards/{deck}/study', \App\Livewire\Flashcard\StudyDeck::class)->name('flashcards.study');

// Alias for Breeze/legacy links expecting a dashboard route
Route::get('/dashboard', HomePage::class)->name('dashboard');

// Breeze auth routes
require __DIR__ . '/auth.php';

// Privacy Policy
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');

// Terms and Conditions
Route::get('/terms', function () {
    return view('terms');
})->name('terms');

// About Us
Route::get('/about', function () {
    return view('about');
})->name('about');

// Push Notifications API
Route::prefix('push')->name('push.')->group(function () {
    Route::get('/vapid-public-key', [\App\Http\Controllers\PushNotificationController::class, 'getPublicKey'])
        ->name('vapid-key');

    Route::post('/subscribe', [\App\Http\Controllers\PushNotificationController::class, 'subscribe'])
        ->name('subscribe');

    Route::post('/unsubscribe', [\App\Http\Controllers\PushNotificationController::class, 'unsubscribe'])
        ->name('unsubscribe');

    Route::post('/send-test', [\App\Http\Controllers\PushNotificationController::class, 'sendTest'])
        ->middleware(['auth'])
        ->name('send-test');
});
