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

    public function index(){
        $this->multi = $this->model::OrderBy("id", "DESC")->paginate(10);
        $this->output = [
            "message" => get_class($this->model),
            "result" => $this->multi
        ];
        return response()->json($this->output, 200);
    }

    public function store(Request $request){
        $this->input = $request->all();
        $this->single = $this->model::create($this->input);
        return response()->json($this->single, 200);
    }

    public function show($id){
        $this->single = $this->model::find($id);
        if(!$this->single){
            abort(404);
        }
        return response()->json($this->single, 200);
    }

    public function update(Request $request, $id){
        $this->input = $request->all();
        $this->single = $this->model::find($id);
        if(!$this->single){
            abort(404);
        }
        $this->single->fill($this->input);
        $this->single->save();
        return response()->json($this->single, 200);
    }

    public function delete($id){
        $this->single = $this->model::find($id);
        if(!$this->single){
            abort(404);
        }
        $this->single->delete();
        $message = ['message' => 'deleted successfuly', 'post_id' => $id];
        return response()->json($message, 200);
    }
}
