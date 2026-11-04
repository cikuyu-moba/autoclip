<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\Admin\CourseManagementController;
use App\Http\Controllers\Admin\QuestionManagementController;
use App\Http\Controllers\Admin\StudentManagementController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/portfolio', [PublicController::class, 'portfolio'])->name('portfolio');
Route::get('/shop', [PublicController::class, 'shop'])->name('shop');
Route::get('/shop/{id}-{slug?}', [PublicController::class, 'shopShow'])->name('shop.show');
Route::get('/news', [PublicController::class, 'news'])->name('news');
Route::get('/news/{slug}', [PublicController::class, 'newsShow'])->name('news.show');
Route::get('/projects/{id}/visit', [PublicController::class, 'visitProject'])->name('projects.visit');

// Mandatory AdSense Legal & Information Pages
Route::get('/privacy-policy', [PublicController::class, 'privacy'])->name('privacy');
Route::get('/privacy', fn() => redirect()->route('privacy', [], 301));
Route::get('/kebijakan-privasi', fn() => redirect()->route('privacy', [], 301));

Route::get('/disclaimer', [PublicController::class, 'disclaimer'])->name('disclaimer');
Route::get('/penafian', fn() => redirect()->route('disclaimer', [], 301));

Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/about-us', fn() => redirect()->route('about', [], 301));
Route::get('/tentang-kami', fn() => redirect()->route('about', [], 301));

Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::get('/contact-us', fn() => redirect()->route('contact', [], 301));
Route::get('/kontak', fn() => redirect()->route('contact', [], 301));
Route::post('/contact', [PublicController::class, 'storeContact'])->name('contact.store');

// Software Download Public Routes
Route::get('/software', [\App\Http\Controllers\SoftwarePublicController::class, 'index'])->name('software.index');
Route::get('/software/{slug}', [\App\Http\Controllers\SoftwarePublicController::class, 'show'])->name('software.show');
Route::get('/software/{slug}/download', [\App\Http\Controllers\SoftwarePublicController::class, 'download'])->name('software.download');
Route::post('/software/{softwareId}/comments', [\App\Http\Controllers\SoftwarePublicController::class, 'storeComment'])->name('software.comments.store');
Route::post('/software/{softwareId}/report', [\App\Http\Controllers\SoftwarePublicController::class, 'reportBrokenLink'])->name('software.report');

// Search Autocomplete Suggestion API Routes
Route::get('/api/shop/suggest', [PublicController::class, 'shopSuggest'])->name('api.shop.suggest');
Route::get('/api/news/suggest', [PublicController::class, 'newsSuggest'])->name('api.news.suggest');
Route::get('/api/software/suggest', [\App\Http\Controllers\SoftwarePublicController::class, 'suggest'])->name('api.software.suggest');

// Auto Post API Route
Route::post('/api/auto-post', [\App\Http\Controllers\Api\AutoPostController::class, 'store'])->name('api.auto-post');

/*
|--------------------------------------------------------------------------
| Admin Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Dashboard & CRUD Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/visitor-logs', [\App\Http\Controllers\Admin\ReportController::class, 'visitorLogs'])->name('reports.visitor-logs');
    
    // Dynamic Menus & Submenus CRUD
    Route::resource('menus', MenuController::class)->except(['show', 'create', 'edit']);
    
    // News Categories CRUD
    Route::resource('categories', CategoryController::class)->except(['show', 'create', 'edit']);
    
    // News Posts CRUD
    Route::post('posts/upload', [PostController::class, 'uploadImage'])->name('posts.upload');
    Route::resource('posts', PostController::class);
    
    // Comments Moderation CRUD in Admin
    Route::get('comments', [\App\Http\Controllers\Admin\CommentController::class, 'index'])->name('comments.index');
    Route::put('comments/{id}/approve', [\App\Http\Controllers\Admin\CommentController::class, 'approve'])->name('comments.approve');
    Route::delete('comments/{id}/reject', [\App\Http\Controllers\Admin\CommentController::class, 'reject'])->name('comments.reject');
    Route::post('comments/{id}/reply', [\App\Http\Controllers\Admin\CommentController::class, 'reply'])->name('comments.reply');
    
    // Shop Products CRUD
    Route::resource('products', ProductController::class);

    // Software Management CRUD & Sync
    Route::post('software/sync-all', [\App\Http\Controllers\Admin\SoftwareController::class, 'syncAll'])->name('software.sync-all');
    Route::post('software/discover', [\App\Http\Controllers\Admin\SoftwareController::class, 'discover'])->name('software.discover');
    Route::post('software/{software}/sync', [\App\Http\Controllers\Admin\SoftwareController::class, 'syncSingle'])->name('software.sync-single');
    Route::get('software-reports', [\App\Http\Controllers\Admin\SoftwareController::class, 'reportsIndex'])->name('software.reports.index');
    Route::put('software-reports/{report}/resolve', [\App\Http\Controllers\Admin\SoftwareController::class, 'resolveReport'])->name('software.reports.resolve');
    Route::delete('software-reports/{report}', [\App\Http\Controllers\Admin\SoftwareController::class, 'destroyReport'])->name('software.reports.destroy');
    Route::resource('software', \App\Http\Controllers\Admin\SoftwareController::class);
    
    // Portfolio Projects CRUD
    Route::resource('projects', ProjectController::class);
    
    // Admin User CRUD
    Route::resource('users', UserController::class);

    // E-Learning Course CRUD
    Route::resource('courses', CourseManagementController::class);

    // E-Learning Quiz Question CRUD
    Route::resource('questions', QuestionManagementController::class);

    // Registered Students List CRUD
    Route::resource('students', StudentManagementController::class)->only(['index', 'show', 'destroy']);
    Route::post('students/{student}/courses/{course}/reset', [StudentManagementController::class, 'resetScores'])->name('students.reset-scores');

    // Gmail API OAuth management
    Route::get('gmail/auth', [\App\Http\Controllers\Admin\GmailAuthController::class, 'redirectToGoogle'])->name('gmail.auth');
    Route::post('gmail/disconnect', [\App\Http\Controllers\Admin\GmailAuthController::class, 'disconnect'])->name('gmail.disconnect');

    // Private bookings management
    Route::get('bookings', [\App\Http\Controllers\BookingRequestController::class, 'adminIndex'])->name('bookings.index');
    Route::get('bookings/{booking}', [\App\Http\Controllers\BookingRequestController::class, 'adminShow'])->name('bookings.show');
    Route::post('bookings/{booking}/toggle', [\App\Http\Controllers\BookingRequestController::class, 'adminToggleStatus'])->name('bookings.toggle');
    Route::delete('bookings/{booking}', [\App\Http\Controllers\BookingRequestController::class, 'adminDestroy'])->name('bookings.destroy');

    // Contact Messages management
    Route::get('contact-messages', [\App\Http\Controllers\Admin\ContactMessageController::class, 'index'])->name('contact-messages.index');
    Route::get('contact-messages/{message}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'show'])->name('contact-messages.show');
    Route::post('contact-messages/{message}/toggle-read', [\App\Http\Controllers\Admin\ContactMessageController::class, 'toggleRead'])->name('contact-messages.toggle-read');
    Route::delete('contact-messages/{message}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
});

/*
|--------------------------------------------------------------------------
| Student Frontend Auth & Course Routes
|--------------------------------------------------------------------------
*/
// Student auth routes
Route::get('/register', [StudentAuthController::class, 'showRegister'])->name('student.register');
Route::post('/register', [StudentAuthController::class, 'register'])->name('student.register.post');
Route::get('/login', [StudentAuthController::class, 'showLogin'])->name('student.login');
Route::post('/login', [StudentAuthController::class, 'login'])->name('student.login.post');
Route::post('/logout', [StudentAuthController::class, 'logout'])->name('student.logout');

// Password reset routes (Laravel standard names required by built-in ResetPassword notification)
Route::get('/forgot-password', [StudentAuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [StudentAuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [StudentAuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/post-reset-password', [StudentAuthController::class, 'resetPassword'])->name('password.update');

// Course page
Route::get('/courses/{slug}', [CourseController::class, 'show'])->name('courses.show');

// Protected Quiz routes (Must be logged in as student or admin)
Route::middleware(['auth'])->group(function () {
    Route::get('/courses/{slug}/test/{type}', [CourseController::class, 'showTest'])->name('courses.test');
    Route::post('/courses/{slug}/test/{type}', [CourseController::class, 'submitTest'])->name('courses.test.submit');
    Route::post('/bookings', [\App\Http\Controllers\BookingRequestController::class, 'store'])->name('bookings.store');
});

// Frontend Guest Comment Submit route
Route::post('/news/{postId}/comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');

// Gmail OAuth callback URI
Route::get('/gmail/oauth/callback', [\App\Http\Controllers\Admin\GmailAuthController::class, 'handleCallback'])->name('gmail.callback');
