<?php

namespace App\Http\Controllers;

use App\Http\Enums\ReactionEnum;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\Reaction;
use App\Notifications\CommentDeleted;
use App\Notifications\GroupPostCommentCreated;
use App\Notifications\PostCommentCreated;
use App\Notifications\PostCreated;
use App\Notifications\PostDeleted;
use App\Notifications\ReactionAddedonPost;
use App\Notifications\ReactionAddedonPostComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PostController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        //
        $data = $request->validated();

        $user = $request->user();

        DB::beginTransaction();

        $allFilepaths = [];

        try {

        $post = Post::create($data);

        /**
         * @var \Illuminate\Http\UploadedFile[] $files
         */
        $files = $data['attachments'] ?? [];

        foreach ($files as $file) {

            $path = $file->store('attachments/' . $post->id, 'public');

            $allFilepaths[] = $path;

            PostAttachment::create([
                'post_id' => $post->id,
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
                'created_by' => $user->id
            ]);

        }

        $group = $post->group;

        if($group){
            $users = $group->approvedUsers()->where('users.id', '!=', $user->id)->get();
            Notification::send($users, new PostCreated($post, $user, $group));

        } else {

            $followers = $user->followers;

            Notification::send($followers, new PostCreated($post, $user));

        }


        DB::commit();

    } catch (\Exception $e) {
        foreach ($allFilepaths as $path) {
            Storage::disk('public')->delete($path);
        }
        DB::rollback();

        throw $e;
    }

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
        $post->loadCount('reactions');

        $post->load([
            'comments' => function($query){
                $query->withCount('reactions');
            }
        ]);

        return Inertia::render('Post/View', [
            'post' => new PostResource($post)
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @throws \Exception
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        //

        $data = $request->validated();

        // dd($data);


        $user = $request->user();

        DB::beginTransaction();

        $allFilepaths = [];

        try {

        $post->update($data);

        $deleted_ids = $data['deleted_file_ids'] ?? [];

        $attachments = PostAttachment::query()
        ->where('post_id', $post->id)
        ->whereIn('id', $deleted_ids)
        ->get();

        foreach ($attachments as $attachment) {
            $attachment->delete();
        }
        /**
         * @var \Illuminate\Http\UploadedFile[] $files
         */
        $files = $data['attachments'] ?? [];

        foreach ($files as $file) {

            $path = $file->store('attachments/' . $post->id, 'public');

            $allFilepaths[] = $path;

            PostAttachment::create([
                'post_id' => $post->id,
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
                'created_by' => $user->id
            ]);

        }

        DB::commit();

    } catch (\Exception $e) {
        foreach ($allFilepaths as $path) {
            Storage::disk('public')->delete($path);
        }
        DB::rollback();

        throw $e;
    }

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $currentUserId = auth()->id();

        if($post->isOwner($currentUserId) || $post->group && $post->group->isAdmin($currentUserId)){
            $post->delete();

            if(!$post->isOwner($currentUserId)){
                $post->user->notify(new PostDeleted($post->group, auth()->user()->name));
            }
            return back();
        }
        return response("You do not have permission to delete this post", 403);
    }

    public function downloadAttachment(PostAttachment $attachment)
    {
       // TODO
       // Check if the user has permission to download that attachment

       return response()->download(Storage::disk('public')->path($attachment->path), $attachment->name);
    }

    public function postReaction(Post $post, Request $request)
    {
       $user = auth()->user();

       $data = $request->validate([
        'reaction' => [Rule::enum(ReactionEnum::class)]
       ]);

       $userId = $user->id;

       $reaction = Reaction::where([
        'user_id' => $userId,
        'reactionable_id' => $post->id,
        'reactionable_type' => Post::class,
       ])->first();

       if($reaction){
        $hasReaction = false;
        $reaction->delete();
       } else {
        $hasReaction = true;
        $reaction = Reaction::create([
            'reactionable_id' => $post->id,
            'reactionable_type' => Post::class,
            'user_id' => $userId,
            'type' => $data['reaction']
           ]);

           if(!$post->isOwner(auth()->id())){

               $postOwner = $reaction->reactionable->user;

               Notification::send($postOwner, new ReactionAddedonPost($reaction, $postOwner, auth()->user()->name));
           }

       }


       $reactions = Reaction::where([
        'reactionable_id' => $post->id,
        'reactionable_type' => Post::class,
       ])->count();

       return response([
        'num_of_reactions' =>  $reactions,
        'current_user_has_reaction' => $hasReaction
       ]);
    }

    public function createComment(Request $request, Post $post)
    {
        $data = $request->validate([
            'comment' => ['required'],
            'parent_id' => ['nullable', 'exists:comments,id']
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'comment' => nl2br($data['comment']),
            'user_id' => auth()->user()->id,
            'parent_id' => $data['parent_id'] ?: null
        ]);

        $postOwner = $comment->post->user;

        Notification::send($postOwner, new PostCommentCreated($comment, $postOwner));

        return response()->json(new CommentResource($comment), 201);
    }

    public function deleteComment(Comment $comment)
    {
        $post = $comment->post;

        $currentUserId = auth()->id();

        if($comment->isOwner($currentUserId) || $post->isOwner($currentUserId)){
            $comment->delete();

            if(!$comment->isOwner($currentUserId)){
                $comment->user->notify(new CommentDeleted($comment, $post, $comment->post->group->name, auth()->user()->name));
            }

            return response()->noContent();
        }
        return response('You do not have permission to delete this comment', 403);
    }

    public function updateComment(UpdateCommentRequest $request, Comment $comment)
    {
       $data = $request->validated();

       $comment->update([
        'comment' => nl2br($data['comment'])
       ]);

       return new CommentResource($comment);
    }

    public function commentReaction(Request $request, Comment $comment)
    {
       $user = auth()->user();

       $data = $request->validate([
        'reaction' => [Rule::enum(ReactionEnum::class)]
       ]);

       $userId = $user->id;

       $reaction = Reaction::where([
        'user_id' => $userId,
        'reactionable_id' => $comment->id,
        'reactionable_type' => Comment::class,
       ])->first();

       if($reaction){
        $hasReaction = false;
        $reaction->delete();
       } else {
        $hasReaction = true;
           $reaction = Reaction::create([
            'reactionable_id' => $comment->id,
            'reactionable_type' => Comment::class,
            'user_id' => $userId,
            'type' => $data['reaction']
           ]);

           if(!$comment->isOwner(auth()->id())){

            $commentOwner = $reaction->reactionable->user;

            Notification::send($commentOwner, new ReactionAddedonPostComment($reaction, $comment, $commentOwner, auth()->user()->name));
        }
       }


       $reactions = Reaction::where([
        'reactionable_id' => $comment->id,
        'reactionable_type' => Comment::class,
       ])->count();

       return response([
        'num_of_reactions' =>  $reactions,
        'current_user_has_reaction' => $hasReaction
       ]);
    }
}
