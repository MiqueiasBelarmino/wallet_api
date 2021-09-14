<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('auth/login', 'App\Http\Controllers\Api\AuthController@login');
Route::post('login', 'App\Http\Controllers\Api\AuthController@login');
Route::any('me', 'App\Http\Controllers\Api\AuthController@me')->name('me');

Route::group(['middleware' =>['apiJWT']], function(){
    /*rotas de usuário/auth*/
    Route::get("users","App\Http\Controllers\Api\UserController@index");
    Route::post('logout', 'App\Http\Controllers\Api\AuthController@logout');
    Route::post('refresh', 'App\Http\Controllers\Api\AuthController@refresh');

    /*rotas de despesas*/
    Route::post("despesas","App\Http\Controllers\Api\DespesaController@index");
    Route::get("despesas/delete/{id}","App\Http\Controllers\Api\DespesaController@delete");
    Route::get("despesas/show/{id}","App\Http\Controllers\Api\DespesaController@show");
    Route::post("despesas/create","App\Http\Controllers\Api\DespesaController@create");
    Route::post("despesas/update/{id}","App\Http\Controllers\Api\DespesaController@update");

    /*rotas de receitas*/
    Route::post("receitas","App\Http\Controllers\Api\ReceitaController@index");
    Route::get("receitas/delete/{id}","App\Http\Controllers\Api\ReceitaController@delete");
    Route::get("receitas/show/{id}","App\Http\Controllers\Api\ReceitaController@show");
    Route::post("receitas/create","App\Http\Controllers\Api\ReceitaController@create");
    Route::post("receitas/update/{id}","App\Http\Controllers\Api\ReceitaController@update");
});