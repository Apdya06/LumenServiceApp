<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use OpenApi\Annotations as OA;

/**
 * Class Controller
 * @package App\Http\Controllers
 * @OA\OpenApi(
 *     @OA\Info(
 *          version="1.0.0",
 *          title="Lumen API",
 *          @OA\License(name="TEDC")
 *     ),
 *     @OA\Server(
 *          description="Local server",
 *          url="http://localhost:8000",
 *     ),
 *     @OA\Components(
 *          @OA\SecurityScheme(
 *              securityScheme="bearerAuth",
 *              type="http",
 *              scheme="bearer",
 *              bearerFormat="JWT"
 *          )
 *     )
 * )
 */

class Controller extends BaseController
{
    protected $model; // untuk mengambil model dari controller
    protected $single, $multi;  // single membaca id dan multi membaca semua data di tabel
    protected $input, $output;

}
