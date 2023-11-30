<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller{
    protected $model = User::class;

    public function register(Request $request)
    {
        $form = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ];

        // $this->validate($request, $form);

        $validator = Validator::make($request->all(), $form);

        if ($validator->fails()) return response()->json(['error' => $validator->errors()], 400);

        $user = new User;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = app('hash')->make($request->input('password'));
        $user->role = $request->input('role');
        $user->save();

        return response()->json($user, 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) return response()->json(['error' => $validator->errors()], 400);

        $credentials = $request->only(['email', 'password']);
        if (! $token = Auth::attempt($credentials)) return response()->json(['message' => 'Unauthorized'], 401);

        return response()->json(['token' => $token, 'token_type' => 'bearer', 'expires_in' => Auth::factory()->setTTL(60)], 200);
    }
}
