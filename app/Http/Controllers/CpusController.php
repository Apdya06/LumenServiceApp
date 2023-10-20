<?php
namespace App\Http\Controllers;

use App\Models\Cpus;

class CpusController extends Controller{
    protected $cpus, $outPut;

    public function __construct()
    {
        $this->cpus = Cpus::OrderBy("id", "DESC")->paginate(10);
        $this->outPut = [
            "message" => "cpus",
            "result" => $this->cpus
        ];
    }

    public function index(){
        return response()->json($this->outPut);
    }

    public function show($brandId){
        foreach ($this->cpus as $cpu) {
            if ($cpu['id'] == $brandId) {
                return response()->json($cpu);
            }
        }
        return response()->json(['message' => 'CPU not found'], 404);
    }
}
