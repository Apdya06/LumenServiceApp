<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

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

    // Method untuk menyimpan data ke tabel untuk method store
    protected function saveStore($input, $model, $data) {
        $input = $data;
        return $model::create($input);
    }

    // Method untuk menyimpan data ke tabel untuk method update
    protected function saveUpdate($input, $model, $data) {
        $input = $data;
        return $model->fill($input)->save($input);
    }

    public function index(Request $request) {
        $accHeader = $request->headers->get('Accept');
        // Early return jika header Accept tidak ada atau bukan json dan xml
        if($accHeader === '*/*' || empty($accHeader) ||
            ($accHeader != 'application/json' && $accHeader != 'application/xml')) {
            return response('Not Accepttable', 404);
        }
        $this->multi = $this->model::OrderBy("id", "DESC")->paginate(2)->toArray();
        if($accHeader == 'application/json') {
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
        if($accHeader == 'application/xml') {
            $xml = new \SimpleXMLElement('<'.$this->getModelName().'/>');
            foreach($this->multi->items('data') as $item) {
                $xmlItem = $xml->addChild($this->getModelName());
                $this->setXml($xmlItem, $item);
            }
            return $xml->asXML();
        }
    }

    public function store(Request $request) {
        $accHeader = $request->headers->get('Accept');
        $contentTypeHeader = $request->headers->get('Content-Type');

        if($accHeader === '*/*' || empty($accHeader) ||
            ($accHeader !== 'application/json' && $accHeader !== 'application/xml'
            && $contentTypeHeader !== 'application/json' && $contentTypeHeader !== 'application/xml')) {
            return response('Not Accepttable', 404);
        }

        if($accHeader == 'application/json' && $contentTypeHeader == 'application/json') {
            $this->input = $this->saveStore($this->input, $this->model, $request->all());
            return response()->json($this->input, 200);
        }

        if($accHeader == 'application/xml' && $contentTypeHeader == 'application/xml') {
            $xmlString = $request->getContent();
            $xml = simplexml_load_string($xmlString);
            $data = json_decode(json_encode($xml), true);
            $this->input = $this->saveStore($this->input, $this->model, $data);
            return response($xmlString, 200)->header('Content-Type', 'application/xml');
        }
    }

    public function show(Request $request, $id) {
        $accHeader = $request->headers->get('Accept');
        if($accHeader === '*/*' || empty($accHeader) ||
            ($accHeader != 'application/json' && $accHeader != 'application/xml')) {
            return response('Not Accepttable', 404);
        }

        $this->single = $this->model::find($id);
        if(!$this->single) {abort(404);}

        if($accHeader == 'application/json') {
            return response()->json($this->single, 200);
        }

        if($accHeader == 'application/xml') {
            $xml = new \SimpleXMLElement('<'.$this->getModelName().'/>');
            $this->setXml($xml, $this->single);
            return $xml->asXML();
        }
    }

    public function update(Request $request, $id) {
        $accHeader = $request->headers->get('Accept');
        $contentTypeHeader = $request->headers->get('Content-Type');

        if($accHeader === '*/*' || empty($accHeader) ||
            ($accHeader != 'application/json' && $accHeader != 'application/xml'
            && $contentTypeHeader!= 'application/json' && $contentTypeHeader!= 'application/xml')) {
            return response('Not Accepttable', 404);
        }

        $this->single = $this->model::find($id);
        if(!$this->single) {abort(404);}

        if($accHeader == 'application/json' && $contentTypeHeader == 'application/json') {
            $this->saveUpdate($this->input, $this->single, $request->all());
            return response()->json($this->single, 200);
        }
        if($accHeader == 'application/xml' && $contentTypeHeader == 'application/xml') {
            $xmlString = $request->getContent();
            $xml = simplexml_load_string($xmlString);
            $data = json_decode(json_encode($xml), true);
            $this->saveUpdate($this->input, $this->single, $data);

            $xml = new \SimpleXMLElement('<'.$this->getModelName().'/>');
            $this->setXml($xml, $this->single);
            return $xml->asXML();
        }
    }

    public function delete(Request $request, $id) {
        $accHeader = $request->headers->get('Accept');
        if($accHeader != 'application/json' && $accHeader != 'application/xml') {
            return response('Not Accepttable', 404);
        }

        $this->single = $this->model::find($id);
        if(!$this->single) {abort(404);}

        if($accHeader == 'application/json') {
            $this->single->delete();
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
