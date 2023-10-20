<?php
namespace App\Http\Controllers;

use App\Models\Cars;

class CarsController extends Controller{
    protected $cars, $outPut;

    public function __construct()
    {
        $this->cars = Cars::OrderBy("id", "DESC")->paginate(10);
        $this->outPut = [
            "message" => "cars",
            "result" => $this->cars
        ];
    }

    public function index(){
        return response()->json($this->outPut, 200);
    }

    public function show($brandId){
        foreach ($this->cars as $car) {
            if ($car['id'] == $brandId) {
                return response()->json($car);
            }
        }
        return response()->json(['message' => 'Car not found'], 404);
    }
}
