<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/hello-lumen/{name}', function ($name){
    return "<h1>Lumen</h1><p>Hi <b>". $name ."</b>  , thanks for using Lumen</p>";
});

$router->get('/scores', ['middleware' => 'login', function (){
    return "<h1>Selamat</h1> <p>Nilai anda 100</p>";
}]);

$router->get('users', 'UsersController@index');
$router->get('users/{userId}', 'UsersController@show');

$router->group(['middleware' => 'cars'], function() use ($router){
    $router->get('cars','CarsController@index');
    $router->get('cars/{brandId}','CarsController@show');
});

$router->group(['middleware' => 'phones'], function() use ($router){
    $router->get('phones', 'PhonesController@index');
    $router->get('phones/{brandId}', 'PhonesController@show');
});

$router->group(['middleware' => 'movies'], function() use ($router){
    $router->get('movies', 'MoviesController@index');
});

$router->group(['middleware' => 'cpus'], function() use ($router){
    $router->get('cpus', 'CpusController@index');
    $router->get('cpus/{brandId}', 'CpusController@show');
});

$router->group(['middleware' => 'watches'], function() use ($router){
    $router->get('watches', 'WatchesController@index');
    $router->get('watches/{brandId}', 'WatchesController@show');
});

$router->get('posts', 'PostsController@index');
