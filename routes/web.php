<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ReflectionController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SampleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('books', [BookController::class, 'index'])->name('books.index');
Route::get('books/{book:slug}', [BookController::class, 'show'])->name('books.show');
Route::get('books/{book:slug}/sample', [BookController::class, 'sample'])->name('books.sample');

Route::get('reflections', [ReflectionController::class, 'index'])->name('reflections.index');
Route::get('reflections/{post:slug}', [ReflectionController::class, 'show'])->name('reflections.show');

// The section used to be called the blog; keep old links and shares working.
Route::permanentRedirect('blog', '/reflections');
Route::get('blog/{slug}', fn (string $slug) => redirect()->route('reflections.show', $slug, 301));

Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
Route::get('reviews/{post:slug}', [ReviewController::class, 'show'])->name('reviews.show');

Route::get('read', [SampleController::class, 'index'])->name('sample.index');
Route::get('read/{chapter}', [SampleController::class, 'chapter'])->name('sample.chapter');

Route::inertia('affiliate-disclosure', 'AffiliateDisclosure')->name('affiliate-disclosure');
Route::inertia('privacy-policy', 'PrivacyPolicy')->name('privacy-policy');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('library')->name('library.')->group(function () {
        Route::get('full', [LibraryController::class, 'full'])->name('full');
        Route::get('chapters/{chapter}', [LibraryController::class, 'chapter'])->name('chapter');
    });

    Route::middleware('admin')->group(function () {
        Route::get('journal', [JournalController::class, 'index'])->name('journal.index');
        Route::get('journal/{post:slug}', [JournalController::class, 'show'])->name('journal.show');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::middleware('admin')->group(function () {
            Route::get('posts', [AdminPostController::class, 'index'])->name('posts.index');
            Route::get('posts/create', [AdminPostController::class, 'create'])->name('posts.create');
            Route::post('posts', [AdminPostController::class, 'store'])->name('posts.store');
            Route::get('posts/{post}/edit', [AdminPostController::class, 'edit'])->name('posts.edit');
            Route::put('posts/{post}', [AdminPostController::class, 'update'])->name('posts.update');
            Route::delete('posts/{post}', [AdminPostController::class, 'destroy'])->name('posts.destroy');
            Route::post('posts/images', [AdminPostController::class, 'uploadImage'])->name('posts.images.store');

            Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

            Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
            Route::get('users/{user}', [AdminUserController::class, 'show'])->name('users.show');
            Route::patch('users/{user}', [AdminUserController::class, 'update'])->name('users.update');

            Route::resource('orders', AdminOrderController::class)->except('show');
        });
    });
});

require __DIR__.'/settings.php';
