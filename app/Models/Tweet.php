<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{
    /** @use HasFactory<\Database\Factories\TweetFactory> */
      public function liked()
  {
      return $this->belongsToMany(User::class)->withTimestamps();
  }

    use HasFactory;
      protected $fillable = ['tweet'];

        // 🔽 1対多の関係
  public function comments()
  {
    return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }
  // app/Models/Tweet.php の中に追記
  public function reposts(){ return $this->hasMany(\App\Models\Repost::class); }

}
