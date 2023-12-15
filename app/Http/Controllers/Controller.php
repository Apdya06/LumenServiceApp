<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Controller extends BaseController
{
    protected $model; // untuk mengambil model dari controller
    protected $single, $multi;  // single membaca id dan multi membaca semua data di tabel
    protected $input, $output;

    // Method untuk mengambil nama model dari controller child
    protected function getModelName() {
        return class_basename($this->model);
    }

    // Method menambahkan data berbentuk xml
    protected function setXml($xml, $item) {
        foreach ($item->getAttributes() as $key => $value) {
            $xml->addChild($key, $value);
        }
    }

    public function indexjson() {
        $this->multi = Auth::user()->role === 'admin' ? $this->model::OrderBy("id", "DESC")->paginate(2)->toArray() :
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
        return response()->json($response, 200);
    }

    public function indexxml() {
        // $this->multi = Auth::user()->role === 'admin' ? $this->model::OrderBy("id", "DESC")->paginate(2)->toArray() :
        //     $this->model::Where(['user_id' => Auth::user()->id])->OrderBy("id", "DESC")->paginate(2)->toArray();
        //   $response = [
        //         'total_count' => $this->multi['total'],
        //         'limit' =>  $this->multi['per_page'],
        //         'pagination' => [
        //             'next_page' => $this->multi['next_page_url'],
        //             'prev_page' => $this->multi['prev_page_url'],
        //             'current_page' => $this->multi['current_page'],
        //         ],
        //         'data' => $this->multi['data']
        //     ];
        //     return response()->json($response, 200);
        // }
        $xml = new \SimpleXMLElement('<'.$this->getModelName().'/>');
        foreach($this->multi->items('data') as $item) {
            $xmlItem = $xml->addChild($this->getModelName());
            $this->setXml($xmlItem, $item);
        }
        return $xml->asXML();
    }

    public function showjson($id) {
        $this->single = Auth::user()->role === 'admin' ?  $this->model::find($id) :
            $this->model::where('user_id', Auth::user()->id)->find($id) ?? $this->model;

        if (Gate::denies('detail-post', $this->single)) {
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized to read this post'
            ], 403);
        }
        return response()->json($this->single, 200);
    }

    public function showxml($id) {
        // $this->single = Auth::user()->role === 'admin' ?  $this->model::find($id) :
        //     $this->model::where('user_id', Auth::user()->id)->find($id) ?? $this->model;

        // if (Gate::denies('detail-post', $this->single)) {
        //     return response()->json([
        //         'success' => false,
        //         'status' => 403,
        //         'message' => 'You are unauthorized to read this post'
        //     ], 403);
        // }

        $xml = new \SimpleXMLElement('<'.$this->getModelName().'/>');
        $this->setXml($xml, $this->single);
        return $xml->asXML();
    }

    public function storejson(Request $request) {
        if (Auth::user()->role === 'admin') {
            return response()->json($this->model::create($request->all()), 200);
        } else {
            $data = $request->all();
            $data['user_id'] = Auth::user()->id;
            return response()->json($this->model::create($data), 200);
        }
    }

    // belum di fix (auth dsb)
    public function storexml(Request $request) {
        $xmlString = $request->getContent();
        $xml = simplexml_load_string($xmlString);
        $data = json_decode(json_encode($xml), true);
        $this->model::create($data);
        return response($xmlString, 200)->header('Content-Type', 'application/xml');
    }

    public function updatejson(Request $request, $id) {
        $this->single = Auth::user()->role === 'admin' ? $this->model::find($id) :
            $this->model::where('user_id', Auth::user()->id)->find($id) ?? $this->model;

        if (Gate::denies('modify-post', $this->single)) {
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized modify this post'
            ], 403);
        }

        $this->single->fill($request->all())->save();
        return response()->json($this->single, 200);
    }

    // belum di fix (auth dsb)
    public function updatexml(Request $request, $id) {
        // $this->single = Auth::user()->role === 'admin' ? $this->model::find($id) :
        //     $this->model::where('user_id', Auth::user()->id)->find($id) ?? $this->model;

        // if (Gate::denies('modify-post', $this->single)) {
        //     return response()->json([
        //         'success' => false,
        //         'status' => 403,
        //         'message' => 'You are unauthorized modify this post'
        //     ], 403);
        // }

        $xmlString = $request->getContent();
        $xml = simplexml_load_string($xmlString);
        $data = json_decode(json_encode($xml), true);
        $this->single::create($data)->save();

        $xml = new \SimpleXMLElement('<'.$this->getModelName().'/>');
        $this->setXml($xml, $this->single);
        return $xml->asXML();
    }

    public function deletejson($id) {
        $this->single = Auth::user()->role === 'admin' ? $this->model::find($id) :
            $this->model::where('user_id', Auth::user()->id)->find($id) ?? $this->model;

        if (Gate::denies('modify-post', $this->single)) {
            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'You are unauthorized to delete this post'
            ], 403);
        }

        $this->single->delete();
        $message = ['message' => 'deleted successfully', 'post_id' => $id];
        return response()->json($message, 200);
    }

    // belum di fix
    public function deletexml(Request $request, $id) {
        // $this->single = Auth::user()->role === 'admin' ? $this->model::find($id) :
        //     $this->model::where('user_id', Auth::user()->id)->find($id) ?? $this->model;

        // if (Gate::denies('modify-post', $this->single)) {
        //     return response()->json([
        //         'success' => false,
        //         'status' => 403,
        //         'message' => 'You are unauthorized to delete this post'
        //     ], 403);
        // }
        $xml = new \SimpleXMLElement('<response/>');
        $xml->addChild('message', 'deleted successfully');
        $xml->addChild('post_id', $id);
        return response($xml->asXML(), 200)->header('Content-Type', 'application/xml');
    }
}
