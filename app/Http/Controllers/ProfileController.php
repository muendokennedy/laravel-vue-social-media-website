<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\PostAttachmentResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Follower;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;


class ProfileController extends Controller
{
    public function index(Request $request, User $user)
    {
        $isCurrentUserFollower = false;

        if(!auth()->guest()){
            $isCurrentUserFollower = Follower::where([
                'user_id' => $user->id,
                'follower_id' => auth()->id()
            ])->exists();
        }

        $posts = Post::postsForTimeline(auth()->id())->where('user_id', $user->id)->paginate(5);

        $posts = PostResource::collection($posts);

        if($request->wantsJson()){
            return $posts;
        }

        $followers = $user->followers;

        $followings = $user->followings;

        $photos = PostAttachment::query()
                        ->where('mime', 'like', 'image/%')
                        ->where('created_by', $user->id)->get();


        $followerCount = Follower::where('user_id', $user->id)->count();

        return Inertia::render('Profile/View', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'success' => session('success'),
            'followerCount' => $followerCount,
            'isCurrentUserFollower' => $isCurrentUserFollower,
            'user' => new UserResource($user),
            'posts' => $posts,
            'followers' => UserResource::collection($followers),
            'followings' => UserResource::collection($followings),
            'photos' => PostAttachmentResource::collection($photos)
        ]);
    }
    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request)
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return to_route('profile.home', $request->user())->with('success', 'Your profile details were updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
    public function updateImages(Request $request)
    {
        $data = $request->validate([
            'cover' => ['nullable', 'image'],
            'avatar' => ['nullable', 'image']
        ]);

        $user = $request->user();

        $avatar = $data['avatar'] ?? null;
        $cover = $data['cover'] ?? null;
        $success = null;

        if($cover){
            if($user->cover_path){
                Storage::disk('public')->delete($user->cover_path);
            }
            $path = $cover->store('covers/user-' . $user->id, 'public');
            $user->update(['cover_path' => $path]);

            $success = 'Your cover image was updated';
        }
        if($avatar){
            if($user->avatar_path){
                Storage::disk('public')->delete($user->avatar_path);
            }
            $path = $avatar->store('avatars/user-' . $user->id, 'public');
            $user->update(['avatar_path' => $path]);

            $success = 'Your avatar image was updated';
        }

        return back()->with('success', $success);

    }
}
