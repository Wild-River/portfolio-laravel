<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\WorkController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Site;
use Illuminate\Support\Facades\Route;

Route::get('/', Site\HomeController::class)->name('home');
Route::get('/posts', [Site\PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [Site\PostController::class, 'show'])->name('posts.show');
Route::get('/works', [Site\WorkController::class, 'index'])->name('works.index');
Route::get('/works/{work}', [Site\WorkController::class, 'show'])->name('works.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::resource('works', WorkController::class)->except('show');
    Route::resource('posts', PostController::class)->except('show');
    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('tags', TagController::class)->except('show');
});

require __DIR__.'/auth.php';
