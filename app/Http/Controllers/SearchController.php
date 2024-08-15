<?php

namespace App\Http\Controllers;

use App\Http\Resources\GroupResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Group;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    //

    public function search(Request $request)
    {
        $search = $request->get('keywords');

        $users = User::query()
                    ->where('name', 'like', "%$search%")
                    ->orWhere('username', 'like', "%$search%")
                    ->get();

        $groups = Group::query()
                    ->where('name', 'like', "%$search%")
                    ->orWhere('about', 'like', "%$search%")
                    ->get();

        $posts = Post::query()
                    ->where('body', 'like', "%$search%")
                    ->paginate(5);

        $posts = PostResource::collection($posts);

        if($request->wantsJson()){
            return $posts;
        }

        return inertia('Search', [
            'posts' => $posts,
            'users' => UserResource::collection($users),
            'groups' => GroupResource::collection($groups)
        ]);
    }
}
