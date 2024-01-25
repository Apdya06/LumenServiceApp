<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;

    /**
     * @OA\Post(
     *     path="/posts",
     *     summary="Create a new post",
     *     tags={"Post"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="title",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="content",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary"
     *                 ),
     *                 @OA\Property(
     *                     property="video",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="title",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="content",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="integer"
     *                 ),
     *                 example={
     *                              "title": "Post Title",
     *                              "status": "published",
     *                              "content": "Post Content",
     *                              "user_id": "1"
     *                 }
     *             )
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/xml",
     *             @OA\Schema(
     *                 @OA\Xml(name="Post"),
     *                 @OA\Property(
     *                     property="title",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="content",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="integer"
     *                 ),
     *                 example="<post><title>Post Title</title><status>published</status><content>Post Content</content><user_id>1</user_id></post>"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/xml"
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     * @OA\Get(
     *     path="/posts",
     *     summary="Get all posts",
     *     tags={"Post"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/xml",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/xml",
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     * @OA\Get(
     *     path="/posts/{id}",
     *     summary = "Get post by id",
     *     tags={"Post"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/xml",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/xml",
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     * @OA\Put(
     *     path="/posts/{id}",
     *     summary="Update a post",
     *     tags={"Post"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="title",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="content",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary"
     *                 ),
     *                 @OA\Property(
     *                     property="video",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="title",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="content",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="integer"
     *                 ),
     *                 example={
     *                              "title": "Post Title Update",
     *                              "status": "published",
     *                              "content": "Post Content Update",
     *                              "user_id": "1"
     *                 }
     *             )
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/xml",
     *             @OA\Schema(
     *                 @OA\Xml(name="Post"),
     *                 @OA\Property(
     *                     property="title",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="content",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="integer"
     *                 ),
     *                 example="<post><title>Post Title Update</title><status>published</status><content>Post Content Update</content><user_id>1</user_id></post>"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/xml",
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     * @OA\Delete(
     *     path="/posts/{id}",
     *     summary="Delete a post",
     *     tags={"Post"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/xml",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         ),
     *         @OA\MediaType(
     *             mediaType="application/xml",
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */

class PostsController extends Controller
{
    protected $model;
    protected $valid;

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
            'image' => 'sometimes|file',
            'video' => 'sometimes|file',
        ]);

        if (Auth::user()->role == 'admin') {
            $validator->sometimes('user_id', 'required|exists:users,id', function ($input) {
                return Auth::user()->id;
            });
        }

        if ($validator->fails()) {
            $this->valid = ['error' => $validator->errors()];
            return false;
        }
        return true;
    }

    public function index(Request $request) {
        $accHeader = $request->headers->get('Accept');
        if($accHeader === '*/*' || empty($accHeader) ||
            ($accHeader != 'application/json' && $accHeader != 'application/xml')) {
            return response('Not Accepttable', 404);
        }

        if (Gate::denies('read-post')) {
            if ($accHeader == 'application/json') {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'Your role is unauthorized to read any posts'
                ], 403);
            } else if ($accHeader == 'application/xml') {
                $xml = new \SimpleXMLElement('<Response/>');
                $xml->addChild('success', 'false');
                $xml->addChild('status', 403);
                $xml->addChild('message', 'Your role is unauthorized to read any posts');
                return response($xml->asXML(), 403)->header('Content-Type', 'application/xml');
            }
        }

        $this->multi = Auth::user()->role === 'admin' ?
            $this->model::OrderBy("id", "DESC")->paginate(2)->toArray() :
            $this->model::Where(['user_id' => Auth::user()->id])->OrderBy("id", "DESC")->paginate(2)->toArray();
            $response = [
                'total_count' => $this->multi['total'],
                'limit' =>  $this->multi['per_page'],
                'pagination' => [
                    'next_page' => $this->multi['next_page_url'],
                    'prev_page' => $this->multi['prev_page_url'],
                    'current_page' => $this->multi['current_page'],
                ],
                'data' => $this->multi['data']
            ];

        if($accHeader == 'application/xml') {
            $xml = new \SimpleXMLElement('<'.class_basename($this->model).'/>');
            foreach($response['data'] as $item) {
                $xmlItem = $xml->addChild(class_basename($this->model) . 'item');
                $xmlItem->addChild('total_count', $response['total_count']);
                $page = $xmlItem->addChild('pagination');
                foreach ($response['pagination'] as $key => $value) {
                    $page->addChild($key, $value);
                }
                $xmlItem->addChild('limit', $response['limit']);
                foreach ($item as $key => $value) {
                    $xmlItem->addChild($key, $value);
                }
            }
            return $xml->asXML();
        }
        return $accHeader == 'application/json' ? response()->json($response, 200) : null;
    }

    public function show(Request $request, $id) {
        $accHeader = $request->headers->get('Accept');
        if($accHeader === '*/*' || empty($accHeader) ||
            ($accHeader != 'application/json' && $accHeader != 'application/xml')) {
            return response('Not Accepttable', 404);
        }

        $this->single = Auth::user()->role === 'admin' ?
            $this->model::find($id) :
            $this->model::where('user_id', Auth::user()->id)->find($id) ?? $this->model;

        if (Gate::denies('detail-post', $this->single)) {
            if ($accHeader == 'application/json') {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'You are unauthorized to read this post'
                ], 403);
            } else if ($accHeader == 'application/xml') {
                $xml = new \SimpleXMLElement('<Response/>');
                $xml->addChild('success', 'false');
                $xml->addChild('status', 403);
                $xml->addChild('message', 'You are unauthorized to read this post');
                return response($xml->asXML(), 403)->header('Content-Type', 'application/xml');
            }
        }

        if($accHeader == 'application/xml') {
            $xml = new \SimpleXMLElement('<'.class_basename($this->model).'/>');
            $attributes = $this->single->getAttributes();
            foreach($attributes as $key => $value) {
                $xml->addChild($key, $value);
            }
            return response($xml->asXML(), 200)->header('Content-Type', 'application/xml');
        }
        return $accHeader == 'application/json' ? response()->json($this->single, 200) : null;
    }

    public function store(Request $request) {
        $accHeader = $request->headers->get('Accept');
        $contentTypeHeader = $request->headers->get('Content-Type');

        if($accHeader === '*/*' || empty($accHeader) ||
            ($accHeader !== 'application/json' && $accHeader !== 'application/xml'
            && $contentTypeHeader !== 'application/json' && $contentTypeHeader !== 'application/xml')) {
            return response('Not Accepttable', 404);
        }

        if (Gate::denies('store-post')) {
            if ($accHeader == 'application/json') {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'Your role is unauthorized to store any posts'
                ], 403);
            } else if ($accHeader == 'application/xml') {
                $xml = new \SimpleXMLElement('<Response/>');
                $xml->addChild('success', 'false');
                $xml->addChild('status', 403);
                $xml->addChild('message', 'Your role is unauthorized to store any posts');
                return response($xml->asXML(), 403)->header('Content-Type', 'application/xml');
            }
        }

        if ($accHeader == 'application/xml') {
            $xmlString = $request->getContent();
            $xml = simplexml_load_string($xmlString);
            $data = json_decode(json_encode($xml), true);
            $request = new Request($data);
        }

        if (!$this->validation($request)) {
            if ($accHeader == 'application/json') {
                return response()->json($this->valid, 400);
            } else if ($accHeader == 'application/xml') {
                $errors = $this->valid['error']->all();
                $xml = ArrayToXml::convert(['error' => $errors], 'Response');
                return response($xml, 400)->header('Content-Type', 'application/xml');
            }
        }

        $data = $request->all();
        if ($request->hasFile('image')) {
            $firstName = str_replace(' ', '_', Profile::where('user_id', Auth::user()->id)->value('first_name'));
            $lastName = str_replace(' ', '_', Profile::where('user_id', Auth::user()->id)->value('last_name'));
            $imgName = Auth::user()->id . '_' . $firstName . '_' . $lastName . 'img' . Str::random(5);
            $request->file('image')->move(storage_path('uploads/posts_images'), $imgName);
            $data['image'] = $imgName;
        }

        if ($request->hasFile('video')) {
            $firstName = str_replace(' ', '_', Profile::where('user_id', Auth::user()->id)->value('first_name'));
            $lastName = str_replace(' ', '_', Profile::where('user_id', Auth::user()->id)->value('last_name'));
            $vidName = Auth::user()->id . '_' . $firstName . '_' . $lastName . 'vid' . Str::random(5) . '.mp4';
            $request->file('video')->move(storage_path('uploads/posts_videos'), $vidName);
            $data['video'] = $vidName;
        }

        if (Auth::user()->role !== 'admin') {
            $data['user_id'] = Auth::user()->id;
        }

        if ($accHeader == 'application/json') {
            return response()->json($this->model::create($data), 200);
        }
        if ($accHeader == 'application/xml') {
            if ($contentTypeHeader !== 'application/xml') {
                $newPost = $this->model::create($data);
                $xmlResponse = new \SimpleXMLElement('<' . class_basename($this->model) . '/>');
                foreach ($newPost->getAttributes() as $key => $value) {
                    $xmlResponse->addChild($key, $value);
                }
                return response($xmlResponse->asXML(), 200)->header('Content-Type', 'application/xml');
            }
            $xmlString = $request->getContent();
            $xml = simplexml_load_string($xmlString);
            $data = json_decode(json_encode($xml), true);
            $newPost = $this->model::create($data);
            $xmlResponse = new \SimpleXMLElement('<' . class_basename($this->model) . '/>');
            foreach ($newPost->getAttributes() as $key => $value) {
                $xmlResponse->addChild($key, $value);
            }
            return response($xmlResponse->asXML(), 200)->header('Content-Type', 'application/xml');
        }
    }


    public function image($id)
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
        if (file_exists($imagePath)) {
            $file = file_get_contents($imagePath);
            return response($file, 200)->header('Content-Type', 'image/jpeg');
        }

        return response()->json(["message" => "Image not found"], 404);
    }

    public function video($id)
    {
        $videoName = $this->model::where('user_id', Auth::user()->id)->find($id);

        if (Gate::denies('detail-post', $videoName)) {
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized to read this post'
            ], 403);
        }

        $videoPath =  storage_path('uploads/posts_videos' . '/' . $videoName->video);
        if (file_exists($videoPath)) {
            $file = file_get_contents($videoPath);
            return response($file, 200)->header('Content-Type', 'video/mp4');
        }

        return response()->json(["message" => "Video not found"], 404);
    }

    public function update(Request $request, $id) {
        $accHeader = $request->headers->get('Accept');
        $contentTypeHeader = $request->headers->get('Content-Type');

        if($accHeader === '*/*' || empty($accHeader) ||
            ($accHeader != 'application/json' && $accHeader != 'application/xml'
            && $contentTypeHeader!= 'application/json' && $contentTypeHeader!= 'application/xml')) {
            return response('Not Accepttable', 404);
        }

        if ($accHeader == 'application/xml') {
            $xmlString = $request->getContent();
            $xml = simplexml_load_string($xmlString);
            $data = json_decode(json_encode($xml), true);
            $request = new Request($data);
        }

        if (!$this->validation($request)) {
            if ($accHeader == 'application/json') {
                return response()->json($this->valid, 400);
            } else if ($accHeader == 'application/xml') {
                $errors = $this->valid['error']->all();
                $xml = ArrayToXml::convert(['error' => $errors], 'Response');
                return response($xml, 400)->header('Content-Type', 'application/xml');
            }
        }

        $this->single = Auth::user()->role === 'admin' ?
            $this->model::find($id) :
            $this->model::where('user_id', Auth::user()->id)->find($id) ?? $this->model;

        if (Gate::denies('modify-post', $this->single)) {
            if ($accHeader == 'application/json' && $contentTypeHeader == 'application/json') {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'You are unauthorized to modify this post'
                ], 403);
            } else if ($accHeader == 'application/xml' && $contentTypeHeader == 'application/xml') {
                $xml = new \SimpleXMLElement('<Response/>');
                $xml->addChild('success', 'false');
                $xml->addChild('status', 403);
                $xml->addChild('message', 'You are unauthorized to modify this post');
                return response($xml->asXML(), 403)->header('Content-Type', 'application/xml');
            }
        }

        if ($accHeader == 'application/json' && $contentTypeHeader == 'application/json') {
            $this->single->fill($request->all())->save();
            return response()->json($this->single, 200);
        }

        if($accHeader == 'application/xml' && $contentTypeHeader == 'application/xml') {
            $xmlString = $request->getContent();
            $xml = simplexml_load_string($xmlString);
            $data = json_decode(json_encode($xml), true);
            $this->single->fill($data)->save();
            $attributes = $this->single->getAttributes();
            $xml = new \SimpleXMLElement('<'.class_basename($this->model).'/>');
            foreach ($attributes as $key => $value) {
                $xml->addChild($key, $value);
            }
            return response($xml->asXML(), 200)->header('Content-Type', 'application/xml');
        }
    }

    public function delete(Request $request, $id) {
        $accHeader = $request->headers->get('Accept');
        if($accHeader != 'application/json' && $accHeader != 'application/xml') {
            return response('Not Accepttable', 404);
        }

        $this->single = Auth::user()->role === 'admin' ?
            $this->model::find($id) :
            $this->model::where('user_id', Auth::user()->id)->find($id) ?? $this->model;

        if (!$this->single) {
            if ($accHeader == 'application/json') {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => 'Post not found'
                ], 404);
            } else if ($accHeader == 'application/xml') {
                $xml = new \SimpleXMLElement('<Response/>');
                $xml->addChild('success', 'false');
                $xml->addChild('status', 403);
                $xml->addChild('message', 'Post not found');
                return response($xml->asXML(), 403)->header('Content-Type', 'application/xml');
            }
        }

        if (Gate::denies('modify-post', $this->single)) {
            if ($accHeader == 'application/json') {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'You are unauthorized to delete this post'
                ], 403);
            } else if ($accHeader == 'application/xml') {
                $xml = new \SimpleXMLElement('<Response/>');
                $xml->addChild('success', 'false');
                $xml->addChild('status', 403);
                $xml->addChild('message', 'You are unauthorized to delete this post');
                return response($xml->asXML(), 403)->header('Content-Type', 'application/xml');
            }
        }

        $this->single->delete();

        if($accHeader == 'application/json') {
            $message = ['message' => 'deleted successfully', 'post_id' => $id];
            return response()->json($message, 200);
        }

        if($accHeader == 'application/xml') {
            $xml = new \SimpleXMLElement('<response/>');
            $xml->addChild('message', 'deleted successfully');
            $xml->addChild('post_id', $id);
            return response($xml->asXML(), 200)->header('Content-Type', 'application/xml');
        }
    }
}
