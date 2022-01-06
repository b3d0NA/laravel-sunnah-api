<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ["imageable_id", "imageable_type", "url"];

    const DEFAULT_IMAGE = "http://127.0.0.1:8000/images/muslim.svg";

    public function imageable(){
        return $this->morphTo();
    }
}