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
                    ->withCount('comments')
                    ->with([
                        'latest5Comments' => function($query)  use ($userId) {
                            $query->whereNull('parent_id')
                            ->withCount('reactions')
                            ->withCount('comments')
                            ->with([
                                'reactions' => function($query) use ($userId){
                                $query->where('user_id', $userId);
                            }]);
                        },
                        'reactions' => function($query) use ($userId){
                        $query->where('user_id', $userId);
                    }])
                    ->latest()->paginate(20);

        return Inertia::render('Home', [
            'posts' => PostResource::collection($posts)
        ]);
    }
}
