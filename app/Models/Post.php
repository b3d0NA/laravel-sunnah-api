<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id', 'text'];
    protected $appends = ["images"];
    protected $casts = [
        "user_id" => "integer",
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function images(){
        return $this->morphMany(Image::class, "imageable");
    }

    public function getImagesAttribute($value){
        return $this->images()->pluck("url");
    }

    public function getUserAttribute($value){
        return $this->user()->get();
    }

    public function likes(){
        return $this->hasMany(Like::class); 
    }

    public function comments(){
        return $this->hasMany(Comment::class); 
    }
}