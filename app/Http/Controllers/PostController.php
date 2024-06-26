<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Models\PostAttachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        $post->update($request->validated());

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
}
