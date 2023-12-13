<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProfileController extends Controller{
    protected $model;

    public function __construct(Profile $profile)
    {
        $this->model = $profile;
    }

    private function validation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|min:2',
            'last_name' => 'required|min:2',
            'summary' => 'required|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
    }

    // Overridding method di bawah
    public function store(Request $request)
    {
        if ($this->validation($request)) return $this->validation($request); // validasi request

        $profile = Profile::where('user_id', Auth::user()->id)->first();

        if(!$profile){
            $profile = new Profile();
            $profile->user_id = Auth::user()->id;
        }

        $profile->first_name = $request->input('first_name');
        $profile->last_name = $request->input('last_name');
        $profile->summary = $request->input('summary');

        if($request->hasFile('image')){
            $firstName = str_replace(' ', '_', $request->input('first_name'));
            $lastName = str_replace(' ', '_', $request->input('last_name'));
            $imgName = Auth::user()->id . '_' . $firstName . '_' . $lastName;
            $request->file('image')->move(storage_path('uploads/image_profile'), $imgName);

            $current_image_path = storage_path('avatar') . '/' . $profile->image;
            if (file_exists($current_image_path)){
                unlink($current_image_path);
            }

            $profile->image = $imgName;
        }

        $profile->save();

        return response()->json($profile, 200);
    }

    public function show(Request $request, $userId)
    {
        $profile = Profile::where('user_id', $userId)->first();
        if(!$profile) abort(404);

        return response()->json($profile, 200);
    }

    public function image($imageName)
    {
        $imagePath =  storage_path('uploads/image_profile' . '/' . $imageName);
        if (file_exists($imagePath))
        {
            $file = file_get_contents($imagePath);
            return response($file, 200)->header('Content-Type', 'image/jpeg');
        }

        return response()->json([
            "message" => "Image not found"
        ], 401);
    }
}
