<?php

namespace App\Models;

use App\Http\Enums\GroupUserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function adminUsers(): BelongsToMany
    {
       return $this->belongsToMany(User::class, 'group_users')->wherePivot('role', GroupUserRole::ADMIN->value);
    }
}
