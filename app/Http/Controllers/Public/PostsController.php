<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Gate;

class PostsController extends Controller{
    protected $model;

    public function __construct(Post $post)
    {
        $this->model = $post;
    }

    public function index(Request $request)
    {
        // if (Gate::denies('public-post')) {
        //     return response()->json([
        //         'success' => false,
        //         'status' => 403,
        //         'message' => 'You are unauthorized to read this post'
        //     ], 403);
        // }
        $accHeader = $request->headers->get('Accept');

        if($accHeader === '*/*' || empty($accHeader) ||
            ($accHeader != 'application/json' && $accHeader != 'application/xml')) {
            return response('Not Accepttable', 404);
        }

        $posts = $this->model::with('user')->OrderBy("id", "DESC")->paginate(5)->toArray();

        if($accHeader == 'application/json') {
            $response = [
                'total_count' => $posts['total'],
                'limit' =>  $posts['per_page'],
                'pagination' => [
                    'next_page' => $posts['next_page_url'],
                    'prev_page' => $posts['prev_page_url'],
                    'current_page' => $posts['current_page'],
                ],
                'data' => $posts['data']
            ];
            return response()->json($response, 200);
        }
        if($accHeader == 'application/xml') {
            $xml = new \SimpleXMLElement('<'.$this->getModelName().'/>');
            foreach($posts->items('data') as $item) {
                $xmlItem = $xml->addChild($this->getModelName());
                $this->setXml($xmlItem, $item);
            }
            return $xml->asXML();
        }
    }

    public function show(Request $request, $id) {
        // if (Gate::denies('public-post')) {
        //     return response()->json([
        //         'success' => false,
        //         'status' => 403,
        //         'message' => 'You are unauthorized to read this post'
        //     ], 403);
        // }

        $accHeader = $request->headers->get('Accept');
        if($accHeader === '*/*' || empty($accHeader) ||
            ($accHeader != 'application/json' && $accHeader != 'application/xml')) {
            return response('Not Accepttable', 404);
        }

        $post = $this->model::with(['user' => function($query){
            $query->select('id', 'name');
        }])->find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'Post not found'
            ], 404);
        }

        if($accHeader == 'application/xml') {
            $xml = new \SimpleXMLElement('<'.$this->getModelName().'/>');
            $this->setXml($xml, $post);
            return $xml->asXML();
        }

        return $accHeader == 'application/json' ? response()->json($post, 200) : null;
    }
}
