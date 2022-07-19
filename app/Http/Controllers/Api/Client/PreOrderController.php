<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Clothes;
use App\Distributor;
use App\Http\Requests\PreOrderRequest;
use App\TemporaryStorage;

class PreOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($phone)
    {
        $user  = Distributor::where('phone', $phone)->with('PartnerGroup')->first();

        if (!$user) {
            return response()->json([
                'status' => 'failed to get data',
                'message' => 'phone '.$phone.' not registered'
            ], 400);
        }

        $clothess = Clothes::orderBy('entity_name')->with('Type', 'Image', 'BufferProduct.Size')->get();

        if ($clothess) {
            foreach ($clothess as $clothes) {
                if ($clothes->combo != '-') {
                    $clothes['combo'] = explode(",", $clothes->combo);
                }
                $clothes['size_2'] = explode(",", $clothes->size_2);
                $clothes['size_4'] = explode(",", $clothes->size_4);
                $clothes['size_6'] = explode(",", $clothes->size_6);
                $clothes['size_8'] = explode(",", $clothes->size_8);
                $clothes['size_10'] = explode(",", $clothes->size_10);
                $clothes['size_12'] = explode(",", $clothes->size_12);
            }
        }
        return response()->json([
        'status' => 'success',
            'message' => 'success get data',
            'discount' => $user->PartnerGroup->discount,
            'data' => $clothess
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PreOrderRequest $request, $phone)
    {
        $user = Distributor::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'number '.$phone.' not registered'
            ], 400);
        }

        try {
            $cart = TemporaryStorage::create([
                'distributor_id' => $user->id,
                'clothes_id' => $request->clothes_id,
                'info' => $request->info,
                'veil' => $request->veil,
                'size_s' => $request->size_s,
                'size_m' => $request->size_m,
                'size_l' => $request->size_l,
                'size_xl' => $request->size_xl,
                'size_xxl' => $request->size_xxl,
                'size_xxxl' => $request->size_xxxl,
                'size_2' => $request->size_2,
                'size_4' => $request->size_4,
                'size_6' => $request->size_6,
                'size_8' => $request->size_8,
                'size_10' => $request->size_10,
                'size_12' => $request->size_12,
                'total' => $request->total
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'success input to cart',
                'data' => $cart
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed input to cart',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($phone, $id)
    {
        $user = Distributor::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'number '.$phone.'not registerd'
            ], 400);
        }

        try {
            $clothes = Clothes::where('id', $id)->with('Type', 'Image', 'BufferProduct')->first();

            if ($clothes->combo != '-') {
                $clothes['combo'] = explode(",", $clothes->combo);
            }
            $clothes['size_2'] = explode(",", $clothes->size_2);
            $clothes['size_4'] = explode(",", $clothes->size_4);
            $clothes['size_6'] = explode(",", $clothes->size_6);
            $clothes['size_8'] = explode(",", $clothes->size_8);
            $clothes['size_10'] = explode(",", $clothes->size_10);
            $clothes['size_12'] = explode(",", $clothes->size_12);

            return response()->json([
                'status' => 'success',
                'message' => 'success to get detail clothes',
                'data' => $clothes
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to get data',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PreOrderRequest $request, $id, $phone)
    {
        $user = Distributor::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'status' => 'rejected',
                'message' => 'number '.$phone.' not registered',
            ], 300);
        }

        try {
            $temporary_storage = TemporaryStorage::find($id);

            $temporary_storage->update([
                'distributor_id' => $user->id,
                'clothes_id' => $request->clothes_id,
                'info' => $request->info,
                'veil' => $request->veil,
                'size_s' => $request->size_s,
                'size_m' => $request->size_m,
                'size_l' => $request->size_l,
                'size_xl' => $request->size_xl,
                'size_xxl' => $request->size_xxl,
                'size_xxxl' => $request->size_xxxl,
                'size_2' => $request->size_2,
                'size_4' => $request->size_4,
                'size_6' => $request->size_6,
                'size_8' => $request->size_8,
                'size_10' => $request->size_10,
                'size_12' => $request->size_12,
                'total' => $request->total
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'success to update cart'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to update cart',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $phone)
    {

        $user = Distributor::where('phone', $phone)->first();
        if (!$user) {
            return response()->json([
                'status' => 'rejected',
                'message' => 'number '.$phone.' not registered',
            ], 300);
        }

        try {
            TemporaryStorage::where('id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'success to delete data'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to delete data',
                'error' => $th->getMessage()
            ], 200);
        }
    }
}
