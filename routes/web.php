<?php

/** @var \Laravel\Lumen\Routing\Router $router */
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return response()->json(['service_name' => 'PHP Service App', 'status' => 'Running']);
    // return $router->app->version();
});

$router->get('/hello-lumen', function (){
    return "<h1>Lumen</h1><p>Hi good developer, thanks for using Lumen</p>";
});

$router->get('/hello-lumen/{name}', function ($name) {
    return "<h1>Lumen</h1><p>Hi <b>". $name ."</b>  , thanks for using Lumen</p>";
});

$router->get('/scores', ['middleware' => 'login', function (){
    return "<h1>Selamat</h1> <p>Nilai anda 100</p>";
}]);

$router->get('users', 'UsersController@index');
$router->get('users/{userId}', 'UsersController@show');

$router->group(['middleware' => 'cars'], function() use ($router) {
    $router->get('cars','CarsController@index');
    $router->post('cars','CarsController@store');
    $router->get('cars/{id}','CarsController@show');
    $router->put('cars/{id}','CarsController@update');
    $router->delete('cars/{id}','CarsController@delete');
});

$router->group(['middleware' => 'phones'], function() use ($router) {
    $router->get('phones', 'PhonesController@index');
    $router->post('phones', 'PhonesController@store');
    $router->get('phones/{id}', 'PhonesController@show');
    $router->put('phones/{id}', 'PhonesController@update');
    $router->delete('phones/{id}', 'PhonesController@delete');
});

$router->group(['middleware' => 'movies'], function() use ($router) {
    $router->get('movies', 'MoviesController@index');
    $router->post('movies', 'MoviesController@store');
    $router->get('movies/{id}', 'MoviesController@show');
    $router->put('movies/{id}', 'MoviesController@update');
    $router->delete('movies/{id}', 'MoviesController@delete');
});

$router->group(['middleware' => 'cpus'], function() use ($router) {
    $router->get('cpus', 'CpusController@index');
    $router->post('cpus', 'CpusController@store');
    $router->get('cpus/{id}', 'CpusController@show');
    $router->put('cpus/{id}', 'CpusController@update');
    $router->delete('cpus/{id}', 'CpusController@delete');
});

$router->group(['middleware' => 'watches'], function() use ($router) {
    $router->get('watches', 'WatchesController@index');
    $router->post('watches', 'WatchesController@store');
    $router->get('watches/{id}', 'WatchesController@show');
    $router->put('watches/{id}', 'WatchesController@update');
    $router->delete('watches/{id}', 'WatchesController@delete');
});

$router->group(['middleware' => 'auth'], function($router) {
    $router->group(['prefix' => "posts"], function($router) {
        $router->get('/',  function(Request $request) {
            $controller = 'App\Http\Controllers\PostsController';
            $accHeader = $request->headers->get('Accept');
            if ($accHeader === 'application/json') {
                return app($controller)->indexjson();
            } else if ($accHeader === 'application/xml') {
                return app($controller)->indexxml();
            } else {
                return response()->json(['message' => 'Unacceptable'], 406);
            }
        });

        $router->post('/', function(Request $request) {
            $controller = 'App\Http\Controllers\PostsController';
            $accHeader = $request->headers->get('Accept');
            $contentTypeHeader = $request->headers->get('Content-Type');
            if ($accHeader === 'application/json') {
                return app($controller)->storejson($request);
            } else if ($accHeader === 'application/xml') {
                return app($controller)->storexml($request);
            } else {
                return response()->json(['message' => 'Unacceptable'], 406);
            }
        });

        $router->get('/{id}', function(Request $request, $id) {
            $controller = 'App\Http\Controllers\PostsController';
            $accHeader = $request->headers->get('Accept');
            if ($accHeader === 'application/json') {
                return app($controller)->showjson($id);
            } else if ($accHeader === 'application/xml') {
                return app($controller)->showxml($id);
            } else {
                return response()->json(['message' => 'Unacceptable'], 406);
            }
        });

        $router->get('/image/{id}', function(Request $request, $id) {
            $controller = 'App\Http\Controllers\PostsController';
            $accHeader = $request->headers->get('Accept');
            if ($accHeader === 'application/json') {
                return app($controller)->imagejson($id);
            } else if ($accHeader === 'application/xml') {
                return app($controller)->imagexml($id);
            } else {
                return response()->json(['message' => 'Unacceptable'], 406);
            }
        });

        $router->put('/{id}', function(Request $request, $id) {
            $controller = 'App\Http\Controllers\PostsController';
            $accHeader = $request->headers->get('Accept');
            $contentTypeHeader = $request->headers->get('Content-Type');
            if ($accHeader === 'application/json' && $contentTypeHeader === 'application/json') {
                return app($controller)->updatejson($request, $id);
            } else if ($accHeader === 'application/xml' && $contentTypeHeader === 'application/xml') {
                return app($controller)->updatexml($request, $id);
            } else {
                return response()->json(['message' => 'Unacceptable'], 406);
            }
        });

        $router->delete('/{id}', function(Request $request, $id) {
            $controller = 'App\Http\Controllers\PostsController';
            $accHeader = $request->headers->get('Accept');
            if ($accHeader === 'application/json') {
                return app($controller)->deletejson($id);
            } else if ($accHeader === 'application/xml') {
                return app($controller)->deletexml($id);
            } else {
                return response()->json(['message' => 'Unacceptable'], 406);
            }
        });
    });
    $router->post('profiles', 'ProfileController@store');
});

$router->get('profiles/{userId}', 'ProfileController@show');
$router->get('profiles/image/{imageName}', 'ProfileController@image');

$router->group(['prefix' => 'public'], function () use ($router) {
    $router->get('posts', 'Public\PostsController@index');
    $router->get('posts/{id}', 'Public\PostsController@show');
});

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/register', 'AuthController@register');
    $router->post('/login', 'AuthController@login');
});
