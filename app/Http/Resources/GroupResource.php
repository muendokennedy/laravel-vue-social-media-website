<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class GroupResource extends JsonResource
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
            'name' => $this->name,
            'status' => $this->currentUseGroup?->status,
            'role' => $this->currentUseGroup?->role,
            'slug' => $this->slug,
            'cover_url' => '/images/coverimageholder.webp',
            'thumbnail_url' => '/man1.jpg',
            'description' => Str::words($this->about, 10),
            'auto_approval' => $this->auto_approval,
            'about' => $this->about,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
