<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
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

        return Inertia::render('Home', [
            'posts' => $posts
        ]);
    }
}
