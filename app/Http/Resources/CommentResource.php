<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at->diffForHumans(),
            'num_of_reactions' => $this->reactions_count,
            'comments' => $this->childComments,
            'num_of_comments' => $this->numOfComments,
            'current_user_has_reaction' => $this->reactions->count() > 0,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'username' => $this->user->username,
                'avatar_url' => $this->user->avatar_path ? Storage::url($this->user->avatar_path) : '/images/useravatar4.webp'
            ]
        ];
    }
}
