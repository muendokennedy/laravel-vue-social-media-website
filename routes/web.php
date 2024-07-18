<?php

use App\Http\Controllers\GroupController;
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
    // Profile
     Route::post('/profile/update-profile-images', [ProfileController::class, 'updateImages'])->name('profile.updateImages');
     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
     // Post
     Route::post('/post', [PostController::class, 'store'])->name('post.store');
     Route::put('/post/{post}', [PostController::class, 'update'])->name('post.update');
     Route::delete('/post/{post}', [PostController::class, 'destroy'])->name('post.destroy');
     Route::get('/post/download/{attachment}', [PostController::class, 'downloadAttachment'])->name('post.download');
     Route::post('/post/{post}/reaction', [PostController::class, 'postReaction'])->name('post.reaction');
     // Comment
     Route::post('/post/{post}/comment', [PostController::class, 'createComment'])->name('post.comment.create');
     Route::delete('/comment/{comment}', [PostController::class, 'deleteComment'])->name('comment.delete');
     Route::put('/comment/{comment}', [PostController::class, 'updateComment'])->name('comment.update');
     Route::post('/comment/{comment}/reaction', [PostController::class, 'commentReaction'])->name('comment.reaction');
     // Group
     Route::post('/group', [GroupController::class, 'store'])->name('group.create');

 });

require __DIR__.'/auth.php';
