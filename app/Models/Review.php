<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{

use HasFactory;

protected $fillable = ['review', 'rating'];
    public function Book()
    {

        // if you want to use different forkey name the you can use below methord
        // return $this->belongsTo(Book::class,'forenkeyname');
        return $this->belongsTo(Book::class);
    }


    //If we are using local cashing and want to update with ever there is an update then we can use this  where
    // where ever there will be an intry it will forget the cache data and fetch it from database
    protected static function booted(){
        static::updated(fn(Review $review)=> cache()->forget('book:' . $review-> book_id));
        static::deleted(fn(Review $review)=> cache()->forget('book:' . $review-> book_id));
        static::created(fn(Review $review) => cache()->forget('book:' . $review->book_id));
    }
}
