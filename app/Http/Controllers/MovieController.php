<?php

namespace App\Http\Controllers;

use App\Http\Resources\MovieCollection;
use App\Http\Resources\MovieResource;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return MovieCollection
     */
    public function index()
    {
        $movies = Movie::with(['category', 'actors'])->get(['id', 'title', 'description', 'release_date', 'director', 'producer', 'category_id']);
        return new MovieCollection($movies);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $movie = Movie::create([
                'title' => $request->title,
                'description' => $request->description,
                'release_date' => $request->release_date,
                'director' => $request->director,
                'producer' => $request->producer,
                'category_id' => $request->category
            ]);
            if ($request->has('actors')) $movie->actors()->attach($request->actors);
            return $movie;
        });
    }

    /**
     * Display the specified resource.
     *
     * @param Movie $movie
     * @return MovieResource
     */
    public function show(Movie $movie)
    {
        return new MovieResource($movie->load('actors', 'category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Movie $movie
     * @return Response
     */
    public function update(Request $request, Movie $movie)
    {
        return DB::transaction(function () use ($movie, $request) {
            $movie->update([
                'title' => $request->title,
                'description' => $request->description,
                'release_date' => $request->release_date,
                'director' => $request->director,
                'producer' => $request->producer,
                'category_id' => $request->category
            ]);
            if ($request->has('actors')) {
                $movie->actors()->detach();
                $movie->actors()->attach($request->actors);
            }
            return $movie;
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Movie $movie
     * @return Response
     */
    public function destroy(Movie $movie)
    {
        $deleted = DB::transaction(function () use ($movie) {
            $r = $movie->actors()->count() > 0 ? $movie->actors()->detach() : true;
            $m = $movie->delete();
            return $r && $m;
        });
        if ($deleted) {
            return response('Deleted', 204)->header('Content-Type', 'text/plain');
        }
        abort(404);
    }
}
