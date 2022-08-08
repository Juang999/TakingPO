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
        'image' => 'Api\Admin\PhotoController',
        'distributor' => 'Api\DistributorController',
        'partner-group' => 'Api\PartnerGroupController',
        'mutif-store-admin' => 'Api\Admin\MutifStoreMasterController',
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
    Route::get('/status', 'Api\Admin\SessionStatus');
    Route::get('get-new-member', 'Api\AccRegistration');
    Route::put('/is-active/{clothes}', 'Api\ActivateClothes');
    Route::post('single-agent', 'Api\Admin\CreateSingleAgent');
    Route::get('unregistered-MS-agent', 'Api\Admin\SingleAgent');
    Route::get('/search-agent/{search}', 'Api\Admin\SearchAgent');
    Route::get('/detail-transaction/{id}', 'Api\DetailTransaction');
    Route::get('/search-products/{search}', 'Api\Admin\SearchProduct');
    Route::get('/search-distributor/{search}', 'Api\Admin\SearchDistributor');
    Route::get('/highest-order', 'Api\Admin\HighestOrder');
    Route::get('/logs', 'Api\Admin\Logger');
    Route::post('logout', 'Api\UserController@logout');
});

// group pre-order route
// Route::apiResource('pre-order/{phone}', 'Api\Client\PreOrderController');
Route::prefix('pre-order')->group( function () {
    // CRUD for Taking PO from client
    Route::get('/{phone}', 'Api\Client\PreOrderController@index');
    Route::post('/{phone}', 'Api\Client\PreOrderController@store');
    Route::delete('/{phone}/clothes/{id}', 'Api\Client\PreOrderController@destroy');
    Route::get('/{phone}/clothes/{id}', 'Api\Client\PreOrderController@show');
    Route::put('/{phone}/cart/{id}', 'Api\Client\PreOrderController@update');

    // cart
    Route::get('cart/{phone}', 'Api\Client\Cart');

    // Store all clothes
    Route::post('/store-all/{phone}', 'Api\Client\StoreAll');
});

// group registration route
Route::prefix('client')->group( function () {
    // Login & Register
    Route::get('login/{phone}', 'Api\Client\ClientController@login');
    Route::post('register', 'Api\Client\ClientController@register');
    Route::put('update-phone/{phone}', 'Api\Client\ClientController@UpdatePhone');

    // Get list group & distributor
    Route::get('/get-list-group', 'Api\Client\ClientController@PartnerGroup');
    Route::get('/get-list-distributor', 'Api\Client\ClientController@distributor');

    // create address for agent & MS
    Route::post('/create-address/{phone}', 'Api\Client\PartnerAddressController@store');
});

Route::prefix('mutif-store')->group( function () {
    Route::get('/{phone}', 'Api\Client\MutifStoreAddressController@index');
    Route::post('/{phone}', 'Api\Client\MutifStoreAddressController@store');
    Route::get('/{phone}/MS/{id}', 'Api\Client\MutifStoreAddressController@show');
    Route::put('/{phone}/MS/{id}', 'Api\Client\MutifStoreAddressController@update');
});

// testing
Route::get('/index', 'WilayahController@index');
Route::post('/testing', 'TestingController@index')->middleware('Logger');
