<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Book extends Model
{

use HasFactory;
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeTitle(Builder $query , string $title): Builder
    {
        return $query-> where('title','LIKE','%' . $title . '%');
    }

    // --------------------------------------This i can use for product -------------------------------------------------
    // tinker ----------------------------------- \App\Models\Book::popular('2023-01-01','2023-03-30')->get();
    public function scopeWithReviewsCount(Builder $query, $from = null , $to = null): Builder| QueryBuilder
    {
        return $query->withCount([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ]);
    }
    public function scopeWithAvgRating(Builder $query, $from = null , $to = null): Builder| QueryBuilder
    {
        return $query->withAvg([
            'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ],'rating');
    }

    public function scopePopular(Builder $query, $from = null , $to = null): Builder | QueryBuilder{
        return $query->WithReviewsCount()
        ->orderBy('reviews_count','desc');
    }

    public function scopehigestRated(Builder $query,$from = null,$to = null): Builder | QueryBuilder{
        return $query->WithAvgRating()->orderBy('reviews_avg_rating','desc');
    }


    // tinker -------------------- \App\Models\Book::higestRated('2023-02-01','2023-03-30')->popular('2023-01-01','2023-03-30')->get();

    public function scopeMinRevews(Builder $query,int $minReviews): Builder | QueryBuilder {
            return $query->having('reviews_count','>=', $minReviews);
    }

    // creating daterange filter

    private function dateRangeFilter(Builder $query, $from = null , $to = null){
        if ($from  && !$to) {
            $query->where('created_at','>=', $from);
        } elseif (!$from && $to) {
            $query->where('created_at','<=', $to);
        }
        elseif ($from && $to) {
            $query->wherebetween('created_at',[ $from, $to]);
        }

    }

    public function scopePopularLastMonth(Builder $query): Builder | QueryBuilder {

        return $query ->popular(now()->subMonth(),now())
                        ->higestRated(now()->subMonth(),now())
                ->MinRevews(1);
    }
    public function scopePopular6LastMonth(Builder $query): Builder | QueryBuilder {

        return $query ->popular(now()->subMonth(6),now())
                        ->higestRated(now()->subMonth(6),now())
                        ->MinRevews(5);
    }

    public function scopeHigestRatedLastMonth(Builder $query): Builder | QueryBuilder {

        return $query ->higestRated(now()->subMonth(),now())
        ->popular(now()->subMonth(),now())

                        ->MinRevews(1);
    }
    public function scopeHigestRated6LastMonth(Builder $query): Builder | QueryBuilder {

        return $query ->higestRated(now()->subMonth(6),now())
        ->popular(now()->subMonth(6),now())

                        ->MinRevews(5);
    }

    protected static function booted(){
        static::updated(fn(Book $book)=> cache()->forget('book:' . $book-> id));
        static::deleted(fn(Book $book)=> cache()->forget('book:' . $book-> id));
    }
}
