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
        'image' => 'Api\ImageController'
    ]);

    Route::apiResource('entity', 'Api\EntityController')
    ->parameters(['entity' => 'entity'])
    ->only('index', 'update');

    Route::apiResource('last-brand', 'Api\LastBrandController')
    ->parameters(['last-brand' => 'entity'])
    ->except('show', 'store');

    Route::apiResource('total-pre-order', 'Api\TotalPreOrderController')
    ->only('index', 'show');

    Route::apiResource('total-product', 'Api\TotalProductController')
    ->only('index');

    Route::get('/detail-transaction/{id}', 'Api\DetailTransaction');
    Route::put('/is-active/{clothes}', 'Api\ActivateClothes');
});

Route::prefix('pre-order')->group( function () {
    Route::get('/{phone}', 'Api\PreOrderController@getClothes');
    Route::post('/{phone}', 'Api\PreOrderController@storeClothes');
    Route::post('/store-all/{phone}', 'Api\PreOrderController@storeAllClothes');
});
