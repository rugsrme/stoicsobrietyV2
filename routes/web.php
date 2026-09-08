<?php

use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LibraryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('books', [BookController::class, 'index'])->name('books.index');
Route::get('books/{book:slug}', [BookController::class, 'show'])->name('books.show');
Route::get('books/{book:slug}/sample', [BookController::class, 'sample'])->name('books.sample');

Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('library')->name('library.')->group(function () {
        Route::get('full', [LibraryController::class, 'full'])->name('full');
        Route::get('chapters/{chapter}', [LibraryController::class, 'chapter'])->name('chapter');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('posts', [AdminPostController::class, 'index'])->name('posts.index');

        Route::middleware('admin')->group(function () {
            Route::get('posts/create', [AdminPostController::class, 'create'])->name('posts.create');
            Route::post('posts', [AdminPostController::class, 'store'])->name('posts.store');
            Route::get('posts/{post}/edit', [AdminPostController::class, 'edit'])->name('posts.edit');
            Route::put('posts/{post}', [AdminPostController::class, 'update'])->name('posts.update');
            Route::delete('posts/{post}', [AdminPostController::class, 'destroy'])->name('posts.destroy');
        });
    });
});

require __DIR__.'/settings.php';
