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

    // main route
    Route::apiResources([
        'agent' => 'Api\AgentController',
        'clothes' => 'Api\ClothesController',
        'distributor' => 'Api\DistributorController',
        'partner-group' => 'Api\PartnerGroupController',
        // 'image' => 'Api\ImageController'
    ]);

    // route with exception
    Route::apiResource('entity', 'Api\EntityController')
    ->parameters(['entity' => 'entity'])
    ->only('index', 'update');

    Route::apiResource('last-brand', 'Api\LastBrandController')
    ->parameters(['last-brand' => 'entity'])
    ->except('show', 'store');

    Route::apiResource('total-product', 'Api\TotalProductController')
    ->only('index');

    Route::apiResource('total-pre-order', 'Api\TotalPreOrderController')
    ->only('index', 'show');

    // single route
    Route::post('/photo/{id}', 'Api\UploadPhoto');
    Route::put('/is-active/{clothes}', 'Api\ActivateClothes');
    Route::get('/detail-transaction/{id}', 'Api\DetailTransaction');
});

// group pre-order route
Route::prefix('pre-order')->group( function () {
    Route::get('/{phone}', 'Api\PreOrderController@getClothes');
    Route::post('/{phone}', 'Api\PreOrderController@storeClothes');
    Route::post('/store-all/{phone}', 'Api\PreOrderController@storeAllClothes');
});

// group registration route
Route::prefix('registration')->group( function () {
    Route::get('/get-list-group', 'Api\PartnerGroupController@index');
    Route::post('/register-member', 'Api\PreOrderController@register');
    Route::get('/get-list-distributor', 'Api\ListController@listDistributor');
});

