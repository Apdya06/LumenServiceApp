<?php
namespace App\Http\Controllers;

use App\Models\Watches;

class WatchesController extends Controller{
    protected $watches, $outPut;

    public function __construct()
    {
        $this->watches = Watches::OrderBy("id", "DESC")->paginate(10);
        $this->outPut = [
            "message" => "watches",
            "result" => $this->watches
        ];
    }

    public function index(){
        return response()->json($this->outPut);
    }

    public function show($brandId){
        foreach ($this->watches as $watch) {
            if ($watch['id'] == $brandId) {
                return response()->json($watch);
            }
        }
        return response()->json(['message' => 'Watch not found'], 404);
    }
}
