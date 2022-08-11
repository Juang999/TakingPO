<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\MutifStoreRequest;
use App\MutifStoreMaster;
use App\MutifStoreAddress;
use App\Distributor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MutifStoreAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($phone)
    {
        $user = Distributor::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'status' => 'rejected',
                'message' => 'number '.$phone.' not registered'
            ], 300);
        }

        try {
            $stores = MutifStoreMaster::where('distributor_id', $user->id)->with('Distributor')->get();

            foreach ($stores as $store) {
                $store['mutif_store_address'] = MutifStoreAddress::where([
                    ['mutif_store_master_id', '=', $store->id],
                    ['active', '=', 1]
                ])->first();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'success to get data',
                'data' => $stores
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MutifStoreRequest $request, $phone)
    {
        $user = Distributor::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'status' => 'rejected',
                'message' => 'number '.$phone.' not registered'
            ], 300);
        }

        try {
            DB::beginTransaction();

            $MutifStoreMaster = MutifStoreMaster::create([
                'mutif_store_name' => $request->ms_ms_name,
                'mutif_store_code' => $request->ms_code,
                'group_code' => $user->group_code,
                'distributor_id' => $user->id,
                'partner_group_id' => $user->partner_group_id,
                'open_date' => $request->open_date,
                'status' => $request->status,
                'msdp' => $request->msdp
            ]);

            $MutfStoreAddress = MutifStoreAddress::create([
                'mutif_store_master_id' => $MutifStoreMaster->id,
                'address' => $request->address,
                'province' => $request->province,
                'regency' => $request->regency,
                'district' => $request->district,
                'phone_1' => $phone,
                'phone_2' => $request->phone,
                'fax_1' => $request->fax,
                'addr_type' => $request->addr_type,
                'zip' => $request->zip,
                'comment' => $request->comment
            ]);

            activity()->causedBy($user)
                            ->performedOn($MutifStoreMaster)
                            ->withProperties([
                                'attributes' => [
                                    'mutif_store_name' => $MutifStoreMaster->mutif_store_name,
                                    'mutif_store_code' => $MutifStoreMaster->mutif_store_code
                                ]
                            ])->log('created');

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'success to create MS',
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to create MS',
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
                'status' => 'rejected',
                'number' => 'number '.$phone.' not found'
            ], 300);
        }

        try {
            $MutifStore = MutifStoreMaster::find($id);

            return response()->json([
                'status' => 'success',
                'message' => 'success to get data',
                'mutif-store' => $MutifStore
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
    public function update(Request $request, $phone, $id)
    {
        $user = Distributor::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'status' => 'rejected',
                'message' => $phone.' not registered'
            ], 300);
        }

        $MutifStoreMaster = MutifStoreMaster::find($id);
        $MutifStoreAddress = MutifStoreAddress::where('mutif_store_master_id', $MutifStoreMaster->id)
                            ->latest()
                            ->first();

        try {
            DB::beginTransaction();

            $MutifStoreMaster->update([
                'mutif_store_name' => ($request->ms_ms_name) ? $request->ms_ms_name : $MutifStoreMaster->mutif_store_name,
                'mutif_store_code' => ($request->ms_code) ? $request->ms_code : $MutifStoreMaster->mutif_store_code,
                'group_code' => $MutifStoreMaster->group_code,
                'distributor_id' => $MutifStoreMaster->distributor_id,
                'partner_group_id' => $MutifStoreMaster->partner_group_id,
                'open_date' => ($request->open_date) ? $request->open_date : $MutifStoreMaster->open_date,
                'status' => ($request->status) ? $request->status : $MutifStoreMaster->status,
                'msdp' => ($request->msdp) ? $request->msdp : $MutifStoreMaster->msdp,
            ]);

            if ($request->address) {
                $MutifStoreAddress->update([
                    'acitve' => 0
                ]);

                MutifStoreAddress::create([
                    'mutif_store_master_id' => $MutifStoreMaster->id,
                    'address' => $request->address,
                    'province' => $request->province,
                    'regency' => $request->regency,
                    'district' => $request->district,
                    'phone_2' => $request->ms_phone,
                    'fax_1' => $request->ms_fax,
                    'addr_type' => $request->addr_type,
                    'zip' => $request->zip,
                    'comment' => $request->comment,
                    'active' => 1
                ]);
            }

            $newMutifStore = MutifStoreMaster::find($id);

            activity()->causedBy($user)
                            ->performedOn($MutifStoreMaster)
                            ->withProperties([
                                'old' => [
                                    'mutif_store_name' => $MutifStoreMaster->mutif_store_name,
                                    'mutif_store_code' => $MutifStoreMaster->mutif_store_code
                                ],
                                'attributes' => [
                                    'mutif_store_name' => $newMutifStore->mutif_store_name,
                                    'mutif_store_code' => $newMutifStore->mutif_store_code
                                ]
                            ])->log('updated');

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'success to update Mutif Store'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollback();

            return response()->json([
                'status' => 'failed',
                'message' => 'failed to update Mutif Store',
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
    public function destroy($phone, $id)
    {
        $user = Distributor::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'status' => 'rejected',
                'message' => $phone.' not registered'
            ], 300);
        }

        try {
            $MutifStoreMaster = MutifStoreMaster::find($id);

            activity()->causedBy($user)
                            ->performedOn($MutifStoreMaster)
                            ->withProperties([
                                'attributes' => [
                                    'mutif_store_name' => $MutifStoreMaster->mutif_store_name,
                                    'mutif_store_code' => $MutifStoreMaster->mutif_store_code
                                ]
                            ])->log('deleted');

            $MutifStoreMaster->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'success to delete store'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to delete store',
                'error' => $th->getMessage()
            ], 400);
        }
    }
}
