<?php
use Illuminate\Support\Facades\Route;

// $app->routeMiddleware([
//     'auth' => App\Http\Middleware\Authenticate::class,
// ]);



Route::get('/stuff', 'StuffController@index');
Route::post('/stuff', 'StuffController@store');
Route::get('/stuff/trash', 'StuffController@deleted');
Route::delete('/stuff/permanent', 'StuffController@permanentDelateAll');
Route::delete('/stuff/permanent/{id}', 'StuffController@permanentDelate');
Route::put('/stuff/restore', 'StuffController@restoreAll');
Route::put('/stuff/restore/{id}', 'StuffController@restore');

Route::put('/stuff/{id}', 'StuffController@update');
Route::get('/stuff/{id}', 'StuffController@show');
Route::delete('/stuff/{id}', 'StuffController@destroy');

Route::get('/inboundstuff', 'InboundStuffController@index');
Route::post('/inboundstuff', 'InboundStuffController@store');
Route::get('/inboundstuff/{id}', 'InboundStuffController@show');
Route::put('/inboundstuff/{id}', 'InboundStuffController@update');
Route::delete('/inboundstuff/{id}', 'InboundStuffController@destroy');


Route::get('/stuffstock', 'StuffStockController@index');
Route::post('/stuffstock', 'StuffStockController@store');
Route::get('/stuffstock/{id}', 'StuffStockController@show');
Route::put('/stuffstock/{id}', 'StuffStockController@update');
Route::delete('/stuffstock/{id}', 'StuffStockController@destroy');


Route::get('/lending', 'LendingController@index');
Route::post('/lending', 'LendingController@store');
Route::get('/lending/{id}', 'LendingController@show');
Route::put('/lending/{id}', 'LendingController@update');
Route::delete('/lending/{id}', 'LendingController@destroy');


Route::get('/restoration', 'RestorationController@index');
Route::post('/restoration', 'RestorationController@store');
Route::get('/restoration/{id}', 'RestorationController@show');
Route::put('/restoration/{id}', 'RestorationController@update');
Route::delete('/restoration/{id}', 'RestorationController@destroy');

Route::get('/user', 'UserController@index');
Route::post('/user-register', 'UserController@store');
Route::post('/user-login', 'UserController@login');
Route::get('/user/{id}', 'UserController@show');
Route::put('/user/{id}', 'UserController@update');
Route::delete('/user/{id}', 'UserController@destroy');

