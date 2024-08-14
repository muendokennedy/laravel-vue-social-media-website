<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;



Route::get('/', [HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('home');
Route::get('/u/{user:username}', [ProfileController::class, 'index'])->name('profile.home');
Route::get('/g/{group:slug}', [GroupController::class, 'groupProfile'])->name('group.profile');

Route::get('/group/confirm-invitation/{token}', [GroupController::class, 'confirmInvitation'])->name('group.confirmInvitation');


 Route::middleware('auth')->group(function () {
     // Group
     Route::post('/group', [GroupController::class, 'store'])->name('group.create');
     Route::put('/group/{group:slug}', [GroupController::class, 'update'])->name('group.update');
     Route::post('/group/update-group-images/{group:slug}', [GroupController::class, 'updateImages'])->name('group.updateImages');
     Route::post('/group/invite-user/{group:slug}', [GroupController::class, 'inviteUser'])->name('group.inviteUser');
     Route::post('/group/join/{group:slug}', [GroupController::class, 'joinGroup'])->name('group.join');
     Route::post('/group/approve-request/{group:slug}', [GroupController::class, 'approveRequest'])->name('group.approveRequest');
     Route::delete('/group/remove-user/{group:slug}', [GroupController::class, 'deleteUser'])->name('group.removeUser');
     Route::post('/group/change-group-role/{group:slug}', [GroupController::class, 'changeGroupRole'])->name('group.changeRole');
     // Profile
     Route::post('/profile/update-profile-images', [ProfileController::class, 'updateImages'])->name('profile.updateImages');
     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
     // User
     Route::post('/user/follow/{user}', [UserController::class, 'followUser'])->name('user.follow');
     // Post
     Route::get('/post/{post}', [PostController::class, 'show'])->name('post.show');
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
     // Open AI
     Route::post('/ai-post', [PostController::class, 'generatePostContentUsingOpenAI'])->name('post.ai.generate');

 });

require __DIR__.'/auth.php';
