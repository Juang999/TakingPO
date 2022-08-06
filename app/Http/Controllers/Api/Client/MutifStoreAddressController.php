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
            $MutifStore = MutifStoreMaster::where('distributor_id', $user->id)->with('MutifStoreAddress')->get();

            return response()->json([
                'status' => 'success',
                'message' => 'success to get data',
                'data' => $MutifStore
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

            return response()->json([
                'status' => 'success',
                'message' => 'success to create MS',
            ], 200);
        } catch (\Throwable $th) {
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
    public function update(Request $request, $id)
    {
        $MutifStoreMaster = MutifStoreMaster::find($id);
        $MutifStoreAddress = MutifStoreAddress::where('mutif_store_master_id', $MutifStoreMaster->id)->first();
        $user_id = Auth::user()->id;

        try {
            DB::beginTransaction();

            $MutifStoreMaster->update([
                'mutif_store_name' => ($request->ms_ms_name) ? $request->ms_ms_name : $MutifStoreMaster->mutif_store_name,
                'mutif_store_code' => ($request->ms_code) ? $request->ms_code : $MutifStoreMaster->mutif_store_code,
                'ms_upd_by' => $user_id,
                'group_code' => $MutifStoreMaster->group_code,
                'distributor_id' => $MutifStoreMaster->distributor_id,
                'partner_group_id' => $MutifStoreMaster->partner_group_id,
                'open_date' => ($request->open_date) ? $request->open_date : $MutifStoreMaster->open_date,
                'status' => ($request->status) ? $request->status : $MutifStoreMaster->status,
                'msdp' => ($request->msdp) ? $request->msdp : $MutifStoreMaster->msdp,
            ]);

            $MutifStoreAddress->update([
                'prtnr_upd_by' => $user_id,
                'address' => ($request->address) ? $request->address : $MutifStoreAddress->address,
                'province' => ($request->province) ? $request->province : $MutifStoreAddress->province,
                'regency' => ($request->regency) ? $request->regency : $MutifStoreAddress->regency,
                'district' => ($request->district) ? $request->district : $MutifStoreAddress->district,
                'phone_2' => ($request->ms_phone) ? $request->ms_phone : $MutifStoreAddress->phone_2,
                'fax_1' => ($request->ms_fax) ? $request->ms_fax : $MutifStoreAddress->fax_1,
                'addr_type' => ($request->addr_type) ? $request->addr_type : $MutifStoreAddress->addr_type,
                'zip' => ($request->zip) ? $request->zip : $MutifStoreAddress->zip,
                'comment' => ($request->comment) ? $request->comment : $MutifStoreAddress->comment
            ]);

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
    public function destroy($id)
    {
        //
    }
}
