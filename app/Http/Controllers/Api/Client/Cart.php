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

        $carts = TemporaryStorage::where('distributor_id', $user->id)->with('Clothes.BufferProduct.Size')->get();

        foreach ($carts as $cart) {
            if ($cart->Clothes->combo != '-') {
                $cart['Clothes']['combo'] = explode(",", $cart->Clothes->combo);
            }

            if ($cart->Clothes->size_2 != 0) {
                $cart['Clothes']['size_2'] = explode(",", $cart->Clothes->size_2);
                $cart['Clothes']['size_4'] = explode(",", $cart->Clothes->size_4);
                $cart['Clothes']['size_6'] = explode(",", $cart->Clothes->size_6);
                $cart['Clothes']['size_8'] = explode(",", $cart->Clothes->size_8);
                $cart['Clothes']['size_10'] = explode(",", $cart->Clothes->size_10);
                $cart['Clothes']['size_12'] = explode(",", $cart->Clothes->size_12);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'success to get data from cart',
            'cart' => $carts
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
