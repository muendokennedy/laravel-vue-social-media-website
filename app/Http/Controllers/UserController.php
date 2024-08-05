<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\User;
use App\Notifications\UserFollowed;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function followUser(Request $request, User $user)
    {
        $data = $request->validate([
            'follow' => 'boolean'
        ]);

        if($data['follow']){
            Follower::create([
                'user_id' => $user->id,
                'follower_id' => auth()->id()
            ]);

            $message = "You followed {$user->name} successfully";

        } else {
            Follower::query()->where([
                'user_id' => $user->id,
                'follower_id' => auth()->id()
            ])->delete();

            $message = "You unfollowed {$user->name} successfully";
        }

        $user->notify(new UserFollowed(auth()->user(), $data['follow']));

        return back()->with('success', $message);
    }
}
