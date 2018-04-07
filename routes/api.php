<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

//Prefix for Our API
Route::group(['prefix' => 'api/v1'], function () {
// Meeting Controller
    Route::resource('meeting', 'MeetingController', [
        'except' => ['edit', 'create']
    ]);
//Registration for Meeting
    Route::resource('meeting/registration', 'RegistrationController', [
        'only' => ['store', 'destroy']
    ]);
//Sing up User
    Route::post('user', 'AuthController@store');
//Sign in
    Route::post('user/signin', 'AuthController@signin');
});

