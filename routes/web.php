<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('home');
Route::get('/u/{user:username}', [ProfileController::class, 'index'])->name('profile.home');

 Route::middleware('auth')->group(function () {
     Route::post('/profile/update-profile-images', [ProfileController::class, 'updateImages'])->name('profile.updateImages');
     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
     Route::post('/post', [PostController::class, 'store'])->name('post.store');
     Route::put('/post/{post}', [PostController::class, 'update'])->name('post.update');
 });

require __DIR__.'/auth.php';
