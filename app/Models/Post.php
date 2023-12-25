<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;


    function author()
    {
        return $this->belongsTo(Author::class, 'author_id');
    }

    function customers()
    {
        return $this->hasMany(Customer::class);
    }
    function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
