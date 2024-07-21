<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Group extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'auto_approval',
        'about',
        'user_id',
        'deleted_by',
        'cover_path',
        'thumbnail_path'
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function currentUserGroup(): HasOne
    {
       return $this->hasOne(GroupUser::class)->where('user_id', auth()->id());
    }

    public function isAdmin($userId): bool
    {
       return $this->currentUserGroup?->user_id === $userId;
    }
}
