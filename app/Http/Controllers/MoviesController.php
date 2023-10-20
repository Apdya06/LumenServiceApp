<?php
namespace App\Http\Controllers;

use App\Models\Movies;

class MoviesController extends Controller{
    protected $movies, $outPut;

    public function __construct()
    {
        $this->movies = Movies::OrderBy("id", "DESC")->paginate(10);
        $this->outPut = [
            "message" => "movies",
            "result" => $this->movies
        ];
    }

    public function index(){
        return response()->json($this->outPut);
    }

    public function show($titleId){
        foreach ($this->movies as $movie) {
            if ($movie['id'] == $titleId) {
                return response()->json($movie);
            }
        }
        return response()->json(['message' => 'Movie not found'], 404);
    }
}
