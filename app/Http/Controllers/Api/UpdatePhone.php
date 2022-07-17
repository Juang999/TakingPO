<?php

namespace App\Http\Controllers\Api;

use App\Distributor;
use App\Http\Controllers\Controller;
use App\MutifStoreMaster;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UpdatePhone extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $phone)
    {
        return response()->json([
            'message' => 'under development'
        ], 400);

        $user = Distributor::where('phone', $phone)->first();

        if ($user->partner_group_id == 1) {
            $validator = Validator::make($request->all(), [
                'new_phone_number' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $user->update('phone', $request->new_phone_number);

            return response()->json([
                'status' => 'success',
                'message' => 'phone updated!',
            ], 200);
        } else if ($user->partner_group_id == 2) {
            $validator = Validator::make($request->all(), [
                'ms_code' => 'required',
                'new_phone_number' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $mutif_stores = MutifStoreMaster::where('distributor_id', $user->id)->get();

            foreach ($mutif_stores as $mutif_store) {
                if ($mutif_store->mutif_store_code == $request->ms_code) {
                    $user->update([
                        'phone' => $request->new_phone_number
                    ]);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'success to update phone number',
            ], 200);
        }
    }
}
