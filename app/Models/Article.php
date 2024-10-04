<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

//    public function comments()
//    {
//        return $this->hasMany(Comment::class);
//    }
//
//    public function votes()
//    {
//        return $this->hasMany(Vote::class);
//    }

    protected function serializeDate(DateTimeInterface $date){
        return $date->format('F d, Y h:i:s');
    }
}
