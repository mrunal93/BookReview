<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{

use HasFactory;
    public function Book()
    {

        // if you want to use different forkey name the you can use below methord
        // return $this->belongsTo(Book::class,'forenkeyname');
        return $this->belongsTo(Book::class);
    }
}
