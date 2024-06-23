<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // TODO may be change later

        // $post = Post::where('id', $this->input('id'))->where('user_id', auth()->user()->id)->first();

        $post = Post::where([
            'id' => $this->input('id'),
            'user_id' => auth()->user()->id
        ])->first();

        return !!$post;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'body' => 'nullable | string',
        ];
    }
}
