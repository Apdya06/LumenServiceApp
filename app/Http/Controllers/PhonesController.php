<?php
namespace App\Http\Controllers;

use App\Models\Phones;

class PhonesController extends Controller{
    protected $phones, $outPut;

    public function __construct()
    {
        $this->phones = Phones::OrderBy("id", "DESC")->paginate(10);
        $this->outPut = [
            "message" => "phones",
            "result" => $this->phones
        ];
    }

    public function index(){
        return response()->json($this->outPut);
    }

    public function show($brandId){
        foreach ($this->phones as $phone) {
            if ($phone['id'] == $brandId) {
                return response()->json($phone);
            }
        }
        return response()->json(['message' => 'Phone not found'], 404);
    }
}
