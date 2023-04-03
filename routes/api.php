<?php

use App\Events\NotificationCreated;
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
    Route::get('user', 'Api\UserController@getAuthenticatedUser');
    Route::get('userName', 'Api\UserController@getUserName');

    // main route
    Route::apiResources([
        'clothes' => 'Api\ClothesController',
        'area' => 'Api\Admin\AreaController',
        'image' => 'Api\Admin\PhotoController',
        'agent' => 'Api\Admin\AgentController',
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

    Route::apiResource('phone', 'Api\Admin\PhoneController')
    ->except('store');

    // single route
    Route::get('/logs', 'Api\Admin\Logger');
    Route::post('/photo/{id}', 'Api\UploadPhoto');
    Route::get('/status', 'Api\Admin\SessionStatus');
    Route::post('logout', 'Api\UserController@logout');
    Route::get('get-new-member', 'Api\AccRegistration');
    Route::get('/highest-order', 'Api\Admin\HighestOrder');
    Route::put('/is-active/{clothes}', 'Api\ActivateClothes');
    Route::post('single-agent', 'Api\Admin\CreateSingleAgent');
    Route::get('unregistered-MS-agent', 'Api\Admin\SingleAgent');
    Route::get('/search-agent/{search}', 'Api\Admin\SearchAgent');
    Route::get('/detail-transaction/{id}', 'Api\DetailTransaction');
    Route::get('/search-products/{search}', 'Api\Admin\SearchProduct');
    Route::get('/search-distributor/{search}', 'Api\Admin\SearchDistributor');

    // API for report
    Route::get('/total', 'Api\Admin\TotalController@totalOrder');
    Route::get('/totalAllAgent', 'Api\Admin\TotalController@totalProductOrderClient');
    Route::get('/totalAgentWithDB', 'Api\Admin\TotalController@totalAgentWithDB');
    Route::get('detailTotalAgent/{id}', 'Api\Admin\TotalController@detailTotalAgentWithD');

});

// group pre-order route
// Route::apiResource('pre-order/{phone}', 'Api\Client\PreOrderController');
Route::prefix('pre-order')->group( function () {
    // CRUD for Taking PO from client
    Route::get('/{phone}', 'Api\Client\PreOrderController@index');
    Route::post('/{phone}', 'Api\Client\PreOrderController@store');
    Route::delete('/{phone}/clothes/{id}', 'Api\Client\PreOrderController@destroy');
    Route::put('/{phone}/cart/{id}', 'Api\Client\PreOrderController@update');
    Route::get('/{phone}/clothes/{id}', 'Api\Client\PreOrderController@show');

    // cart
    Route::get('cart/{phone}', 'Api\Client\Cart');

    // Store all clothes
    Route::post('/store-all/{phone}', 'Api\Client\StoreAll');

    // getHistory
    Route::get('/{phone}/history', 'Api\Client\PreOrderController@history');
});

// group registration route
Route::prefix('client')->group( function () {
    // Login & Register
    Route::get('login/{phone}', 'Api\Client\ClientController@login');
    Route::post('register', 'Api\Client\ClientController@register');
    Route::put('update-p/hone/{phone}', 'Api\Client\ClientController@UpdatePhone');

    // Get list group & distributor
    Route::get('/get-list-group', 'Api\Client\ClientController@PartnerGroup');
    Route::get('/get-list-distributor', 'Api\Client\ClientController@distributor');

    // route for address agent
    Route::get('area-client', 'Api\Client\getArea');
    // Route::post('/create-address/{phone}', 'Api\Client\PartnerAddressController@store');
    // Route::put('/update-address/{phone}', 'Api\Client\PartnerAddressController@update');
});

Route::prefix('product')->group(function () {
    Route::get('/', 'Api\ClothesController@getProduct');
    Route::get('/{id}', 'Api\ClothesController@getDetailProduct');
    Route::get('/find/{name}', 'Api\ClothesController@findProduct');
    Route::get('type', 'Api\ClothesController@getType');
    Route::get('/firstPhoto/{photoId}', 'Api\ClothesController@getFirstPhoto');
    Route::post('/storeProduct', 'Api\ClothesController@Product');
});

// Route::prefix('mutif-store')->group( function () {
//     Route::get('/{phone}', 'Api\Client\MutifStoreAddressController@index');
//     Route::post('/{phone}', 'Api\Client\MutifStoreAddressController@store');
//     Route::get('/{phone}/store/{id}', 'Api\Client\MutifStoreAddressController@show');
//     Route::put('/{phone}/store/{id}', 'Api\Client\MutifStoreAddressController@update');
//     Route::delete('/{phone}/store/{id}', 'Api\Client\MutifStoreAddressController@destroy');
// });

// testing
