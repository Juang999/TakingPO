<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Distributor;
use App\Http\Requests\AddressRequest;
use App\PartnerAddress;
use Illuminate\Support\Facades\Redis;

class AddressController extends Controller
{
    public function createAddress(AddressRequest $request, $phone)
    {
        $distributor = Distributor::where('phone', $phone)->first();

        if (!$distributor) {
            return response()->json([
                'status' => 'failed',
                'message' => 'number '.$phone.' not register'
            ], 400);
        }
        try {

        $address = PartnerAddress::create([
            'distributor_id' => $distributor->id,
            'address' => $request->address,
            'district' => $request->district,
            'regency' => $request->regency,
            'province' => $request->province,
            'phone_1' => $request->phone_1,
            // 'comment' => $request->comment
        ]);

            return response()->json([
                'status' => 'success',
                'message' => 'success to register address',
                'data' => $address,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to register address',
                'error' => $th->getMessage()
            ], 400);
        }
    }
}
