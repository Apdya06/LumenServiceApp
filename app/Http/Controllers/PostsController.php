<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Gate;

class PostsController extends Controller{
    protected $model;

    public function __construct(Post $post)
    {
        $this->model = $post;
    }

    private function validation(Request $request)
    {
        $rules = [
            'title' => 'required|min:5',
            'content' => 'required|min:10',
            'status' => 'required|in:draft,published',
            'user_id' => 'required|exists:users,id',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return response()->json(['error' => $validator->errors()], 400);
    }

    public function index(Request $request)
    {
        if (Gate::denies('read-post'))
        {
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized'
            ], 403);
        }
        return parent::index($request);
    }

    // Overridding method di bawah
    public function store(Request $request)
    {
        if ($this->validation($request)) return $this->validation($request); // validasi request
        return parent::store($request);
    }

    public function update(Request $request, $id)
    {
        if ($this->validation($request)) return $this->validation($request);
        return parent::update($request, $id);
    }
}
