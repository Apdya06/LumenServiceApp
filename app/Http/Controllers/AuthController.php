<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/auth/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="name",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="password_confirmation",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="role",
     *                 type="string"
     *             ),
     *             example={"name": "User Name", "email": "user@test.com", "password": "123456", "password_confirmation": "123456","role": "admin"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(
     *                  property="name",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="email",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="role",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="updated_at",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="created_at",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="id",
     *                  type="integer"
     *              ),
     *              example={"name": "User Name", "email": "user@test.com", "role": "admin", "updated_at": "yyyy-mm-dd","created_at": "yyyy-mm-dd", "id": 1}
     *         ),
     *     )
     * )
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Log in a user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 example={"email": "user@test.com", "password": "123456"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(
     *                  property="token",
     *                  type="string",
     *                  description="Bearer token"
     *              ),
     *              @OA\Property(
     *                  property="token_type",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="expires_in",
     *                  type="integer"
     *              ),
     *              example={"token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...", "token_type": "bearer", "expires_in": 3600}
     *         )
     *     ),
     *     security={}
     * ),
     */

    protected $model = User::class;

    public function register(Request $request)
    {
        $form = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ];

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
        if (!$token = Auth::attempt($credentials)) return response()->json(['message' => 'Unauthorized'], 401);

        $ttl = JWTAuth::factory()->getTTL();
        $expiration = Carbon::now()->addMinutes($ttl)->getTimestamp();
        return response()->json(['token' => $token, 'token_type' => 'bearer', 'expires_in' => $expiration], 200);
    }
}
