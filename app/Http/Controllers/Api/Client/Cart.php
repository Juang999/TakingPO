<?php

namespace App\Http\Controllers\Api\Client;

use App\Distributor;
use App\Http\Controllers\Controller;
use App\TemporaryStorage;
use Illuminate\Http\Request;

class Cart extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke($phone)
    {
        try {
        $user = Distributor::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'status' => 'rejected',
                'message' => 'number '.$phone.' not registered'
            ], 300);
        }

        $cart = TemporaryStorage::where('distributor_id', $user->id)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'success to get data from cart',
            'cart' => $cart
        ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to get data from cart',
                'error' => $th->getMessage()
            ], 400);
        }
    }
}
