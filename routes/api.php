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
        'area' => 'Api\Admin\AreaController',
        'image' => 'Api\Admin\PhotoController',
        'agent' => 'Api\Admin\AgentController',
        'clothes' => 'Api\Admin\ClothesController',
        'partner-group' => 'Api\PartnerGroupController',
        'mutif-store-admin' => 'Api\Admin\MutifStoreMasterController',
    ]);

    Route::prefix('partnumber')->group(function () {
        Route::post('/', 'Api\Admin\PartnumberController@store');
        Route::delete('/{partnumber}/delete', 'Api\Admin\PartnumberController@destroy');
    });

    Route::prefix('v2')->group(function () {
        Route::prefix('agent')->group(function () {
            Route::get('/', 'Api\Admin\Event\AgentController@index');
            Route::post('/', 'Api\Admin\Event\AgentController@store');
            Route::get('/{id}/detail', 'Api\Admin\Event\AgentController@show');
            Route::put('/{id}/update', 'Api\Admin\Event\AgentController@update');
        });

        Route::prefix('distributor')->group(function () {
            Route::get('/', 'Api\Admin\Event\DistributorController@index');
            Route::post('/', 'Api\Admin\Event\DistributorController@store');
            Route::get('/{id}/detail', 'Api\Admin\Event\DistributorController@show');
            Route::put('/{id}/update', 'Api\Admin\Event\DistributorController@update');
            Route::get('/list', 'Api\Admin\Event\DistributorController@getListDistributor');
        });

        Route::prefix('product')->group(function () {
            Route::get('/', 'Api\Admin\Event\ProductController@getAllProduct');
            Route::post('/create', 'Api\Admin\Event\ProductController@storeProduct');
            Route::post('/input-photo', 'Api\Admin\Event\ProductController@inputImage');
            Route::put('/{id}/update', 'Api\Admin\Event\ProductController@updateProduct');
            Route::get('/{id}/detail', 'Api\Admin\Event\ProductController@getDetailProduct');
        });

        Route::prefix('buffer-product')->group(function () {
            Route::post('/create', 'Api\Admin\Event\BufferProductController@createBufferProduct');
            Route::patch('/{buffer_product}/increase-amount', 'Api\Admin\Event\BufferProductController@increaseAmount');
        });

        Route::prefix('event')->group(function () {
            Route::get('/', 'Api\Admin\Event\EventController@getEvent');
            Route::post('/', 'Api\Admin\Event\EventController@createEvent');
            Route::delete('/{id}/delete-event', 'Api\Admin\Event\EventController@deleteEvent');
            Route::delete('/{id}/delete-session', 'Api\Admin\Event\EventController@deleteSession');
            Route::get('/{id}/detail', 'Api\Admin\Event\EventController@getDetailEvent');
            Route::post('/input-session', 'Api\Admin\Event\EventController@createSession');
            Route::put('/{id}/update-event', 'Api\Admin\Event\EventController@updateEvent');
            Route::patch('/{id}/activate-event', 'Api\Admin\Event\EventController@activateEvent');
            Route::delete('/{id}/delete-detail-session', 'Api\Admin\Event\EventController@deleteDetailSession');
            Route::post('/input-detail-session', 'Api\Admin\Event\EventController@inputDetailSession');
        });
    });

    // route with exception
    Route::apiResource('entity', 'Api\Admin\EntityController')
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
    Route::post('/photo/{clothes_id}', 'Api\Admin\ImageController@uploadPhoto');

    // API for report
    Route::get('/total', 'Api\Admin\TotalController@totalOrder');
    Route::get('/totalAgentWithDB', 'Api\Admin\TotalController@totalAgentWithDB');
    Route::get('/totalAllAgent', 'Api\Admin\TotalController@totalProductOrderClient');
    Route::get('detailTotalAgent/{id}', 'Api\Admin\TotalController@detailTotalAgentWithD');

});

// group pre-order route
// Route::apiResource('pre-order/{phone}', 'Api\Client\PreOrderController');
// Route::prefix('pre-order')->group( function () {
//     // CRUD for Taking PO from client
//     Route::get('/{phone}', 'Api\Client\PreOrderController@index');
//     Route::post('/{phone}', 'Api\Client\PreOrderController@store');
//     Route::delete('/{phone}/clothes/{id}', 'Api\Client\PreOrderController@destroy');
//     Route::put('/{phone}/cart/{id}', 'Api\Client\PreOrderController@update');
//     Route::get('/{phone}/clothes/{id}', 'Api\Client\PreOrderController@show');

//     // cart
//     Route::get('cart/{phone}', 'Api\Client\Cart');

//     // Store all clothes
//     Route::post('/store-all/{phone}', 'Api\Client\StoreAll');

//     // getHistory
//     Route::get('/{phone}/history', 'Api\Client\PreOrderController@history');
// });

// group registration route
Route::prefix('client')->group( function () {
    Route::prefix('auth')->group( function () {
        Route::post('login', 'Api\Client\Event\ClientController@login');
        Route::post('register', 'Api\Client\Event\ClientController@register');
        Route::get('partner-group', 'Api\Client\Event\ClientController@partnerGroupList');
        Route::post('register-distributor', 'Api\Client\Event\ClientController@registerDistributor');
    });

    Route::prefix('master')->group(function () {
        Route::get('current-event', 'Api\Client\Event\MasterController@activeEvent');
        Route::get('distributor', 'Api\Client\Event\MasterController@distributorList');
    });

    Route::prefix('SB')->middleware('client-check', 'check-event')->group(function () {
        Route::get('product', 'Api\Client\Event\OrderController@getProduct');
        Route::delete('/{id}/delete-chart', 'Api\Client\Event\OrderController@deleteDataChart');
        Route::post('chart-input', 'Api\Client\Event\OrderController@inputIntoChart');
        Route::post('/{eventId}/order', 'Api\Client\Event\OrderController@createOrder');
        Route::get('/{eventId}/data-chart', 'Api\Client\Event\OrderController@getDataChart');
        Route::get('/{eventId}/count-data-chart', 'Api\Client\Event\OrderController@countDataChart');
        Route::patch('/{id}/update-chart', 'Api\Client\Event\OrderController@updateDataChart');
    });

    Route::post('verification', 'Api\Client\Event\ClientController@verification');

    // Route::prefix('SB')->mid
    // Login & Register
    // Route::get('login/{phone}', 'Api\Client\ClientController@login');
    // Route::post('register', 'Api\Client\ClientController@register');
    // Route::put('update-p/hone/{phone}', 'Api\Client\ClientController@UpdatePhone');

    // Get list group & distributor
    // Route::get('/get-list-group', 'Api\Client\ClientController@PartnerGroup');
    // Route::get('/get-list-distributor', 'Api\Client\ClientController@distributor');

    // route for address agent
    // Route::get('area-client', 'Api\Client\getArea');
    // Route::post('/create-address/{phone}', 'Api\Client\PartnerAddressController@store');
    // Route::put('/update-address/{phone}', 'Api\Client\PartnerAddressController@update');
});

// Route::prefix('product')->group(function () {
//     Route::get('/', 'Api\ClothesController@getProduct');
//     Route::get('/{id}', 'Api\ClothesController@getDetailProduct');
//     Route::get('/find/{name}', 'Api\ClothesController@findProduct');
//     Route::get('type', 'Api\ClothesController@getType');
//     Route::get('/firstPhoto/{photoId}', 'Api\ClothesController@getFirstPhoto');
//     Route::post('/storeProduct', 'Api\ClothesController@Product');
// });

// Route::prefix('mutif-store')->group( function () {
//     Route::get('/{phone}', 'Api\Client\MutifStoreAddressController@index');
//     Route::post('/{phone}', 'Api\Client\MutifStoreAddressController@store');
//     Route::get('/{phone}/store/{id}', 'Api\Client\MutifStoreAddressController@show');
//     Route::put('/{phone}/store/{id}', 'Api\Client\MutifStoreAddressController@update');
//     Route::delete('/{phone}/store/{id}', 'Api\Client\MutifStoreAddressController@destroy');
// });

Route::prefix('exapro')->group(function () {
    Route::get('/{partnumber}/image', 'Api\Client\ImageController@show');
    Route::get('/{partnumber}/description', 'Api\Client\ProductController@show');
    Route::get('/image-catalog', 'Api\Client\ImageController@getImageCatalog');
});


// testing
