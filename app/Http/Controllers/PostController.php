<?php

namespace App\Http\Controllers;

use App\Http\Enums\PostReactionEnum;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\PostReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
    }

    /**
     * Update the specified resource in storage.
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
        // TODO
        if($post->user_id !== auth()->user()->id){
            return response("You do not have permission to delete this post", 403);
        }

        $post->delete();

        return back();
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
        'reaction' => [Rule::enum(PostReactionEnum::class)]
       ]);

       $userId = $user->id;

       $reaction = PostReaction::where([
        'user_id' => $userId,
        'post_id' => $post->id
       ])->first();

       if($reaction){
        $hasReaction = false;
        $reaction->delete();
       } else {
        $hasReaction = true;
           PostReaction::create([
            'post_id' => $post->id,
            'user_id' => $userId,
            'type' => $data['reaction']
           ]);
       }

       $reactions = PostReaction::where('post_id', $post->id)->count();

       return response([
        'num_of_reactions' =>  $reactions,
        'current_user_has_reaction' => $hasReaction
       ]);
    }

    public function createComment(Request $request, Post $post)
    {
        $data = $request->validate([
            'comment' => ['required']
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'comment' => $data['comment'],
            'user_id' => auth()->user()->id
        ]);

        return response()->json(new CommentResource($comment), 201);
    }
}
