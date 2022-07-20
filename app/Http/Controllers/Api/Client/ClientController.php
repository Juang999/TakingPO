<?php

namespace App\Http\Controllers\Api\Client;

use App\IsActive;
use App\Distributor;
use App\PartnerGroup;
use App\TemporaryStorage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\RegisterRequest;
use App\MutifStoreMaster;
use App\PartnerAddress;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    public function login($phone)
    {
        if ($phone == 0 || $phone == NULL) {
            return response()->json([
                'message' => 'please enter your phone number'
            ], 400);
        }

        $user = Distributor::where('phone', $phone)->with('PartnerAddress', 'MutifStoreMaster.MutifStoreAddress')->first();

        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'user '.$phone.' not registered'
            ], 400);
        }

        $activate = IsActive::find(1);

        if (!$activate || $activate->name == 'NON-ACTIVE') {
            // when the web is active
            return response()->json([
                'status' => 'closed',
                'message' => 'the web is being closed'
            ], 400);
        } elseif ($activate && $activate->name == 'ACTIVE') {
            // when web is being closed
            return response()->json([
                'status' => 'successs',
                'message' => 'hello '.$user->name,
                'account' => $user
            ], 200);
        } elseif ($activate && $activate->name == 'DONE') {
            // when logging into the final session
            try {
                $data = TemporaryStorage::where('distributor_id', $user->id)->with('Clothes')->get();

                return response()->json([
                    'status' => 'success',
                    'message' => 'success to get data',
                    'user' => $user,
                    'final_data' => $data
                ], 200);
            } catch (\Throwable $th) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'failed to get data',
                    'error' => $th->getMessage()
                ], 400);
            }
        }
    }

    public function register(RegisterRequest $request)
    {
        try {
            $partner_group = PartnerGroup::where('id', $request->partner_group_id)->first();

            Distributor::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'distributor_id' => $request->distributor_id,
                'group_code' => $partner_group->prtnr_code,
                'partner_group_id' => $partner_group->id,
                'level' => 'bronze'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'register successfully',
            ], 200);

        } catch (\Throwable $th) {

            return response()->json([
                'status' => 'failed',
                'message' => 'failed to register',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function UpdatePhone(Request $request, $phone)
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
        } else if ($user->partner_group_id == 2 || $user->partner_group_id == 3) {
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

    public function distributor()
    {
        $distributor = Distributor::where('partner_group_id', 1)->get();

        return response()->json([
            'status' => 'success',
            'message' => 'success to get data distributor',
            'data' => $distributor
        ], 200);
    }

    public function PartnerGroup()
    {
        $partner_group = PartnerGroup::get();

        return response()->json([
            'status' => 'success',
            'message' => 'success to get partner group',
            'data' => $partner_group
        ], 200);
    }

    public function CreateAddress(AddressRequest $request, $phone)
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
            // 'phone_1' => $request->phone_1,
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
