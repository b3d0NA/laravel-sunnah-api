<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $appends = ["profile"];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function image(){
        return $this->morphOne(Image::class, "imageable");
    }

    public function imageUrl(){
        if(count($this->image()->get()) > 0){
            return $this->image()->first()->url;
        }else{
            return Image::DEFAULT_IMAGE;
        }
    }

    public function getProfileAttribute(){
        return $this->imageUrl();
    }

    public function posts(){
        return $this->hasMany(Post::class);
    }

    public function isUserLikedPost(Post $post){
        return Like::where("post_id", $post->id)
            ->where("user_id", $this->id)
            ->exists();
    }
}