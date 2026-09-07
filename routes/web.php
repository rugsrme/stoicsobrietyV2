<?php

use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('books', [BookController::class, 'index'])->name('books.index');
Route::get('books/{book:slug}', [BookController::class, 'show'])->name('books.show');
Route::get('books/{book:slug}/sample', [BookController::class, 'sample'])->name('books.sample');

Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('posts', AdminPostController::class)->except(['show']);
    });
});

require __DIR__.'/settings.php';
