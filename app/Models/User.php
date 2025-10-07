<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{

      // 🔽 1対多の関係

    public function follows()
  {
    return $this->belongsToMany(User::class, 'follows', 'follow_id', 'follower_id');
  }

  public function followers()
  {
    return $this->belongsToMany(User::class, 'follows', 'follower_id', 'follow_id');
  }
  public function comments()
  {
    return $this->hasMany(Comment::class);
  }
  
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
      public function likes()
  {
      return $this->belongsToMany(Tweet::class)->withTimestamps();
  }
    protected $fillable = [
        'name',
        'email',
        'password',
        'bio', 
        'avatar_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function tweets()
  {
    return $this->hasMany(Tweet::class);
  }
    // app/Models/User.php の中に追記
    public function reposts(){ return $this->hasMany(\App\Models\Repost::class); }

    protected $appends = ['avatar_url'];

    public function getAvatarUrlAttribute()
{
    return $this->avatar_path
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->avatar_path)
        : 'https://placehold.co/120x120?text=User';
}

}
