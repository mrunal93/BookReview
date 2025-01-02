<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * php artisan make:controller BookController --resource
     */
    public function index( Request $request)
    {
        $title = $request->input("title");
        $filter = $request->input("filter", '');
        $books = Book::when(
            $title,
            fn($query,$title) => $query->title($title)
            );

            // Match is the type of stament just like switch
            $books = match ($filter) {
                'popular_last_month' => $books->popularLastMonth(),
                'popular_last_6month' => $books->Popular6LastMonth(),
                'highest_rated_last_month' => $books->HigestRatedLastMonth(),
                'highest_rated_last_6month' => $books->HigestRated6LastMonth(),
                default => $books->latest()->WithAvgRating()->WithReviewsCount()
            };

            // $books = $books->get();

            $cacheKey = 'books:' . $filter . ':' . $title;
            $books =

          //  cache()->remember($cacheKey,3600,function() use($books){

          cache()->remember(
            $cacheKey,
            3600,
            fn() =>
            $books->get()
        );



        return view("books.index",['books'=> $books]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $cachekey = 'book:'. $id;

        $book =cache()->remember( $cachekey,
         3600,
         fn() =>
         Book::with(['reviews' => fn($query) => $query-> latest()])
         ->WithAvgRating()->WithReviewsCount()->findOrFail($id)
    );
        return view('books.show',
        [
            'book' => $book]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
