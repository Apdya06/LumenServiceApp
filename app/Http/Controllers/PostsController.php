<?php
namespace App\Http\Controllers;

use App\Models\Post;

class PostsController extends Controller{
    protected $posts, $outPut;

    public function __construct()
    {
        $this->posts = Post::OrderBy("id", "DESC")->paginate(10);
        $this->outPut = [
            "message" => "posts",
            "result" => $this->posts
        ];
    }

    public function index()
    {

        return response()->json($this->outPut, 200);
    }
}
