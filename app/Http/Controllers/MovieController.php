<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;

class MovieController extends Controller
{
    public function store(StoreMovieRequest $request)
    {
        $movie = Movie::create($request->validated());
        return response()->json($movie, 201);
    }


    public function show($id)
    {
        $movie = Movie::findOrFail($id);
        return response()->json($movie);
    }

    public function update(UpdateMovieRequest $request, $id)
    {
        $movie = Movie::findOrFail($id);
        $movie->update($request->validated());
        return response()->json($movie);
    }

 
    public function destroy($id)
    {
        $movie = Movie::findOrFail($id);
        $movie->delete();
        return response()->json(null, 204);
    }

    public function index()
    {
        $movies = Movie::all();
        return response()->json($movies);
    }
}
