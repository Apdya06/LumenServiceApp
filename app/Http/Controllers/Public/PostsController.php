<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

class PostsController extends Controller
{
    /**
     * @OA\Post(
     *     path="/public/posts",
     *     summary="Create a new post",
     *     tags={"Public Post"},
     *     @OA\RequestBody(
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
     *                 example={"title": "Post Title", "status": "published", "content": "Post Content", "user_id": "1"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
     * @OA\Get(
     *     path="/public/posts",
     *     summary="Get Public posts",
     *     tags={"Public Post"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
     * @OA\Get(
     *     path="/public/posts/{id}",
     *     summary = "Get Public post by id",
     *     tags={"Public Post"},
     *     @OA\Parameter(
     *       name="id",
     *       in="path",
     *       required=true,
     *       @OA\Schema(type="number")
     *       ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json"
     *         )
     *     )
     * )
     */

    protected $model;

    public function __construct(Post $post)
    {
        $this->model = $post;
    }

    public function index(Request $request)
    {
        $accHeader = $request->headers->get('Accept');

        if (
            $accHeader === '*/*' || empty($accHeader) ||
            ($accHeader != 'application/json' && $accHeader != 'application/xml')
        ) {
            return response('Not Accepttable', 404);
        }

        $posts = $this->model->OrderBy("id", "DESC")->paginate(5)->toArray();

        if ($accHeader == 'application/json') {
            return response()->json($posts['data'], 200);
        }
        if ($accHeader == 'application/xml') {
            $xml = new \SimpleXMLElement('<Posts/>');
            foreach ($posts['data'] as $item) {
                $xmlItem = $xml->addChild('Post');
                foreach ($item as $key => $value) {
                    $xmlItem->addChild($key, $value);
                }
            }
            return $xml->asXML();
        }
    }

    public function show(Request $request, $id)
    {
        $accHeader = $request->headers->get('Accept');
        if (
            $accHeader === '*/*' || empty($accHeader) ||
            ($accHeader != 'application/json' && $accHeader != 'application/xml')
        ) {
            return response('Not Accepttable', 404);
        }

        $post = $this->model::with(['user' => function ($query) {
            $query->select('id', 'name');
        }])->find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'Post not found'
            ], 404);
        }

        function setXml($xml, $item)
        {
            foreach ($item as $key => $value) {
                $xml->addChild($key, $value);
            }
        }

        if ($accHeader == 'application/xml') {
            $xml = new \SimpleXMLElement('<Posts/>');
            setXml($xml, $post);
            return $xml->asXML();
        }


        return $accHeader == 'application/json' ? response()->json($post, 200) : null;
    }

    // For testing only
    public function storejson(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:5',
            'content' => 'required|min:10',
            'status' => 'required|in:draft,published',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $data = $request->all();
        return response()->json($this->model::create($data), 200);
    }

    public function storexml(Request $request)
    {
        $xmlString = $request->getContent();
        $xml = simplexml_load_string($xmlString);
        $data = json_decode(json_encode($xml), true);

        $validator = Validator::make($data, [
            'title' => 'required|min:5',
            'content' => 'required|min:10',
            'status' => 'required|in:draft,published',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            $xmlResponse = new \SimpleXMLElement('<error/>');
            $xmlResponse->addChild('message', 'Validation error');
            $errors = $xmlResponse->addChild('errors');

            foreach ($validator->errors()->getMessages() as $field => $messages) {
                $error = $errors->addChild('error');
                $error->addChild('field', $field);
                foreach ($messages as $message) {
                    $error->addChild('message', $message);
                }
            }

            return response($xmlResponse->asXML(), 400)->header('Content-Type', 'application/xml');
        }

        $newModel = $this->model::create($data);

        $xml->addChild('id', $newModel->id);
        $xml->addChild('created_at', $newModel->created_at);
        $xml->addChild('updated_at', $newModel->updated_at);

        return response($xml->asXML(), 200)->header('Content-Type', 'application/xml');
    }
}
