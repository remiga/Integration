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

Route::group(['middleware' => ['json.response']], function () {
//Take patient orders -
    Route::middleware('client:api')->get('/order', function (Request $request) {
//    Route::middleware('auth:api')->get('/order', function (Request $request) {
//        return $request->user();
        return 'labas';
      //return response()->json(['Your name'=>'Regimantas']);
//  return response()->json(['kietas'=>'Registratura'])->withCallback($request->input('callback'));
    });

});
Route::get('/orders/{id}','OrdersController@index');
Route::get('/documents/{id}','DocumentsController@index');

