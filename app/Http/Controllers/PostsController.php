<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PostsController extends Controller{
    protected $model;

    public function __construct(Post $post)
    {
        $this->model = $post;
    }

    private function validation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:5',
            'content' => 'required|min:10',
            'status' => 'required|in:draft,published',
        ]);

        if (Auth::user()->role == 'admin') {
            $validator->sometimes('user_id', 'required|exists:users,id', function ($input) {
                return Auth::user()->id;
            });
        }

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
    }

    public function indexjson()
    {
        if (Gate::denies('read-post'))
        {
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'Your role is unauthorized to read any posts'
            ], 403);
        }
        return parent::indexjson();
    }

    public function indexxml()
    {
        if (Gate::denies('read-post'))
        {
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'Your role is unauthorized to read any posts'
            ], 403);
        }
        return parent::indexjson();
    }

    public function storejson(Request $request) {
        if ($this->validation($request)) return $this->validation($request); // validasi request

        if (Gate::denies('store-post')) {
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'Your role is unauthorized to store any posts'
            ], 403);
        }
        $data = $request->all();

        if($request->hasFile('image')){
            $firstName = str_replace(' ', '_', Profile::where('user_id', Auth::user()->id)->value('first_name'));
            $lastName = str_replace(' ', '_', Profile::where('user_id', Auth::user()->id)->value('last_name'));
            $imgName = Auth::user()->id . '_' . $firstName . '_' . $lastName . Str::random(15);
            $request->file('image')->move(storage_path('uploads/posts_images'), $imgName);
            $data['image'] = $imgName;
        }

        if (Auth::user()->role === 'admin') {
            return response()->json($this->model::create($data), 200);
        } else {
            $data['user_id'] = Auth::user()->id;
            return response()->json($this->model::create($data), 200);
        }
    }

    public function imagejson($id)
    {
        $imageName = $this->model::where('user_id', Auth::user()->id)->find($id);

        if (Gate::denies('detail-post', $imageName)) {
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized to read this post'
            ], 403);
        }

        $imagePath =  storage_path('uploads/posts_images' . '/' . $imageName->image);
        if (file_exists($imagePath))
        {
            $file = file_get_contents($imagePath);
            return response($file, 200)->header('Content-Type', 'image/jpeg');
        }

        return response()->json(["message" => "Image not found"], 404);
    }

    public function updatejson(Request $request, $id)
    {
        if ($this->validation($request)) return $this->validation($request);
        return parent::updatejson($request, $id);
    }

    public function updatexml(Request $request, $id)
    {
        if ($this->validation($request)) return $this->validation($request);
        return parent::updatexml($request, $id);
    }
}
