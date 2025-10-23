<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'description', 'video_path', 'thumbnail_path', 'views'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->where('liked', true)->exists();
    }

    public function isDislikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->where('liked', false)->exists();
    }
}