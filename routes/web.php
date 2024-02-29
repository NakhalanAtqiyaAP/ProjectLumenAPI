<?php
use Illuminate\Support\Facades\Route;

// $app->routeMiddleware([
//     'auth' => App\Http\Middleware\Authenticate::class,
// ]);


// STUFF
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
// INBOUND STUFF ------------------------------------------------------------
Route::get('/inbound', 'InboundStuffController@index');
Route::post('/inbound', 'InboundStuffController@store');
Route::get('/inbound/trash', 'InboundStuffController@deleted');
Route::delete('/inbound/permanent', 'InboundStuffController@permanentDelateAll');
Route::delete('/inbound/permanent/{id}', 'InboundStuffController@permanentDelate');
Route::put('/inbound/restore', 'InboundStuffController@restoreAll');
Route::put('/inbound/restore/{id}', 'InboundStuffController@restore');

Route::put('/inbound/{id}', 'InboundStuffController@update');
Route::get('/inbound/{id}', 'InboundStuffController@show');
Route::delete('/inbound/{id}', 'InboundStuffController@destroy');

// STUFF STOCK --------------------------------------------------------------
Route::get('/stuffstock', 'StuffStockController@index');
Route::post('/stuffstock', 'StuffStockController@store');
Route::get('/stuffstock/trash', 'StuffStockController@deleted');
Route::delete('/stuffstock/permanent', 'StuffStockController@permanentDelateAll');
Route::delete('/stuffstock/permanent/{id}', 'StuffStockController@permanentDelate');
Route::put('/stuffstock/restore', 'StuffStockController@restoreAll');
Route::put('/stuffstock/restore/{id}', 'StuffStockController@restore');

Route::put('/stuffstock/{id}', 'StuffStockController@update');
Route::get('/stuffstock/{id}', 'StuffStockController@show');
Route::delete('/stuffstock/{id}', 'StuffStockController@destroy');
// LENDING --------------------------------------------------------------   
Route::get('/lending', 'LendingController@index');
Route::post('/lending', 'LendingController@store');
Route::get('/lending/trash', 'LendingController@deleted');
Route::delete('/lending/permanent', 'LendingController@permanentDelateAll');
Route::delete('/lending/permanent/{id}', 'LendingController@permanentDelate');
Route::put('/lending/restore', 'LendingController@restoreAll');
Route::put('/lending/restore/{id}', 'LendingController@restore');

Route::get('/lending/{id}', 'LendingController@show');
Route::put('/lending/{id}', 'LendingController@update');
Route::delete('/lending/{id}', 'LendingController@destroy');

// RESTORATION --------------------------------------------------------------
Route::get('/restoration', 'RestorationController@index');
Route::post('/restoration', 'RestorationController@store');
Route::get('/restoration/trash', 'RestorationController@deleted');
Route::delete('/restoration/permanent', 'RestorationController@permanentDelateAll');
Route::delete('/restoration/permanent/{id}', 'RestorationController@permanentDelate');
Route::put('/restoration/restore', 'RestorationController@restoreAll');
Route::put('/restoration/restore/{id}', 'RestorationController@restore');

Route::get('/restoration/{id}', 'RestorationController@show');
Route::put('/restoration/{id}', 'RestorationController@update');
Route::delete('/restoration/{id}', 'RestorationController@destroy');

// USER --------------------------------------------------------------
Route::get('/user', 'UserController@index');
Route::post('/user-register', 'UserController@store');
Route::post('/user-login', 'UserController@login');
Route::get('/user/trash', 'UserController@deleted');
Route::delete('/user/permanent', 'UserController@permanentDelateAll');
Route::delete('/user/permanent/{id}', 'UserController@permanentDelate');
Route::put('/user/restore', 'UserController@restoreAll');
Route::put('/user/restore/{id}', 'UserController@restore');

Route::get('/user/{id}', 'UserController@show');
Route::put('/user/{id}', 'UserController@update');
Route::delete('/user/{id}', 'UserController@destroy');

