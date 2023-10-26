<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    protected $model, $multi, $single, $input, $output;

    protected function getModelName()
    {
        return class_basename($this->model);
    }

    public function index(Request $request){
        $accHeader = $request->headers->get('Accept');

        if($accHeader == 'application/json' || $accHeader == 'application/xml') {
            $this->multi = $this->model::OrderBy("id", "DESC")->paginate(10);

            if($accHeader == 'application/json'){
                return response()->json($this->multi->items('data'), 200);
            }else{
                $xml = new \SimpleXMLElement('<'.$this->getModelName().'/>');
                foreach($this->multi->items('data') as $item) {
                    $xmlItem = $xml->addChild($this->getModelName());
                    foreach ($item->getAttributes() as $key => $value) {
                        $xmlItem->addChild($key, $value);
                    }
                }
                return $xml->asXML();
            }
        }else{
            return response('Not Accepttable', 404);
        }
    }

    public function store(Request $request)
{
    $accHeader = $request->headers->get('Accept');
    if ($accHeader == 'application/json' || $accHeader == 'application/xml') {
        $contentTypeHeader = $request->headers->get('Content-Type');
        if ($contentTypeHeader == 'application/json') {
            $this->input = $request->all();
            $this->model::create($this->input);
            return response()->json($this->input, 200);
        } else {
            $xmlString = $request->getContent();
            $xml = simplexml_load_string($xmlString);
            $data = json_decode(json_encode($xml), true);

            $this->input = $data;
            $this->model::create($this->input);

            return response($xmlString, 200)->header('Content-Type', 'application/xml');
        }
    } else {
        return response('Not Acceptable', 406);
    }
}


    public function show(Request $request, $id){
        $accHeader = $request->headers->get('Accept');
        if($accHeader == 'application/json' || $accHeader == 'application/xml') {
            $this->single = $this->model::find($id);
            if(!$this->single){abort(404);}
            if($accHeader == 'application/json'){
                return response()->json($this->single, 200);
            }else {
                $xml = new \SimpleXMLElement('<'.$this->getModelName().'/>');
                foreach ($this->single->getAttributes() as $key => $value) {
                    $xml->addChild($key, $value);
                }
                return $xml->asXML();
            }
        }else{
            return response('Not Accepttable', 404);
        }
    }

    public function update(Request $request, $id){
        $accHeader = $request->headers->get('Accept');
        if($accHeader == 'application/json' || $accHeader == 'application/xml') {
            $this->input = $request->all();
            $this->single = $this->model::find($id);
            if(!$this->single){abort(404);}
            if($accHeader == 'application/json'){
                $this->single->fill($this->input);
                $this->single->save();
                return response()->json($this->single, 200);
            }else{
                $xml = new \SimpleXMLElement('<'.$this->getModelName().'/>');
                foreach ($this->single->getAttributes() as $key => $value) {
                    $xml->addChild($key, $value);
                }
                return $xml->asXML();
            }
        }else{
            return response('Not Accepttable', 404);
        }
    }

    public function delete(Request $request, $id){
        $accHeader = $request->headers->get('Accept');
        if($accHeader == 'application/json' || $accHeader == 'application/xml') {
            $this->single = $this->model::find($id);
            if(!$this->single){abort(404);}
            $this->single->delete();
            $message = ['message' => 'deleted successfully', 'post_id' => $id];
            if ($accHeader == 'application/json') {
                return response()->json($message, 200);
            } else {
                $xml = new \SimpleXMLElement('<response/>');
                $xml->addChild('message', 'deleted successfully');
                $xml->addChild('post_id', $id);
                return response($xml->asXML(), 200)->header('Content-Type', 'application/xml');
            }
        } else {
            return response('Not Acceptable', 404);
        }
    }
}
