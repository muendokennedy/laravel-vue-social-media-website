<?php

namespace App\Http\Requests;

use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdatePostRequest extends StorePostRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // TODO may be change later

        // $post = Post::where('id', $this->input('id'))->where('user_id', auth()->user()->id)->first();

        // $post = Post::where([
        //     'id' => $this->input('id'),
        //     'user_id' => auth()->user()->id
        // ])->first();

        $post = $this->route('post');

        // dd($this->all());

        // return !!$post;

        return $post->user_id === auth()->user()->id;
    }
}
