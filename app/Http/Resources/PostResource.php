<?php

namespace App\Http\Resources;

use App\Http\Resources\CommentResource;
use App\Http\Resources\PostAttachmentResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $comments = $this->comments;

        return [
            'id' => $this->id,
            'body' => $this->body,
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->diffForHumans(),
            'time_difference' => $this->created_at->diffInSeconds($this->updated_at),
            'user' => new UserResource($this->user),
            'group' => $this->group,
            'attachments' => PostAttachmentResource::collection($this->postAttachments),
            'num_of_reactions' => $this->reactions_count,
            'num_of_comments' => count($comments),
            'current_user_has_reaction' => $this->reactions->count() > 0,
            'comments' => self::convertCommentsIntoTree($comments)
        ];
    }

    /**
     * Convert the comments into a comment tree.
     *
     * @param \App\Models\Comment[] $comments
     * @param $parentId
     * @return array
     */

    private static function convertCommentsIntoTree($comments, $parentId = null, ): array
    {
        $commentTree = [];

        foreach ($comments as $comment) {

           if($comment->parent_id === $parentId){

            $children = self::convertCommentsIntoTree($comments, $comment->id);

            $comment->childComments = $children;

            $comment->numOfComments = collect($children)->sum('numOfComments') + count($children);

            $commentTree[] = new CommentResource($comment);
           }
        }

        return $commentTree;
    }
}
