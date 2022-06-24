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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', 'Api\UserController@login');

Route::middleware('jwt.verify')->group(function () {
    Route::get('user', 'Api\UserController@getAuthenticatedUser')->middleware('jwt.verify');

    Route::apiResources([
        'clothes' => 'Api\ClothesController',
        'distributor' => 'Api\DistributorController',
        'total-pre-order' => 'Api\TotalPreOrderController'
    ]);

    Route::put('/photo/{id}', 'Api\UploadPhoto');
    Route::put('/is-active/{clothes}', 'Api\ActivateClothes');
    Route::apiResource('entity', 'Api\EntityController')
    ->parameters(['entity' => 'entity'])
    ->only('index', 'update');
});

Route::prefix('pre-order')->group( function () {
    Route::post('/', 'Api\DistributorController@store');
    Route::get('/{phone}', 'Api\PreOrderController@getClothes');
    Route::post('/{phone}', 'Api\PreOrderController@storeClothes');
});
