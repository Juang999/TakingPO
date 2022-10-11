<?php

namespace App\Http\Controllers\Api\Client;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{DB, Validator};
use App\Http\Requests\{RegisterRequest, AddressRequest};
use App\{IsActive, Distributor, PartnerGroup, TemporaryStorage, PartnerAddress, Phone};

class ClientController extends Controller
{
    public function login($phone)
    {
        if ($phone == 0 || $phone == NULL) {
            return response()->json([
                'message' => 'please enter your phone number'
            ], 400);
        }

        $phone_number = Phone::where('phone_number', $phone)->first();

        if ($phone_number == NULL) {
            return response()->json([
                'status' => 'failed',
                'pesan' => 'nomor tidak ada'
            ]);
        } elseif ($phone_number->approved == 0) {
            return response()->json([
                'status' => 'waiting',
                'message' => 'menunggu persetujuan dari admin'
            ], 200);
        } elseif ($phone_number->is_active == 0) {
            return response()->json([
                'status' => 'waiting',
                'message' => 'kamu sudah mengganti nomor'
            ], 200);
        }

        $user = Distributor::where('id', $phone_number->distributor_id)
                            ->with('PartnerAddress', 'MutifStoreMaster.MutifStoreAddress')
                            ->first();

        $cart = TemporaryStorage::where('distributor_id', $user->id)->with('Clothes.BufferProduct.Size')->get();

        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'pengguna '.$phone.' tidak terdaftar'
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
                'status' => 'success',
                'message' => 'hello '.$user->name,
                'user' => $user,
                'cart' => $cart,
                'final_data' => []
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

            $existClient = Distributor::where('phone', $request->phone)->first();

            if ($existClient) {
                return response()->json([
                    'status' => 'gagal',
                    'message' => 'nomor sudah teregistrasi'
                ], 300);
            }

            $distributor = Distributor::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'distributor_id' => $request->distributor_id,
                'group_code' => $partner_group->prtnr_code,
                'partner_group_id' => $partner_group->id,
                'level' => 'bronze'
            ]);

            $phone = Phone::create([
                'distributor_id' => $distributor->id,
                'phone_number' => $distributor->phone,
                'is_active' => 1,
                'approved' => 1
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
        try {
            $user = Distributor::where('phone', $phone)->first();

            $existPhone = Phone::where('distributor_id', $request->new_phone_number)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'rejected',
                    'message' => 'user not found!'
                ], 300);
            } elseif ($existPhone) {
                return response()->json([
                    'status' => 'rejected',
                    'message' => 'phone already exist'
                ]);
            }

            $validator = Validator::make($request->all(), [
                'new_phone_number' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            DB::beginTransaction();
                $newPhoneNumber = Phone::create([
                    'distributor_id' => $user->id,
                    'phone_number' => $request->new_phone_number,
                    'is_active' => 0,
                    'approved' => 0
                ]);

                $modelPhone = new Phone();

                activity()->causedBy($user)
                            ->performedOn($modelPhone)
                            ->withProperties([
                                'attributes' => [
                                    'phone_number' => $newPhoneNumber->phone_number
                                ]
                            ])->log('created');

            DB::commit();
            return response()->json([
                'status' => 'berhasil',
                'pesan' => 'menunggu persetujuan dari adamin',
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'gagal update nomor',
                'error' => $th->getMessage(),
                'baris' => $th->getLine(),
                'file' => $th->getFile()
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
        $partner_group = PartnerGroup::where('prtnr_code', '!=', 'DB')->get();

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
