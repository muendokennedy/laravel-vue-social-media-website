<?php

namespace App\Http\Controllers;

use App\Http\Enums\GroupUserStatus;
use App\Http\Resources\GroupResource;
use App\Http\Resources\PostResource;
use App\Models\Group;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    //
    public function index(Request $request)
    {
        $userId = auth()->user()->id;

        $posts = Post::query()
                    ->withCount('reactions')
                    ->with([
                        'comments' => function($query){
                            $query->withCount('reactions');
                        },
                        'reactions' => function($query) use ($userId){
                        $query->where('user_id', $userId);
                    }])
                    ->latest()->paginate(5);

        $posts = PostResource::collection($posts);

        if($request->wantsJson()){
            return $posts;
        }

        $groups = Group::query()
                    ->select(['groups.*', 'group_users.role', 'group_users.status'])
                    ->join('group_users', 'group_users.group_id', 'groups.id')
                    ->where('group_users.user_id', auth()->id())
                    // ->where('status', GroupUserStatus::APPROVED->value)
                    ->orderBy('group_users.role')
                    ->orderBy('name', 'desc')
                    ->get();

        return Inertia::render('Home', [
            'posts' => $posts,
            'groups' => GroupResource::collection($groups)
        ]);
    }
}
