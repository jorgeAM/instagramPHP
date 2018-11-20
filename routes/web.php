<?php

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
    return $router->app->version();
});

$router->group(['prefix' => 'api', 'middleware' => 'jwt.auth'],  function () use ($router) {
  $router->get('/users', 'UserController@getUsers');
  $router->get('/users/{id}', 'UserController@getUser');
  $router->put('/users/{id}', 'UserController@updateUser');
  $router->delete('/users/{id}', 'UserController@deleteUser');
  $router->post('/users/{id}/avatar', 'UserController@uploadAvatar');
});

$router->group(['prefix' => 'api'],  function () use ($router) {
  $router->post('/sign-in', 'UserController@saveUser');
  $router->post('/login', 'UserController@login');
});
